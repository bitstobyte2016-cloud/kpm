<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Models\BrevoEmail;

class ProcessBackInStockQueue extends BaseCommand
{
    protected $group       = 'Notifications';
    protected $name        = 'notifications:back-in-stock';
    protected $description = 'Process back-in-stock email notifications.';

    private const MAX_ATTEMPTS = 5;
    private const BATCH_SIZE = 10;

    public function run(array $params)
    {
        $db = Database::connect();
        $emailModel = new BrevoEmail();

        $queueTable = $db->table('Back_in_stock_queue');

        $jobs = $queueTable
            ->where('status', 'pending')
            ->where('attempts <', self::MAX_ATTEMPTS)
            ->orderBy('id', 'ASC')
            ->limit(self::BATCH_SIZE)
            ->get()
            ->getResultArray();

        if (empty($jobs)) {
            CLI::write('No pending back-in-stock notifications.', 'yellow');
            return;
        }

        foreach ($jobs as $job) {
            $queueId = (int) $job['id'];
            $productId = $job['product_id'];

            // Claim this job so another cron process cannot claim it too.
            $db->table('Back_in_stock_queue')
                ->where('id', $queueId)
                ->where('status', 'pending')
                ->update(['status' => 'processing']);

            if ($db->affectedRows() !== 1) {
                continue;
            }

            CLI::write("Processing product: {$productId}");

            try {
                // Fetch the product and its current supplier quantity.
                $product = $db->table('Products p')
                    ->select(
                        'p.id, p.main_image, pd.product_name, pp.price, ps.product_qty'
                    )
                    ->join(
                        'Product_description pd',
                        'pd.product_id = p.id',
                        'left'
                    )
                    ->join(
                        'Product_price pp',
                        'pp.product_id = p.id',
                        'left'
                    )
                    ->join(
                        'Products_stock ps',
                        'ps.product_id = p.id',
                        'left'
                    )
                    ->where('p.id', $productId)
                    ->where('p.status', 'Y')
                    ->get()
                    ->getRowArray();

                // Do not notify customers if the product is no longer available.
                if (
                    empty($product) ||
                    (int) ($product['product_qty'] ?? 0) <= 0
                ) {
                    $db->table('Back_in_stock_queue')
                        ->where('id', $queueId)
                        ->update([
                            'status' => 'completed',
                            'processed_at' => date('Y-m-d H:i:s')
                        ]);

                    CLI::write(
                        "Product {$productId} is unavailable; notification skipped.",
                        'yellow'
                    );
                    continue;
                }

                $subscribers = $db->table('Notify_subscribers')
                    ->select('id, cust_email')
                    ->where('product_id', $productId)
                    ->where('status', 'opt_in')
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->getResultArray();

                $hadFailure = false;

                foreach ($subscribers as $subscriber) {
                    try {
                        $result = $emailModel->sendBackInStockEmail(
                            $subscriber['cust_email'],
                            $product
                        );
                    } catch (\Throwable $e) {
                        $result = ['success' => false];
                        log_message(
                            'error',
                            'Back-in-stock email exception for queue ' .
                            $queueId . ': ' . $e->getMessage()
                        );
                    }

                    if (!empty($result['success'])) {
                        // Delete only this opted-in subscription after success.
                        $db->table('Notify_subscribers')
                            ->where('id', (int) $subscriber['id'])
                            ->where('product_id', $productId)
                            ->where('status', 'opt_in')
                            ->delete();

                        CLI::write(
                            'Email accepted by Brevo: ' .
                            $subscriber['cust_email'],
                            'green'
                        );
                    } else {
                        $hadFailure = true;

                        log_message(
                            'error',
                            'Back-in-stock email failed for queue ' .
                            $queueId . ', subscriber ID ' .
                            $subscriber['id'] . ': ' .
                            json_encode($result)
                        );

                        CLI::write(
                            'Email failed; subscription retained: ' .
                            $subscriber['cust_email'],
                            'red'
                        );
                    }
                }

                if (!$hadFailure) {
                    $db->table('Back_in_stock_queue')
                        ->where('id', $queueId)
                        ->update([
                            'status' => 'completed',
                            'processed_at' => date('Y-m-d H:i:s')
                        ]);

                    CLI::write(
                        "Queue {$queueId} completed.",
                        'green'
                    );
                } else {
                    $newAttempts = (int) $job['attempts'] + 1;

                    $newStatus = $newAttempts >= self::MAX_ATTEMPTS
                        ? 'failed'
                        : 'pending';

                    $db->table('Back_in_stock_queue')
                        ->where('id', $queueId)
                        ->update([
                            'status' => $newStatus,
                            'attempts' => $newAttempts
                        ]);

                    CLI::write(
                        "Queue {$queueId}: {$newStatus}; attempt {$newAttempts}.",
                        'yellow'
                    );
                }
            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'Back-in-stock queue processing error for queue ' .
                    $queueId . ': ' . $e->getMessage()
                );

                $newAttempts = (int) $job['attempts'] + 1;
                $newStatus = $newAttempts >= self::MAX_ATTEMPTS
                    ? 'failed'
                    : 'pending';

                $db->table('Back_in_stock_queue')
                    ->where('id', $queueId)
                    ->where('status', 'processing')
                    ->update([
                        'status' => $newStatus,
                        'attempts' => $newAttempts
                    ]);

                CLI::error(
                    "Queue {$queueId} encountered an error."
                );
            }
        }
    }
}
