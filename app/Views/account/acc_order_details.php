

<section class="account-order-details-page">

    <div class="container">

        <!-- breadcrumb -->
        <div class="search-breadcrumb">

            <a href="<?= base_url(); ?>">
                Home
            </a>
            <span>/</span>
            <span>
                My Account
            </span>
            <span>/</span>
            <span>
                Orders
            </span>
            <span>/</span>
            <strong>
                Order #<?= $order['id']; ?>
            </strong>

        </div>


        <!-- order card -->
        <div class="order-details-card">

            <!-- Order Header -->
            <div class="order-details-header">
                <div class="order-details-title-row">
                    <button
                        type="button"
                        class="order-back-btn"
                        onclick="backToOrders()"
                        aria-label="Back to orders"
                        title="Back to orders"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>

                    <h1>
                        Order #<?= $order['id']; ?>
                    </h1>

                </div>

                <div class="order-meta">
                    <div class="order-meta-item">
                        <strong>Order Date</strong>
                        <span>
                            <?= date(
                                'd M Y, h:i A',
                                strtotime($order['order_date'])
                            ); ?>
                        </span>
                    </div>

                    <div class="order-meta-divider"></div>

                    <div class="order-meta-item">
                        <strong>Order Status</strong>
                        <span
                            class="order-status-badge
                            <?= strtolower($order['order_status']) == 'cancelled'
                                ? 'status-cancelled'
                                : 'status-active'; ?>"
                        >
                            <i class="fa fa-circle"></i>
                            <?= esc($order['order_status']); ?>
                        </span>
                    </div>

                    <button
                            type="button"
                            class="account-order-invoice-btn"
                            onclick="downloadInvoice(<?= (int)$order['id']; ?>)"
                        >
                            <i class="fa-solid fa-download"></i>
                            Download Invoice
                        </button>
                </div>

            </div>

            <!-- address section -->
            <div class="order-addresses">

                <!-- Billing -->
                <div class="order-address">
                    <h3>
                        Billing Address
                    </h3>
                    <div class="address-content">
                        <?php if (!empty($billingAddress)) { ?>
                            <?php if (
                                !empty($billingAddress['first_name']) ||
                                !empty($billingAddress['last_name'])
                            ) { ?>
                                <div>
                                    <?= esc(trim(
                                        ($billingAddress['first_name'] ?? '') .
                                        ' ' .
                                        ($billingAddress['last_name'] ?? '')
                                    )); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['company_name'])) { ?>
                                <div>
                                    <?= esc($billingAddress['company_name']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['address_line1'])) { ?>
                                <div>
                                    <?= esc($billingAddress['address_line1']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['address_line2'])) { ?>
                                <div>
                                    <?= esc($billingAddress['address_line2']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['city'])) { ?>
                                <div>
                                    <?= esc($billingAddress['city']); ?>
                                    <?php if (!empty($billingAddress['state'])) { ?>
                                        , <?= esc($billingAddress['state']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['country'])) { ?>
                                <div>
                                    <?= esc($billingAddress['country']); ?>
                                    <?php if (!empty($billingAddress['zip'])) { ?>
                                        <?= esc($billingAddress['zip']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($billingAddress['contact_no'])) { ?>
                                <div>
                                    <?= esc($billingAddress['contact_no']); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <!-- Divider -->
                <div class="order-address-divider"></div>

                <!-- Shipping -->
                <div class="order-address">
                    <h3>
                        Shipping Address
                    </h3>

                    <div class="address-content">
                        <?php if (!empty($shippingAddress)) { ?>
                            <?php if (
                                !empty($shippingAddress['first_name']) ||
                                !empty($shippingAddress['last_name'])
                            ) { ?>
                                <div>
                                    <?= esc(trim(
                                        ($shippingAddress['first_name'] ?? '') .
                                        ' ' .
                                        ($shippingAddress['last_name'] ?? '')
                                    )); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['company_name'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['company_name']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['address_line1'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['address_line1']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['address_line2'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['address_line2']); ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['city'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['city']); ?>

                                    <?php if (!empty($shippingAddress['state'])) { ?>
                                        , <?= esc($shippingAddress['state']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['country'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['country']); ?>

                                    <?php if (!empty($shippingAddress['zip'])) { ?>
                                        <?= esc($shippingAddress['zip']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($shippingAddress['contact_no'])) { ?>
                                <div>
                                    <?= esc($shippingAddress['contact_no']); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- products -->
            <div class="order-products-box">
                <div class="order-products-header">
                    <div>
                        Item
                    </div>
                    <div>
                        Price
                    </div>
                    <div>
                        Qty
                    </div>
                    <div>
                        Total
                    </div>
                </div>

                <?php foreach ($products as $product) { ?>
                    <div class="order-product-row">

                        <!-- Product -->
                        <div class="order-product-info">
                            <div class="order-product-image">
                                <img
                                    src="<?= !empty($product['main_image'])
                                        ? $product['main_image']
                                        : base_url('Images/icons/ic_product.png'); ?>"
                                    onerror="this.src='<?= base_url('Images/icons/ic_product.png'); ?>'"
                                    alt="<?= esc($product['product_name']); ?>"
                                >
                            </div>

                            <div class="order-product-name">
                                <?= esc($product['product_name']); ?>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="order-product-price">
                            ₹<?= number_format(
                                (float)$product['product_price'],
                                0
                            ); ?>
                        </div>

                        <!-- Quantity -->
                        <div class="order-product-qty">
                           <?= (int)$product['product_qty']; ?>
                        </div>

                        <!-- Total -->
                        <div class="order-product-total">
                            ₹<?= number_format(
                                (float)$product['line_total'],
                                0
                            ); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Summary -->
            <div class="order-summary-box">
                <h2>
                    Order Summary
                </h2>

                <div class="order-summary-row">
                    <span>
                        Subtotal
                    </span>
                    <span>
                        ₹<?= number_format(
                            (float)$order['sub_total_amount'],
                            0
                        ); ?>
                    </span>
                </div>

                <div class="order-summary-row">
                    <span>
                        Shipping Fee
                    </span>
                    <span>
                        ₹<?= number_format(
                            (float)$order['shipping_charged_amount'],
                            0
                        ); ?>
                    </span>
                </div>

                <?php
                $discountAmount = (float)$order['sub_total_amount'] - (float)$order['discounted_total_amount'];

                if ($discountAmount > 0) { ?>
                    <div class="order-summary-row summary-discount">
                        <span>
                            Discount
                        </span>
                        <span>
                            - ₹<?= number_format( (float)$order['discounted_total_amount'],  0 ); ?>
                        </span>

                    </div>

                <?php } ?>

                <?php if ( isset($order['store_credit']) && (float)$order['store_credit'] > 0  ) { ?>

                    <div class="order-summary-row summary-discount">
                        <span>
                            Store Credit Used
                        </span>
                        <span>
                            - ₹<?= number_format(
                                (float)$order['store_credit'],
                                0
                            ); ?>
                        </span>
                    </div>
                <?php } ?>

                <?php if ( isset($order['cod_charges']) &&  (float)$order['cod_charges'] > 0  ) { ?>

                    <div class="order-summary-row">
                        <span>
                            COD Charges
                        </span>
                        <span>
                            ₹<?= number_format(  (float)$order['cod_charges'],  0  ); ?>
                        </span>
                    </div>

                <?php } ?>

                <div class="order-summary-row">

                    <span>
                        GST
                    </span>
                    <span>
                        ₹<?= number_format(  (float)$order['total_taxed_amount'],   0   ); ?>
                    </span>
                </div>

                <div class="order-summary-total">
                    <strong>
                        Total Amount
                    </strong>
                    <strong>
                        ₹<?= number_format((float)$order['grand_total_amount'],  0 ); ?>
                    </strong>

                </div>

            </div>

            <!-- payment section -->
            <div class="order-payment-box">
                <div class="order-info-icon">
                    <i class="fa fa-credit-card"></i>
                </div>

                <div class="order-info-title">
                    Payment Method
                </div>

                <div class="order-info-value">
                    <?= esc($order['payment_label']); ?>
                </div>

            </div>

            <!-- shipping section -->
            <?php
                $orderStatus = strtolower(trim($order['order_status']));
                $shippingExpandable = in_array( $orderStatus, ['shipped', 'returned', 'delivered']);
            ?>

            <div class="order-shipping-box <?= $shippingExpandable ? 'shipping-expandable' : 'shipping-locked'; ?>" >

                <!-- Shipping Header -->
                <div
                    class="order-shipping-header"
                    <?= $shippingExpandable  ? 'onclick="toggleShippingDetails()"'  : ''; ?>   >

                    <div class="order-info-icon">
                        <i class="fa fa-truck"></i>
                    </div>

                    <div class="order-shipping-header-content">
                        <div class="order-info-title">
                            Shipping Details
                        </div>
                        <?php if (!$shippingExpandable) { ?>

                            <div class="order-shipping-status">
                                No shipping information is currently available.
                            </div>
                        <?php } ?>
                    </div>

                    <?php if ($shippingExpandable) { ?>
                        <div class="order-shipping-arrow">
                            <i
                                id="shippingArrow"
                                class="fa fa-chevron-down"
                            ></i>
                        </div>
                    <?php } ?>
                </div>

                <!-- Shipping Details - Initially Hidden -->
                <?php if ($shippingExpandable) { ?>
                    <div
                        id="shippingDetailsContent"
                        class="shipping-details-content"  >

                        <div class="shipping-detail-row">
                            <span>
                                Courier Partner
                            </span>
                            <strong>
                                <?= !empty($order['tracking_id'])
                                    ? 'Courier Partner'
                                    : '—'; ?>
                            </strong>
                        </div>

                        <div class="shipping-detail-row">
                            <span>
                                Tracking Number
                            </span>
                            <strong>
                                <?= !empty($order['tracking_id'])
                                    ? esc($order['tracking_id'])
                                    : '—'; ?>
                            </strong>
                        </div>

                        <?php if (!empty($order['tracking_id'])) { ?>
                            <button
                                type="button"
                                class="order-track-btn" >
                                Track Order
                            </button>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <!-- order notes -->
               <?php if (!empty($order['order_comment'])): ?>
                   <div class="order-notes-section">
                       <div class="order-notes-header">
                           <i class="fa-regular fa-note-sticky"></i>
                           <strong>Order Notes</strong>
                       </div>

                       <div class="order-notes-content">
                           <?= nl2br(htmlspecialchars($order['order_comment'])); ?>
                       </div>
                   </div>
               <?php endif; ?>
            
            <!-- order chat -->
            <div class="order-chat-box">
                <div
                    class="order-chat-header"
                    onclick="toggleOrderChat()"  >

                    <div class="order-chat-header-content">
                        <div class="order-info-title">
                            <i class="fa-solid fa-comments order-chat-title-icon"></i>
                            Order Chat
                        </div>
                    </div>

                    <div
                        class="order-chat-arrow"
                        id="order-chat-arrow" >
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>

                </div>

                <div
                    class="order-chat-content"
                    id="order-chat-content">
                    <?php if (!empty($order_messages)): ?>
                        <div
                            class="order-chat-messages"
                            id="orderChatMessages"  >
                            <?php foreach ($order_messages as $message): ?>
                                <?php
                                    $isSupport = ((int)$message['sender_id'] === 0);
                                ?>
                                <div class="order-chat-message <?= $isSupport ? 'support-message' : 'user-message'; ?>">
                                    <div class="order-chat-sender">
                                        <?= $isSupport ? 'Support:' : 'You:'; ?>
                                    </div>
                                    
                                    <div class="order-chat-message-bubble">
                                        <div class="order-chat-message-text">
                                            <?= nl2br(htmlspecialchars($message['order_message'])); ?>
                                        </div>

                                        <div class="order-chat-message-date">
                                            <?= date('d M Y', strtotime($message['message_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div
                            class="order-chat-messages"
                            id="orderChatMessages"
                        >
                            <div class="order-chat-empty">
                                No messages yet.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- New Message -->
                    <div class="order-chat-compose">
                        <textarea
                            id="orderChatMessage"
                            class="order-chat-input"
                            placeholder="Type your message..."
                            maxlength="2000"
                            rows="3"
                        ></textarea>

                        <button
                            type="button"
                            class="order-chat-send-btn"
                            onclick="sendOrderMessage(<?= (int)$order['id']; ?>)" >
                            <i class="fa-solid fa-paper-plane"></i>
                            Send
                        </button>
                    </div>

                </div>
                
            </div>

            <!-- cancel order -->
            <?php $statusLower = strtolower(trim($order['order_status'])); ?>
            <?php if ( !in_array( $statusLower, ['cancelled', 'delivered', 'returned','fulfilled','shipping soon','shipped'] )  ) { ?>
                <button
                    type="button"
                    class="order-cancel-btn"
                    onclick="cancelOrder(<?= (int)$order['id']; ?>)">
                    <i class="fa fa-trash"></i>
                    Cancel Order
                </button>
            <?php } ?>

        </div>

    </div>

</section>

<script>
    const invoiceData = <?= json_encode([
        'order' => $order,
        'invoice_id' => '',
        'invoice_date' => '',
        'billing_address' => $billingAddress,
        'shipping_address' => $shippingAddress,
        'products' => $products
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        

    const cancelOrderEmailData = <?= json_encode([
        'order'            => $order,
        'billing_address' => $billingAddress,
        'shipping_address'=> $shippingAddress,
        'products'        => $products
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

</script>