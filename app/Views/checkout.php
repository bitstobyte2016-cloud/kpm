
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="checkout-page">

    <div class="checkout-container">

        <h1 class="checkout-page-title">
            <i class="fa-solid fa-bag-shopping"></i>
            Checkout
        </h1>

        <div class="checkout-layout">

            <!-- LEFT COLUMN -->
            <div class="checkout-left">
                <!-- ACCOUNT INFORMATION -->
                <section class="checkout-card">

                    <div class="checkout-card-heading">
                        <i class="fa-regular fa-user"></i>
                        <h2>Account Information</h2>
                    </div>

                    <div class="checkout-info-panel">
                        <strong>
                            <?= esc(trim(
                                ($customer['first_name'] ?? '') . ' ' .
                                ($customer['last_name'] ?? '')
                            )); ?>
                        </strong>

                        <span>
                            <i class="fa-regular fa-envelope"></i>
                            <?= esc($customer['email'] ?? ''); ?>
                        </span>
                    </div>

                </section>
                
                <!-- SHIPPING ADDRESS -->
                <div class="shipping-address-list">
                    <section class="checkout-card">
                        <div class="checkout-card-heading">
                            <i class="fa-solid fa-truck-fast"></i>
                            <h2>Shipping Address</h2>

                            <button
                                type="button"
                                class="checkout-text-button"
                                id="addCheckoutAddress"
                            >
                                <i class="fa-solid fa-plus"></i>
                                Add New Address
                            </button>
                        </div>

                        <?php if (empty($addresses)): ?>
                            <p class="checkout-no-address">
                                You haven't saved any addresses yet.
                                Please add an address to continue.
                            </p>
                        <?php else: ?>
                            <div class="checkout-address-list">
                                <?php foreach ($addresses as $address): ?>
                                    <label class="checkout-address-option
                                        <?= $address['is_default'] === 'Y' ? 'selected' : ''; ?>">
                                        <input
                                            type="radio"
                                            name="shipping_address"
                                            value="<?= esc($address['id']); ?>"
                                            data-pincode="<?= esc($address['zip']); ?>"
                                            data-state="<?= esc($address['state']); ?>"
                                            data-city="<?= esc($address['city']); ?>"
                                            <?= $address['is_default'] === 'Y' ? 'checked' : ''; ?>
                                        >

                                        <div class="checkout-address-content">
                                            <?php if ($address['is_default'] === 'Y'): ?>
                                                <span class="checkout-default-tag">
                                                    Default
                                                </span>
                                            <?php endif; ?>

                                            <strong>
                                                <?= esc(trim(
                                                    $address['first_name'] . ' ' .
                                                    $address['last_name']
                                                )); ?>
                                            </strong>

                                            <?php if (!empty($address['company_name'])): ?>
                                                <span class="checkout-default-tag">
                                                        Company
                                                    </span>
                                                <span><?= esc($address['company_name']); ?></span>
                                                <span><?= esc($address['company_gstin']); ?></span>
                                            <?php endif; ?>

                                            <span><?= esc($address['address_line1']); ?></span>

                                            <?php if (!empty($address['address_line2'])): ?>
                                                <span><?= esc($address['address_line2']); ?></span>
                                            <?php endif; ?>

                                            <span>
                                                <?= esc($address['city']); ?>,
                                                <?= esc($address['state']); ?> -
                                                <?= esc($address['zip']); ?>
                                            </span>

                                            <span><?= esc($address['country']); ?></span>

                                            <span>
                                                <i class="fa-solid fa-phone"></i>
                                                <?= esc($address['contact_no']); ?>
                                            </span>

                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

                <!-- BILLING ADDRESS -->
                <div class="billing-address-list">
                    <section class="checkout-card">
                        <div class="checkout-card-heading">
                            <i class="fa-regular fa-file-lines"></i>
                            <h2>Billing Address</h2>

                            <label class="checkout-same-address">
                                <input type="checkbox"
                                       id="sameBillingAddress"
                                       checked>
                                Same as shipping address
                            </label>
                        </div>

                        <div class="checkout-address-list"
                             id="billingAddressList"
                             style="display:none;">
                            <?php foreach ($addresses as $address): ?>
                                <label class="checkout-address-option
                                    <?= $address['is_default'] === 'Y' ? 'selected' : ''; ?>">
                                    <input
                                            type="radio"
                                            name="billing_address"
                                            value="<?= esc($address['id']); ?>"
                                            data-pincode="<?= esc($address['zip']); ?>"
                                            data-state="<?= esc($address['state']); ?>"
                                            data-city="<?= esc($address['city']); ?>"
                                            <?= $address['is_default'] === 'Y' ? 'checked' : ''; ?>
                                        >

                                    <div class="checkout-address-content">
                                        <?php if ($address['is_default'] === 'Y'): ?>
                                            <span class="checkout-default-tag">
                                                Default
                                            </span>
                                        <?php endif; ?>

                                        <strong>
                                            <?= esc(trim(
                                                $address['first_name'] . ' ' .
                                                $address['last_name']
                                            )); ?>
                                        </strong>

                                        <?php if (!empty($address['company_name'])): ?>
                                                <span class="checkout-default-tag">
                                                        Company
                                                    </span>
                                                <span><?= esc($address['company_name']); ?></span>
                                                <span><?= esc($address['company_gstin']); ?></span>
                                        <?php endif; ?>

                                        <span><?= esc($address['address_line1']); ?></span>

                                        <?php if (!empty($address['address_line2'])): ?>
                                            <span><?= esc($address['address_line2']); ?></span>
                                        <?php endif; ?>

                                        <span>
                                            <?= esc($address['city']); ?>,
                                            <?= esc($address['state']); ?> -
                                            <?= esc($address['zip']); ?>
                                        </span>

                                        <span><?= esc($address['country']); ?></span>

                                        <span>
                                            <i class="fa-solid fa-phone"></i>
                                            <?= esc($address['contact_no']); ?>
                                        </span>

                                    </div>

                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>
                
                <!-- ORDER NOTES -->
                <section class="checkout-card">

                    <div class="checkout-card-heading">
                        <i class="fa-regular fa-comment-dots"></i>
                        <h2>Order Notes <span>(Optional)</span></h2>
                    </div>

                    <textarea
                        id="checkoutNotes"
                        class="checkout-notes"
                        maxlength="500"
                        placeholder="Any special instructions for your order?"
                    ></textarea>

                    <div class="checkout-notes-count" id="checkoutNotesCount">
                        0/500
                    </div>

                </section>

            </div>

            <!-- RIGHT COLUMN -->
            <aside class="checkout-right">

                <!-- ORDER SUMMARY -->
                <section class="checkout-card">

                    <div class="checkout-card-heading">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <h2>Order Summary</h2>
                        <span class="checkout-item-count">0 items</span>
                    </div>
                   
                    <div class="checkout-items">

                        <?php if (empty($checkoutItems)): ?>

                            <div class="checkout-empty">
                                No selected products found in your cart.
                            </div>

                        <?php else: ?>

                            <?php foreach ($checkoutItems as $item): ?>

                                <div class="checkout-item">

                                    <img
                                        src="<?= !empty($item['main_image'])
                                            ? esc($item['main_image'])
                                            : base_url('Images/cart2.png'); ?>"
                                        alt="<?= esc($item['product_name']); ?>"
                                        class="checkout-item-image"
                                    >

                                    <div class="checkout-item-details">
                                        <strong>
                                            <?= esc($item['product_name']); ?>
                                        </strong>

                                        <span>
                                            ₹<?= number_format($item['unit_price'], 2); ?>
                                            × <?= (int) $item['product_quantity']; ?>
                                        </span>
                                    </div>

                                    <strong class="checkout-item-total">
                                        ₹<?= number_format($item['line_total'], 2); ?>
                                    </strong>

                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="checkout-summary-lines">
                        <div class="checkout-summary-row">
                            <span>Subtotal</span>
                            <strong id="checkoutSubtotal">
                                ₹<?= number_format((float) $subtotal, 2); ?>
                            </strong>
                        </div>
                        
                        <div class="checkout-summary-row">
                            <span>
                                Shipping Fee

                                <?php if (!empty($recommendedCourier)): ?>
                                    
                                    <small
                                        class="checkout-recommended-courier"
                                        id="checkoutCourierLabel"
                                    >
                                        <?= !empty($recommendedCourier)
                                            ? 'Recommended courier - ' . esc($recommendedCourier)
                                            : ''; ?>
                                    </small>

                                <?php endif; ?>
                            </span>

                            <strong id="checkoutShipping">
                                <?php if ($shippingCost !== null): ?>
                                    ₹<?= number_format((float) $shippingCost, 2); ?>
                                <?php else: ?>
                                    TBD
                                <?php endif; ?>
                            </strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span >Discount</span>
                            <strong class="checkout-discount" id="checkoutDiscount">-₹0.00</strong>
                        </div>
                        
                        <div class="checkout-summary-row" id="checkoutStoreCreditRow" hidden>
                            <span>Store Credit Used</span>
                            <strong id="checkoutStoreCredit">-₹0.00</strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>COD Charges</span>
                            <strong id="checkoutCod">₹0.00</strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>GST (18%)</span>
                            <strong id="checkoutGst">
                                ₹<?= number_format((float) $gst, 2); ?>
                            </strong>
                        </div>

                    </div>

                    <div class="checkout-total-row">
                        <strong>Total Amount</strong>
                        <strong class="checkout-grand-total" id="checkoutGrandTotal">
                            <?php if ($total !== null): ?>
                                ₹<?= number_format((float) $total, 2); ?>
                            <?php else: ?>
                                TBD
                            <?php endif; ?>
                        </strong>
                    </div>

                    <?php if (!empty($shippingError)): ?>
                        <p class="checkout-shipping-error">
                            <?= esc($shippingError); ?>
                        </p>
                    <?php endif; ?>


                    <p class="checkout-tax-note">
                        Inclusive of applicable taxes
                    </p>
                    
                    <?php if ($total !== null && $total > 8000): ?>
                        <p class="checkout-cod-notice">
                            We do not accept Cash on Delivery (COD) orders above
                            ₹8000.
                            Please select another payment method.
                        </p>
                    <?php endif; ?>

                </section>
                
                <!-- COUPON -->
                <section class="checkout-card checkout-compact-card">
                    <div class="checkout-card-heading">
                        <i class="fa-solid fa-ticket"></i>
                        <h2>Apply Coupon Code</h2>
                    </div>

                    <div class="checkout-inline-form">
                        <input
                            type="text"
                            id="couponCodeInput"
                            placeholder="Enter coupon code"
                            autocomplete="off"
                            maxlength="100"
                        >

                        <button
                            type="button"
                            class="checkout-primary-button"
                            id="applyCouponButton"
                        >
                            Apply
                        </button>
                    </div>

                    <p id="couponMessage" aria-live="polite"></p>

                    <button
                        type="button"
                        id="removeCouponButton"
                        hidden
                    >
                        Remove coupon
                    </button>
                </section>
                
                <!-- STORE CREDIT -->
                <section class="checkout-card checkout-compact-card"
                         id="checkoutStoreCreditSection">

                    <div class="checkout-card-heading">
                        <i class="fa-solid fa-gift"></i>
                        <h2>Use Store Credit</h2>
                    </div>

                    <div class="checkout-store-credit">
                        <div>
                            <span>Available Store Credit</span>
                            <strong id="availableStoreCredit">
                                ₹<?= number_format(
                                    (float) ($customer['store_credit'] ?? 0),
                                    2
                                ); ?>
                            </strong>
                        </div>

                        <div class="checkout-inline-form">
                            <input
                                type="number"
                                id="usedStoreCreditInput"
                                min="0"
                                max="<?= esc((float) ($customer['store_credit'] ?? 0)); ?>"
                                step="0.01"
                                placeholder="Enter amount"
                                <?= (float) ($customer['store_credit'] ?? 0) <= 0
                                    ? 'disabled'
                                    : ''; ?>
                            >

                            <button
                                type="button"
                                class="checkout-primary-button"
                                id="applyStoreCreditButton"
                                <?= (float) ($customer['store_credit'] ?? 0) <= 0
                                    ? 'disabled'
                                    : ''; ?>
                            >
                                Apply
                            </button>
                        </div>

                        <p id="storeCreditMessage" aria-live="polite"></p>

                        <button
                            type="button"
                            id="removeStoreCreditButton"
                            hidden>
                            Remove store credit
                        </button>

                    </div>
                </section>

                <!-- PAYMENT METHOD -->
                <section class="checkout-card">

                    <div class="checkout-card-heading">
                        <i class="fa-regular fa-credit-card"></i>
                        <h2>Payment Method</h2>
                    </div>

                    <div class="checkout-payment-options" id="checkoutPaymentSection">

                        <?php if (!empty($codAvailable) && $codEligible): ?>
                            <label class="checkout-payment-option selected">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    checked >

                                <i class="fa-solid fa-hand-holding-dollar"></i>

                                <span>
                                    <strong>Cash on Delivery (COD)</strong>
                                    <small>Pay in cash at the time of delivery.</small>
                                </span>
                            </label>
                        <?php endif; ?>

                        <label class="checkout-payment-option">
                            <input type="radio"
                                   name="payment_method"
                                   value="cashfree">

                            <i class="fa-regular fa-credit-card"></i>

                            <span>
                                <strong>Pre-Paid Order</strong>
                                <small>Visa, Mastercard, UPI, Net Banking & more</small>
                            </span>
                        </label>


                    </div>

                    <button
                        type="button"
                        class="checkout-place-order"
                        onclick="handlePlaceOrder()"
                    >
                        <i class="fa-solid fa-lock"></i>
                        Place Order
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <p class="checkout-terms">
                        By placing your order, you agree to our
                        <a href="<?= base_url('terms-and-conditions'); ?>">
                            Terms & Conditions
                        </a>
                        and
                        <a href="<?= base_url('privacy-policy'); ?>">
                            Privacy Policy
                        </a>.
                    </p>

                </section>

            </aside>

        </div>

    </div>

</div>

<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>


<script>


    //****************************************************
    //********** universal functions ******************
    //****************************************************
    
    function formatINR(amount) {
        return '₹' + Number(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function roundMoney(amount) {
        return Math.round((amount + Number.EPSILON) * 100) / 100;
    }


    //****************************************************
    //********** to check order notes length ******************
    //****************************************************
    document.addEventListener('DOMContentLoaded', function () {

        const notesTextarea = document.getElementById('checkoutNotes');
        const notesCounter = document.getElementById('checkoutNotesCount');

        if (!notesTextarea || !notesCounter) {
            return;
        }

        const maxLength = 500;

        function updateNotesCounter() {
            const currentLength = notesTextarea.value.length;

            notesCounter.textContent = `${currentLength}/${maxLength}`;
        }

        // Update the counter whenever the text changes.
        notesTextarea.addEventListener('input', updateNotesCounter);

        // Set the correct initial count.
        updateNotesCounter();

    });



    //****************************************************
    //********** to add new address ******************
    //****************************************************
    
    //click for add address
    $(document).on("click", "#addCheckoutAddress", function() {
        openAddAddress();
    });
    
    //to open add address
    function openAddAddress() {
        // Prevent duplicate modals if one is already open.
        if ($("#addAddressModal").length) {
            $("#addAddressModal").fadeIn(200);
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('accountAddAddress'); ?>",
            {
                isCheckout : true
            },
            function(response) {
                hideLoader();

                $("body").append(response);
                $("#addAddressModal").fadeIn(200);
            }
        ).fail(function(xhr) {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to open the address form. Please try again."
            );
        });
    }


    //****************************************************
    //********** billing address toggle ******************
    //****************************************************
    const sameBilling = document.getElementById('sameBillingAddress');
    const billingList = document.getElementById('billingAddressList');

    function toggleBillingAddress() {
        if (!sameBilling || !billingList) return;

        billingList.style.display = sameBilling.checked ? 'none' : 'flex';
    }

    function copyShippingAddressToBilling() {
        if (!sameBilling || !sameBilling.checked) return;

        const selectedShippingAddress = document.querySelector(
            'input[name="shipping_address"]:checked'
        );

        if (!selectedShippingAddress) return;

        const matchingBillingAddress = document.querySelector(
            'input[name="billing_address"][value="' +
            selectedShippingAddress.value +
            '"]'
        );

        if (matchingBillingAddress) {
            matchingBillingAddress.checked = true;
            matchingBillingAddress.dispatchEvent(
                new Event('change', { bubbles: true })
            );
        }
    }

    if (sameBilling && billingList) {
        sameBilling.addEventListener('change', function () {
            toggleBillingAddress();

            if (sameBilling.checked) {
                copyShippingAddressToBilling();
            }
        });

        document.querySelectorAll('input[name="shipping_address"]').forEach(
            function (radio) {
                radio.addEventListener('change', function () {
                    copyShippingAddressToBilling();
                });
            }
        );

        // Set the initial visibility and address selection.
        toggleBillingAddress();
        copyShippingAddressToBilling();
    }
 
 
 
 
 
    //****************************************************
    //********** shipping address toggle *****************
    //****************************************************
    
    let selectedShippingAddress = null;
    let shippingCost = <?= json_encode($shippingCost); ?>;
    let codAvailableFromCourier = <?= json_encode((bool) ($codAvailable ?? false)); ?>;
    let codChargesFromCourier = <?= json_encode((float) ($codCharges ?? 50)); ?>;
    let shippingRequestNumber = 0;

    const shippingUrl = "<?= base_url('calculateShipping'); ?>";

    //create array of selected products
    const selectedItems = <?= json_encode(array_map(function ($item) {
        return [
            'product_id' => (string) $item['id'],
            'quantity' => (int) $item['product_quantity']
        ];
    }, $checkoutItems ?? [])); ?>;

    // Called when the shipping address changes or the page opens.
    async function updateShippingAddress() {
        const address = document.querySelector(
            'input[name="shipping_address"]:checked'
        );

        if (!address) {
            selectedShippingAddress = null;
            return;
        }

        selectedShippingAddress = address.value;

        // Keep the billing address synchronized when requested.
        if (typeof copyShippingAddressToBilling === 'function') {
            copyShippingAddressToBilling();
        }

        await recalculateShipping(address);
    }

    // Recalculate shipping for the selected address.
    async function recalculateShipping(address) {
        const requestId = ++shippingRequestNumber;

        const shippingElement = document.getElementById('checkoutShipping');
        const courierLabel = document.getElementById('checkoutCourierLabel');

        const pincode = address.dataset.pincode || '';
        const state = address.dataset.state || '';
        const city = address.dataset.city || '';

        if (
            !/^\d{6}$/.test(pincode) ||
            !state ||
            !city ||
            selectedItems.length === 0
        ) {
            shippingCost = null;
            codAvailableFromCourier = false;
            codChargesFromCourier = 0;

            if (shippingElement) shippingElement.textContent = 'Unavailable';
            if (courierLabel) courierLabel.textContent = '';

            if (typeof updateSummary === 'function') updateSummary();
            return;
        }

        if (shippingElement) shippingElement.textContent = 'Calculating...';
        if (courierLabel) courierLabel.textContent = '';

        try {
            const response = await fetch(shippingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    pincode: pincode,
                    state: state,
                    city: city,
                    selected_items: JSON.stringify(selectedItems)
                })
            });

            const result = await response.json();

            // Ignore the response if another address was selected meanwhile.
            if (
                requestId !== shippingRequestNumber ||
                selectedShippingAddress !== address.value
            ) {
                return;
            }

            if (!response.ok || result.status !== 'success' || !result.data) {
                throw new Error(
                    result.message || 'Unable to calculate shipping.'
                );
            }

            shippingCost = Number(result.data.shipping_cost);

            codAvailableFromCourier =
                result.data.cod_available === true ||
                Number(result.data.cod_available) === 1;

            // Preserve the existing ₹50 COD fee.
            const courierCodCharge = Number(result.data.cod_charges);
            codChargesFromCourier =  Number.isFinite(courierCodCharge) && courierCodCharge > 0  ? courierCodCharge : 50;  
            if (!Number.isFinite(shippingCost)) {
                throw new Error('Invalid shipping cost received.');
            }

            if (shippingElement) {
                shippingElement.textContent = '₹' +
                    shippingCost.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            }

            if (courierLabel) {
                courierLabel.textContent = result.data.courier_name
                    ? 'Recommended courier - ' + result.data.courier_name
                    : '';
            }

            if (typeof updateSummary === 'function') updateSummary();

        } catch (error) {
            if (
                requestId !== shippingRequestNumber ||
                selectedShippingAddress !== address.value
            ) {
                return;
            }

            shippingCost = null;
            codAvailableFromCourier = false;
            codChargesFromCourier = 0;

            if (shippingElement) shippingElement.textContent = 'Unavailable';
            if (courierLabel) {
                courierLabel.textContent =
                    error.message || 'Unable to calculate shipping.';
            }

            if (typeof updateSummary === 'function') updateSummary();

            console.error('Shipping calculation failed:', error);
        }
    }

    // Listen for a new shipping address.
    document.querySelectorAll('input[name="shipping_address"]').forEach(
        function (radio) {
            radio.addEventListener('change', updateShippingAddress);
        }
    );

    // Calculate shipping for the default address on page load.
    updateShippingAddress();

   




    //****************************************************
    //********** store credit & coupons  *****************
    //****************************************************
    
    const subtotal = <?= json_encode((float) $subtotal); ?>;
    const availableStoreCredit = <?= json_encode((float) ($customer['store_credit'] ?? 0)); ?>;
    const applyCouponUrl = "<?= base_url('applyCoupon'); ?>";

    let appliedCoupon = null;
    let appliedCouponDiscount = 0;
    let usedStoreCredit = 0;

    //to apply coupon
    async function applyCoupon() {
        const couponCodeInput = document.getElementById('couponCodeInput');
        const applyCouponButton = document.getElementById('applyCouponButton');
        const couponMessage = document.getElementById('couponMessage');
        const removeCouponButton = document.getElementById('removeCouponButton');

        const couponCode = couponCodeInput.value.trim();

        if (!couponCode) {
            couponMessage.textContent = 'Please enter a coupon code.';
            return;
        }

        applyCouponButton.disabled = true;
        applyCouponButton.textContent = 'Checking...';
        couponMessage.textContent = '';

        try {
            const response = await fetch(applyCouponUrl, {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ coupon_code: couponCode })
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success' || !result.data) {
                couponMessage.textContent =
                    result.message || 'Unable to apply this coupon.';
                return;
            }

            const coupon = result.data;
            const amount = Number(coupon.amount);

            if (!Number.isFinite(amount) || amount <= 0) {
                throw new Error('The coupon discount amount is invalid.');
            }

            if (coupon.amount_type === 'percent') {
                appliedCouponDiscount = roundMoney(subtotal * amount / 100);
            } else if (coupon.amount_type === 'fixed') {
                appliedCouponDiscount = roundMoney(amount);
            } else {
                throw new Error('This coupon has an unsupported discount type.');
            }

            appliedCouponDiscount = Math.min(appliedCouponDiscount, subtotal);
            appliedCoupon = coupon;

            couponCodeInput.value = coupon.coupon_code || couponCode;
            couponMessage.textContent =
                'Coupon applied successfully! You saved ' +
                formatINR(appliedCouponDiscount) + '.';

            if (removeCouponButton) removeCouponButton.hidden = false;

            updateSummary();

        } catch (error) {
            couponMessage.textContent =
                error.message || 'Unable to apply the coupon. Please try again.';
            console.error('Coupon application failed:', error);
        } finally {
            applyCouponButton.disabled = false;
            applyCouponButton.textContent = 'Apply';
        }
    }

    //to remove applied coupon
    function removeCoupon() {
        appliedCoupon = null;
        appliedCouponDiscount = 0;

        document.getElementById('couponCodeInput').value = '';
        document.getElementById('couponMessage').textContent = 'Coupon removed.';
        document.getElementById('removeCouponButton').hidden = true;

        updateSummary();
    }

    document.getElementById('applyCouponButton')  ?.addEventListener('click', applyCoupon);
    document.getElementById('removeCouponButton') ?.addEventListener('click', removeCoupon);

    //to apply store credit
    function applyStoreCredit() {
        const input = document.getElementById('usedStoreCreditInput');
        const message = document.getElementById('storeCreditMessage');
        const amount = Number(input.value);

        if (input.value.trim() === '' || !Number.isFinite(amount) || amount <= 0) {
            message.textContent = 'Please enter a valid amount greater than ₹0.';
            input.focus();
            return;
        }

        if (shippingCost === null || !Number.isFinite(Number(shippingCost))) {
            message.textContent = 'Please calculate shipping before applying store credit.';
            return;
        }

        if (amount > availableStoreCredit) {
            message.textContent =
                'You only have ' + formatINR(availableStoreCredit) +
                ' available. Applying the maximum available amount.';
        } else {
            message.textContent = '';
        }

        usedStoreCredit = Math.min(amount, availableStoreCredit);
        updateSummary();
    }

    //to remoive applied credit
    function removeStoreCredit() {
        usedStoreCredit = 0;
        document.getElementById('usedStoreCreditInput').value = '';
        document.getElementById('storeCreditMessage').textContent = '';

        updateSummary();
    }

    document.getElementById('applyStoreCreditButton') ?.addEventListener('click', applyStoreCredit);
    document.getElementById('removeStoreCreditButton')?.addEventListener('click', removeStoreCredit);

    //to check if storecredit is enough or not
    function isStoreCreditCoveringOrder() {
        if (
            shippingCost === null ||
            !Number.isFinite(Number(shippingCost))
        ) {
            return false;
        }

        // COD charges do not apply when store credit covers the order.
        const amountWithoutCOD = roundMoney(
            subtotal +
            Number(shippingCost) -
            appliedCouponDiscount
        );
        let gst = 0;
        gst = Math.round(amountWithoutCOD *0.18);
        const totalAmt = amountWithoutCOD +gst;

        return totalAmt > 0 &&
            usedStoreCredit >= totalAmt;
    }


    //****************************************************
    //**************** summary update *******************
    //****************************************************
    let selectedPaymentMethod =document.querySelector('input[name="payment_method"]:checked')?.value || null;
    
    //to update the number of items
    function updateCheckoutItemCount() {
        const itemCount = document.querySelectorAll(
            '.checkout-items .checkout-item'
        );

        let totalQuantity = 0;

        itemCount.forEach(function (item) {
            const quantityText = item.querySelector(
                '.checkout-item-details span'
            )?.textContent || '';

            const match = quantityText.match(/×\s*(\d+)/);

            if (match) {
                totalQuantity += Number(match[1]);
            }
        });

        const countElement = document.querySelector('.checkout-item-count');

        if (countElement) {
            countElement.textContent =
                totalQuantity + (totalQuantity === 1 ? ' item' : ' items');
        }
    }

    updateCheckoutItemCount();

    
    //to update the order summary section
    function updateSummary() {
        const codLimit = 8000;

        const shippingUnavailable = shippingCost === null || !Number.isFinite(Number(shippingCost));

        const isCODSelected = () => { const selected = document.querySelector(
                'input[name="payment_method"]:checked'
            );

            return selected?.value.toLowerCase() === 'cod';
        };

        let codCharges = 0;
        let gst = null;
        let totalBeforeCredit = null;
        let total = null;
        let fullyPaidByStoreCredit = false;

        if (!shippingUnavailable) {
            const amountWithoutCOD = Math.max(
                0,
                subtotal + Number(shippingCost) - appliedCouponDiscount
            );

            // Calculate the full non-COD total, including GST.
            const baseGST = roundMoney(amountWithoutCOD * 0.18);
            const totalWithoutCOD = roundMoney(amountWithoutCOD + baseGST);

            fullyPaidByStoreCredit = totalWithoutCOD > 0 && usedStoreCredit >= totalWithoutCOD;
            
            codCharges = !fullyPaidByStoreCredit && isCODSelected()
                ? (  Number(codChargesFromCourier) > 0  ? Number(codChargesFromCourier)  : 50  )
                : 0;

            const taxableAmount = Math.max(
                0,
                subtotal +
                Number(shippingCost) +
                codCharges -
                appliedCouponDiscount
            );

            gst = Math.round(taxableAmount * 0.18);
            totalBeforeCredit = roundMoney(taxableAmount + gst);

            usedStoreCredit = roundMoney(
                Math.min(
                    Math.max(0, usedStoreCredit),
                    availableStoreCredit,
                    totalBeforeCredit
                )
            );

            total = Math.max(0, roundMoney(totalBeforeCredit - usedStoreCredit));

            // Recheck coverage using the actual total, including any COD charges.
            fullyPaidByStoreCredit = total <= 0 && usedStoreCredit > 0;
        }

        document.getElementById('checkoutSubtotal').textContent = formatINR(subtotal);
        document.getElementById('checkoutShipping').textContent =  shippingUnavailable ? 'TBD' : formatINR(shippingCost);
        document.getElementById('checkoutCod').textContent =  formatINR(codCharges);
        document.getElementById('checkoutGst').textContent =  gst === null ? 'TBD' : formatINR(gst);
        document.getElementById('checkoutGrandTotal').textContent = total === null ? 'TBD' : formatINR(total);
        document.getElementById('checkoutDiscount').textContent =  '-' + formatINR(appliedCouponDiscount);
        
        const creditRow = document.getElementById('checkoutStoreCreditRow');
        const creditAmount = document.getElementById('checkoutStoreCredit');
        const creditInput = document.getElementById('usedStoreCreditInput');
        const paymentSection = document.getElementById('checkoutPaymentSection');
        const removeCreditButton = document.getElementById('removeStoreCreditButton');

        if (creditRow && creditAmount) {
            creditRow.hidden = usedStoreCredit <= 0;
            creditAmount.textContent = '-' + formatINR(usedStoreCredit);
        }

        if (creditInput) {
            creditInput.value =
                usedStoreCredit > 0 ? usedStoreCredit.toFixed(2) : '';
        }

        if (removeCreditButton) {
            removeCreditButton.hidden = usedStoreCredit <= 0;
        }

        if (paymentSection) {
            paymentSection.hidden = fullyPaidByStoreCredit;
        }

        const notice = document.querySelector('.checkout-cod-notice');

        if (notice) {
            notice.hidden = total === null || total <= codLimit;
        }

        const codInput = document.querySelector(
            'input[name="payment_method"][value="cod"]'
        );

        if (codInput) {
            const codEligible =
                !fullyPaidByStoreCredit &&
                codAvailableFromCourier &&
                total !== null &&
                total > 0 &&
                total <= codLimit;

            codInput.disabled = !codEligible;

            const codOption = codInput.closest('.checkout-payment-option');

            if (codOption) {
                codOption.hidden = !codEligible;
            }
            
            if (!codEligible && codInput.checked && !fullyPaidByStoreCredit) {
                codInput.checked = false;

                const alternative = document.querySelector(
                    'input[name="payment_method"]:not([value="cod"]):not(:disabled)'
                );

                if (alternative) {
                    alternative.checked = true;
                    alternative.dispatchEvent(
                        new Event('change', { bubbles: true })
                    );
                }
            }

        }
    }






    //****************************************************
    //**************** payment options *******************
    //****************************************************


    //to toggle different payment options
    function updatePaymentSelection() {
        const paymentRadios = document.querySelectorAll(
            '.checkout-payment-option input[name="payment_method"]'
        );

        paymentRadios.forEach(function (radio) {
            const option = radio.closest('.checkout-payment-option');

            if (option) {
                option.classList.toggle('selected', radio.checked);
            }
        });

        const selectedRadio = document.querySelector(
            'input[name="payment_method"]:checked'
        );

        selectedPaymentMethod = selectedRadio  ? selectedRadio.value  : null;
        updateSummary();
    }

    document.querySelectorAll(
        '.checkout-payment-option input[name="payment_method"]'
    ).forEach(function (radio) {
        radio.addEventListener('change', updatePaymentSelection);
    });

    // Set the initial styling and selected method
    updatePaymentSelection();

    //when checkout is clicked
    function handlePlaceOrder() {

        // 1. Store credit covers the entire order
        if (isStoreCreditCoveringOrder()) {
            placeStoreCreditOrder();
            return;
        }

        // 2. Cash on Delivery
        if (selectedPaymentMethod === 'cod') {
            placeCodOrder();
            return;
        }

        // 3. Prepaid payment methods
        if (selectedPaymentMethod === 'cashfree') {
            placePaidOrder();
            return;
        }

        // No valid payment method selected
        showAlert('error','Error','Please select a payment type');
    }

    
    
    //****************************************************
    //**************** placing order  ********************
    //****************************************************
    
    //place order with store credit
    async function placeStoreCreditOrder() {
        try {
            if (!isStoreCreditCoveringOrder()) {
                alert('Store credit does not cover the entire order.');
                return;
            }

            const selectedShippingAddress = document.querySelector( 'input[name="shipping_address"]:checked' );
            const sameBillingAddress = document.getElementById( 'sameBillingAddress'  );
            const selectedBillingAddress = sameBillingAddress?.checked  ? selectedShippingAddress?.value  : document.querySelector(  'input[name="billing_address"]:checked'   )?.value;
            const notes = document.querySelector('.checkout-notes');
            
            if (!selectedShippingAddress || !selectedBillingAddress) {
                alert('Please select your shipping and billing addresses.');
                return;
            }

            if (!selectedItems.length) {
                alert('Your cart is empty.');
                return;
            }

            const placeOrderButton = document.querySelector(
                '.checkout-place-order'
            );

            if (placeOrderButton) {
                placeOrderButton.disabled = true;
                placeOrderButton.textContent = 'Placing Order...';
            }

            
            const response = await fetch(
                "<?= base_url('placeOrderWithStoreCredit'); ?>",
                {
                    method: 'POST',
                    headers: {
                        'Content-Type':
                            'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                    products: JSON.stringify(selectedItems),
                    usedStoreCredit: usedStoreCredit.toFixed(2),
                    discount: appliedCouponDiscount.toFixed(2),
                    coupon_code: appliedCoupon?.coupon_code || '',
                    shipping_address_id: selectedShippingAddress.value,
                    billing_address_id: selectedBillingAddress,
                    order_comment: notes?.value.trim() || ''
                })
                }
            );

            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                showAlert(result.status,'Error',result.message);
            }else{

            showAlert(result.status,'Order Placed',result.message);
            window.location.href="<?= base_url('userhome'); ?>?";
            
            }


        } catch (error) {
            console.error('Store credit order failed:', error);
            alert(error.message || 'Something went wrong while placing your order.');

        } finally {
            const placeOrderButton = document.querySelector(
                '.checkout-place-order'
            );

            if (placeOrderButton) {
                placeOrderButton.disabled = false;
                placeOrderButton.innerHTML =
                    '<i class="fa-solid fa-lock"></i> Place Order ' +
                    '<i class="fa-solid fa-arrow-right"></i>';
            }
        }
    }
    
    //place order with cod
    async function placeCodOrder() {
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const selectedShippingAddress = document.querySelector( 'input[name="shipping_address"]:checked' );
        const sameBillingAddress = document.getElementById( 'sameBillingAddress'  );
        const selectedBillingAddress = sameBillingAddress?.checked  ? selectedShippingAddress?.value  : document.querySelector(  'input[name="billing_address"]:checked'   )?.value;
            const codCharged = document.getElementById('checkoutCod').value;
            const notes = document.querySelector('.checkout-notes');

            if (!selectedShippingAddress || !selectedBillingAddress) {
                alert('Please select your shipping and billing addresses.');
                return;
            }

            if (!selectedItems.length) {
                alert('Your cart is empty.');
                return;
            }


        try {
            if (placeOrderBtn) {
                placeOrderBtn.disabled = true;
                placeOrderBtn.textContent = 'Placing Order...';
            }
            

            const response = await fetch(
                '<?= base_url("placeOrderWithCod") ?>',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type':
                            'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body:new URLSearchParams({
                        products: JSON.stringify(selectedItems),
                        shipping_address: selectedShippingAddress.value,
                        billing_address: selectedBillingAddress,
                        usedStoreCredit: usedStoreCredit.toFixed(2),
                        discount: appliedCouponDiscount.toFixed(2),
                        codCharges : codCharged,
                        coupon_code: appliedCoupon?.coupon_code || '',
                        order_comment: notes?.value.trim() || ''
                    })
                }
            );

            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                showAlert(result.status,'Error',result.message);
            }else{

                showAlert(result.status,'Order Placed',result.message);
                window.location.href="<?= base_url('userhome'); ?>?";
            }


        } catch (error) {
            console.error('COD order error:', error.message);

            showAlert('error','Error', 'Something went wrong while placing your order.');

        } finally {
            if (placeOrderBtn) {
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
            }
        }
    }
    
    //to place pre-paid order
    async function placePaidOrder() {
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        
        try {
            if (placeOrderBtn) {
                placeOrderBtn.disabled = true;
                placeOrderBtn.textContent = 'Connecting to payment...';
            }
            
            const selectedShippingAddress = document.querySelector( 'input[name="shipping_address"]:checked' );
            const sameBillingAddress = document.getElementById( 'sameBillingAddress'  );
            const selectedBillingAddress = sameBillingAddress?.checked  ? selectedShippingAddress?.value  : document.querySelector(  'input[name="billing_address"]:checked'   )?.value;
            let grandTotal = document.getElementById('checkoutGrandTotal').textContent;
            const notes = document.querySelector('.checkout-notes');
            
            grandTotal = parseFloat(
                grandTotal.replace(/[^\d.]/g, '')
            );

            // Create the Cashfree order through your CodeIgniter controller.
            const response = await fetch(
                '<?= base_url("createCashfreeOrder") ?>',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        shipping_address: selectedShippingAddress?.value,
                        billing_address: selectedBillingAddress,
                        usedStoreCredit: usedStoreCredit.toFixed(2),
                        coupon_code: appliedCoupon?.coupon_code || '',
                        products: JSON.stringify(selectedItems),
                        order_comment: notes?.value.trim() || ''
                    })
                }
            );

            const result = await response.json();

            if (result[0].success === 0) {
                showAlert('error','Payment error', result.message || 'Unable to initiate prepaid payment.'  );
            }

            // Open Cashfree Checkout.
            const cashfree = Cashfree({  mode: result.mode || 'production' }); //mode "production" or "sandbox"
            await cashfree.checkout({
                paymentSessionId: result[0].payment_session_id,
                redirectTarget: '_self'
            });
            
            
        } catch (error) {
            console.error('Cashfree checkout error:', error);
            showAlert('error','Payment Error',error.message || 'Unable to start payment. Please try again.');

        } finally {
            if (placeOrderBtn) {
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
            }
        }
    }



</script>

<?= $this->endSection(); ?>
    