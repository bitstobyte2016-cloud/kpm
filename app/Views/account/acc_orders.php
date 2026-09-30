<div class="account-orders">

    <style>

        /* =========================================
           ORDERS PAGE
        ========================================= */

        .account-orders {
            width: 100%;
        }


        /* HEADER */

        .account-orders-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;

            margin-bottom: 28px;
        }

        .account-orders-header h2 {
            margin: 0 0 6px;

            font-size: 24px;
            font-weight: 700;

            color: #111;
        }

        .account-orders-header p {
            margin: 0;

            font-size: 13px;
            color: #777;
        }


        /* FILTER */

        .account-orders-filter {
            position: relative;
        }

        .account-orders-filter select {
            height: 46px;
            min-width: 140px;

            padding: 0 38px 0 16px;

            border: 1px solid #ddd;
            border-radius: 10px;

            background: #fff;

            color: #555;

            font-size: 13px;
            font-weight: 600;

            cursor: pointer;

            outline: none;
        }


        /* ORDER LIST */

        .account-orders-list {
            display: flex;
            flex-direction: column;

            gap: 12px;
        }


        /* ORDER CARD */

        .account-order-card {
            display: grid;

            grid-template-columns:
                125px
                minmax(220px, 1.15fr)
                minmax(150px, 0.9fr)
                150px;

            align-items: center;

            min-height: 145px;

            padding: 18px 20px;

            background: #fff;

            border: 1px solid #ececf2;
            border-radius: 12px;

            box-sizing: border-box;
        }


        /* PRODUCT IMAGE */

        .account-order-image {
            width: 105px;
            height: 105px;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;
        }

        .account-order-image img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;
        }


        /* ORDER INFORMATION */

        .account-order-info {
            padding: 0 20px;

            border-left: 1px solid #eeeeee;
        }

        .account-order-id {
            margin-bottom: 9px;

            font-size: 17px;
            line-height: 1.2;

            font-weight: 700;

            color: #111;
        }

        .account-order-date,
        .account-order-payment {
            display: flex;
            align-items: center;

            gap: 7px;

            margin-bottom: 7px;

            font-size: 12px;
            color: #555;
        }

        .account-order-date i,
        .account-order-payment i,
        .account-order-items i {
            width: 14px;

            color: #7650d8;
        }

        .account-order-items {
            display: flex;
            align-items: center;

            gap: 7px;

            margin-top: 13px;

            font-size: 12px;
            color: #555;
        }

        .account-order-total {
            margin-left: 12px;

            font-size: 13px;
            font-weight: 700;

            color: #5425c8;
        }


        /* STATUS / DELIVERY */

        .account-order-status-area {
            min-height: 80px;

            padding: 0 20px;

            border-left: 1px solid #eeeeee;
        }


        /* STATUS BADGE */

        .account-order-status {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            padding: 6px 15px;

            border-radius: 8px;

            font-size: 11px;
            font-weight: 600;

            margin-bottom: 14px;
        }

        .account-order-status.processing,
        .account-order-status.fulfilled,
        .account-order-status.shipped,
        .account-order-status.shipping-soon {
            background: #f0e8ff;

            color: #7441d3;
        }

        .account-order-status.delivered {
            background: #e5f8ec;

            color: #178346;
        }

        .account-order-status.cancelled {
            background: #ffe9ec;

            color: #d9364f;
        }

        .account-order-status.returned {
            background: #fff0df;

            color: #c46b13;
        }


        /* DELIVERY */

        .account-order-delivery-label {
            margin-bottom: 5px;

            font-size: 11px;
            color: #555;
        }

        .account-order-delivery-date {
            font-size: 12px;
            font-weight: 500;

            color: #222;
        }


        /* ACTIONS */

        .account-order-actions {
            display: flex;
            flex-direction: column;

            align-items: center;

            gap: 15px;

            padding-left: 20px;
        }

        .account-order-view-btn {
            width: 112px;
            height: 36px;

            border: none;
            border-radius: 6px;

            background: var(--theme-purple);

            color: #fff;

            font-size: 12px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .account-order-view-btn:hover {
            opacity: 0.9;
        }



        /* EMPTY STATE */

        .account-orders-empty {
            padding: 70px 20px;

            text-align: center;

            border: 1px solid #ececf2;
            border-radius: 12px;

            background: #fff;
        }

        .account-orders-empty i {
            font-size: 38px;

            color: #b28bea;

            margin-bottom: 15px;
        }

        .account-orders-empty h3 {
            margin: 0 0 7px;

            font-size: 18px;
            font-weight: 700;
        }

        .account-orders-empty p {
            margin: 0;

            font-size: 13px;

            color: #777;
        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media(max-width: 1100px) {

            .account-order-card {
                grid-template-columns:
                    100px
                    minmax(180px, 1fr)
                    130px;
            }

            .account-order-actions {
                grid-column: 3;
                grid-row: 1;
            }

            .account-order-status-area {
                grid-column: 2;
                grid-row: 2;

                margin-top: 10px;

                border-left: none;
            }

        }


        @media(max-width: 700px) {

            .account-orders-header {
                flex-direction: column;

                gap: 15px;
            }

            .account-order-card {
                display: flex;
                flex-direction: column;

                align-items: stretch;

                gap: 15px;

                padding: 18px;
            }

            .account-order-image {
                margin: 0 auto;
            }

            .account-order-info,
            .account-order-status-area,
            .account-order-actions {
                border-left: none;

                padding: 0;
            }

            .account-order-actions {
                align-items: stretch;
            }

            .account-order-view-btn {
                width: 100%;
            }

        }

    </style>


    <?php

        $perPage = 5;
        $totalOrders = count($orders);
        $totalPages = ceil(
            $totalOrders / $perPage
        );
    ?>


    <!-- HEADER -->

    <div class="account-orders-header">

        <div>

            <h2>
                My Orders
            </h2>

            <p>
                Track, view and manage all your orders.
            </p>

        </div>


        <div class="account-orders-filter">

            <select
                id="orderStatusFilter"
                aria-label="Filter orders"
            >

                <option value="all">
                    All Orders
                </option>

                <option value="processing">
                    Processing
                </option>

                <option value="fulfilled">
                    Fulfilled
                </option>

                <option value="shipping-soon">
                    Shipping Soon
                </option>

                <option value="shipped">
                    Shipped
                </option>

                <option value="delivered">
                    Delivered
                </option>

                <option value="returned">
                    Returned
                </option>

                <option value="cancelled">
                    Cancelled
                </option>

            </select>

        </div>

    </div>


    <!-- ORDERS -->

    <div class="account-orders-list">

        <?php if(!empty($orders)): ?>


            <?php foreach($orders as $order): ?>

                <?php

                    $orderDate = new DateTime(
                        $order['order_date']
                    );


                    $deliveryStart = clone $orderDate;

                    $deliveryStart->modify(
                        '+1 month'
                    );


                    $deliveryEnd = clone $deliveryStart;

                    $deliveryEnd->modify(
                        '+4 days'
                    );


                    $status = strtolower(
                        trim(
                            $order['order_status'] ?? ''
                        )
                    );


                    $statusClass = strtolower(
                        str_replace(
                            ' ',
                            '-',
                            $status
                        )
                    );


                    $paymentType = strtolower(
                        trim(
                            $order['payment_type'] ?? ''
                        )
                    );


                    if(
                        $paymentType === 'cod' ||
                        $paymentType === 'cash on delivery'
                    ){

                        $paymentLabel =
                            'Cash on Delivery';

                    } else {

                        $paymentLabel =
                            'Pre-paid';

                    }

                ?>


                <article
                    class="account-order-card order-item"
                    data-order-status="<?= esc($status); ?>"
                >


                    <!-- PRODUCT IMAGE -->

                    <div class="account-order-image">

                        <?php if(!empty($order['main_image'])): ?>

                            <img
                                src="<?= esc($order['main_image']); ?>"
                                alt="Product from Order #<?= esc($order['id']); ?>"
                            >

                        <?php else: ?>

                            <i class="fa-solid fa-box-open"></i>

                        <?php endif; ?>

                    </div>


                    <!-- ORDER DETAILS -->

                    <div class="account-order-info">

                        <div class="account-order-id">

                            ORDER ID #
                            <?= esc($order['id']); ?>

                        </div>


                        <div class="account-order-date">

                            <i class="fa-regular fa-calendar"></i>

                            <span>

                                Placed on
                                <?= $orderDate->format('d M Y'); ?>

                            </span>

                        </div>


                        <div class="account-order-payment">

                            <i class="fa-solid fa-credit-card"></i>

                            <span>

                                <?= esc($paymentLabel); ?>

                            </span>

                        </div>


                        <div class="account-order-items">

                            <i class="fa-regular fa-file"></i>

                            <span>

                                <?= (int)$order['item_count']; ?>

                                <?= (
                                    (int)$order['item_count'] === 1
                                )
                                    ? 'Item'
                                    : 'Items';
                                ?>

                            </span>


                            <span class="account-order-total">

                                ₹
                                <?= number_format(
                                    (float)$order['grand_total_amount'],
                                    0
                                ); ?>

                            </span>

                        </div>

                    </div>


                    <!-- STATUS / DELIVERY -->

                    <div class="account-order-status-area">

                        <div
                            class="
                                account-order-status
                                <?= esc($statusClass); ?>
                            "
                        >

                            <?= esc(
                                $order['order_status']
                            ); ?>

                        </div>


                        <?php if($status === 'cancelled'): ?>

                            <div class="account-order-delivery-label">

                                Cancelled on

                            </div>


                            <div class="account-order-delivery-date">

                                <?= $orderDate->format('d M Y'); ?>

                            </div>


                        <?php else: ?>

                            <div class="account-order-delivery-label">

                                Estimated Delivery

                            </div>


                            <div class="account-order-delivery-date">

                                <?= $deliveryStart->format('d M Y'); ?>

                                -

                                <?= $deliveryEnd->format('d M Y'); ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- ACTIONS -->

                    <div class="account-order-actions">

                        <button
                            type="button"
                            class="account-order-view-btn"
                            onclick="viewOrder(<?= (int)$order['id']; ?>)"
                        >

                            View Details

                        </button>

                    </div>


                </article>


            <?php endforeach; ?>


        <?php else: ?>


            <div class="account-orders-empty">

                <i class="fa-solid fa-box-open"></i>

                <h3>
                    No Orders Yet
                </h3>

                <p>
                    You haven't placed any orders yet.
                </p>

            </div>


        <?php endif; ?>

    </div>


    <!-- PAGINATION -->
    <?php if($totalPages > 1): ?>
        <div class="orders-pagination">
            <button
                id="orderPrev"
                class="page-btn"
                type="button"
                aria-label="Previous page"
            >
                <i class="fa fa-chevron-left"></i>
            </button>

            <?php for($i = 1; $i <= $totalPages; $i++){ ?>

                <button
                    class="page-btn order-page-btn <?= ($i == 1) ? 'active' : ''; ?>"
                    type="button"
                    data-page="<?= $i; ?>"
                    aria-label="Go to page <?= $i; ?>"
                >
                    <?= $i; ?>
                </button>

            <?php } ?>


            <button
                id="orderNext"
                class="page-btn"
                type="button"
                aria-label="Next page"
            >
                <i class="fa fa-chevron-right"></i>
            </button>

        </div>

    <?php endif; ?>

</div>
