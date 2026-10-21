
<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<?php
$cartItems = $cartItems ?? [];
$subtotal = $subtotal ?? 0;
$cartCount = $cartCount ?? 0;
?>


<div class="cart-page">

    <?php if (empty($cartItems)): ?>

        <div class="cart-top">
            <div class="cart-heading">
                <img src="<?= base_url('Images/ic_cart.png'); ?>"
                    alt="Shopping Cart"
                    class="cart-heading-icon">
                <div>
                    <h1>Shopping Cart</h1>
                    <p>Your cart is currently empty</p>
                </div>
            </div>

            <a href="<?= base_url(); ?>" class="cart-outline-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping
            </a>
        </div>

        <div class="cart-items-panel cart-empty">
            <img src="<?= base_url('Images/cart1.png'); ?>" alt="Empty shopping cart">
            <h2>Your cart is empty!</h2>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="<?= base_url(); ?>" class="cart-primary-btn">
                Start Shopping <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    <?php else: ?>

        <div class="cart-top">
            <div class="cart-heading">
                <img src="<?= base_url('Images/ic_cart.png'); ?>"
                    alt="Shopping Cart"
                    class="cart-heading-icon">
                <div>
                    <h1>Shopping Cart</h1>
                    <p>You have <?= (int) $cartCount; ?> items in your cart</p>
                </div>
            </div>

            <a href="<?= base_url(); ?>" class="cart-outline-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping
            </a>
        </div>

        <div class="cart-layout">

            <div class="cart-items-panel">

                <div class="cart-table-head">
                    <input type="checkbox" class="cart-select-all" aria-label="Select all items">
                    <span>Select All (<?= count($cartItems); ?> items)</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span>Total</span>
                    <span></span>
                </div>

                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">

                        <input type="checkbox"
                            class="cart-item-check"
                            data-product-id="<?= esc($item['id']); ?>"
                            data-product-quantity="<?= (int) $item['product_quantity']; ?>"
                            data-line-total="<?= (float) $item['line_total']; ?>"
                            aria-label="Select <?= esc($item['product_name']); ?>"
                            checked>

                        <div class="cart-product">
                            <img
                                src="<?= !empty($item['main_image'])
                                        ? esc($item['main_image'])
                                        : base_url('Images/cart2.png'); ?>"
                                alt="<?= esc($item['product_name']); ?>">

                            <div class="cart-product-info">
                                <div class="cart-product-name">
                                    <?= esc($item['product_name']); ?>
                                </div>

                                <?php if (!empty($item['version'])): ?>
                                    <div class="cart-product-version">
                                        <?= esc($item['version']); ?>
                                    </div>
                                <?php endif; ?>

                                <span class="cart-stock">
                                    <?= ((int) ($item['product_qty'] ?? 0) > 0)
                                        ? 'In Stock'
                                        : 'Currently Unavailable'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="cart-price">
                            ₹<?= number_format((float) $item['unit_price']); ?>

                            <?php if (
                                !empty($item['discounted_price']) &&
                                $item['discounted_price'] > 0 &&
                                $item['discounted_price'] < $item['price']
                            ): ?>
                                <span class="cart-old-price">
                                    ₹<?= number_format((float) $item['price']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cart-quantity">
                            <button
                                type="button"
                                onclick="changeCartQuantity('<?= esc($item['id']); ?>', <?= (int) $item['product_quantity']; ?>, -1)"
                                <?= (int) $item['product_quantity'] <= 1 ? 'disabled' : ''; ?>
                            >−</button>

                            <span><?= (int) $item['product_quantity']; ?></span>

                            <button
                                type="button"
                                onclick="changeCartQuantity('<?= esc($item['id']); ?>', <?= (int) $item['product_quantity']; ?>, 1)"
                            >+</button>
                        </div>

                        <div class="cart-line-total">
                            ₹<?= number_format((float) $item['line_total']); ?>
                        </div>
                        
                        <button
                            type="button"
                            class="cart-remove"
                            onclick="removeCartProduct('<?= esc($item['id']); ?>')"
                            aria-label="Remove <?= esc($item['product_name']); ?>"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>


                    </div>
                <?php endforeach; ?>

            </div>

            <aside>
                <div class="cart-summary">
                    <h2>Order Summary</h2>

                    <div class="cart-summary-row">
                        <span>Subtotal (<span id="selectedItemCount"><?= count($cartItems); ?></span> selected)</span>
                        <strong id="cartSubtotal">₹<?= number_format((float) $subtotal); ?></strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Shipping Fee</span>
                        <strong id="shippingFee" class="cart-muted">
                            TBD <i class="fa-regular fa-circle-question"></i>
                        </strong>
                    </div>
                                   
                    <div class="cart-summary-row">
                        <span>GST (18%)</span>
                        <strong id="cartGst">₹<?= number_format((float) $subtotal * 0.18, 2); ?></strong>
                    </div>

                    <hr class="cart-summary-divider">

                    <div class="cart-summary-row cart-total-row">
                        <strong>Total Amount</strong>
                        <div>
                            <strong id="cartTotal">₹<?= number_format((float) $subtotal); ?></strong>   
                        </div>
                    </div>

                    <button
                        type="button"
                        class="cart-secondary-btn"
                        id="toggleShippingForm"
                        aria-expanded="false"
                        aria-controls="shippingCalculator"
                    >
                        <i class="fa-solid fa-truck-fast"></i>
                        Calculate Shipping
                        <i class="fa-solid fa-chevron-down" id="shippingToggleIcon"></i>
                    </button>

                    <div class="shipping-calculator" id="shippingCalculator" hidden>

                        <div class="shipping-field">
                            <label for="shippingState">State</label>
                            <select id="shippingState" required>
                                <option value="">Select state</option>
                            </select>
                        </div>

                        <div class="shipping-field">
                            <label for="shippingCity">City</label>
                            <select id="shippingCity" required disabled>
                                <option value="">Select city</option>
                            </select>
                        </div>

                        <div class="shipping-field">
                            <label for="shippingPincode">Pincode</label>
                            <input
                                type="text"
                                id="shippingPincode"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="postal-code"
                                placeholder="Enter 6-digit pincode"
                                required
                            >
                        </div>

                        <button
                            type="button"
                            class="cart-primary-btn shipping-submit-btn"
                            id="calculateShippingSubmit"
                        >
                            Calculate Shipping
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <p class="shipping-calculator-message" id="shippingCalculatorMessage" hidden></p>

                    </div>
   
                    <button type="button"
                            class="cart-primary-btn"
                            id="proceedToCheckout">
                        <i class="fa-solid fa-lock"></i>
                        Proceed to Checkout
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                </div>
            </aside>

        </div>

    <?php endif; ?>

    <!-- cart recommendations -->  
    <?php if (!empty($cartItems) && !empty($recommendations)): ?>
        <div class="cart-recommendations">
            <div class="cart-recommendations-header">
                <h2>
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    You Might Also Like
                </h2>

            </div>

            <div class="cart-recommendations-slider">
                <button type="button"
                        class="cart-recommendation-arrow left"
                        onclick="scrollCartRecommendations(-1)"
                        aria-label="Previous recommendations">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="cart-recommendations-track" id="cartRecommendationsTrack">
                    <?php foreach ($recommendations as $recommendation): ?>
                        <?php
                        $recommendationPrice = (float) ($recommendation['price'] ?? 0);

                        $recommendationDiscountedPrice =
                            (float) ($recommendation['discounted_price'] ?? 0);

                        $recommendationHasDiscount = (
                            $recommendationDiscountedPrice > 0 &&
                            $recommendationDiscountedPrice < $recommendationPrice
                        );
                        ?>

                        <div class="cart-recommendation-card">

                            <button type="button"
                                    class="cart-recommendation-wishlist"
                                    onclick="addProductToWishlist('<?= esc($recommendation['id']); ?>')"
                                    aria-label="Add to wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>

                            <a href="<?= base_url('product/' . $recommendation['id']); ?>"
                               class="cart-recommendation-image">

                                <img src="<?= esc($recommendation['main_image']); ?>"
                                     alt="<?= esc($recommendation['product_name']); ?>"
                                     loading="lazy">

                            </a>

                            <div class="cart-recommendation-content">
                                <a href="<?= base_url('product/' . $recommendation['id']); ?>"
                                   class="cart-recommendation-title">
                                    <?= esc($recommendation['product_name']); ?>
                                </a>

                                <?php if ($recommendationHasDiscount): ?>

                                    <div class="cart-recommendation-prices">
                                        <span class="cart-recommendation-old-price">
                                            ₹<?= number_format($recommendationPrice, 0); ?>
                                        </span>

                                        <span class="cart-recommendation-price">
                                            ₹<?= number_format($recommendationDiscountedPrice, 0); ?>
                                        </span>
                                    </div>

                                <?php else: ?>

                                    <div class="cart-recommendation-price">
                                        ₹<?= number_format($recommendationPrice, 0); ?>
                                    </div>

                                <?php endif; ?>

                                <button type="button"
                                        class="cart-recommendation-add"
                                        onclick="addProductToCart('<?= esc($recommendation['id']); ?>')">

                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Add to Cart

                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button"
                        class="cart-recommendation-arrow right"
                        onclick="scrollCartRecommendations(1)"
                        aria-label="Next recommendations">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

            </div>

        </div>

    <?php endif; ?>


</div>


<script>
    //to change item quantity
    function changeCartQuantity(productId, currentQuantity, change) {
        const newQuantity = currentQuantity + change;

        if (newQuantity < 1 || newQuantity > 25) {
            return;
        }

        $.ajax({
            url: "<?= base_url('updateCartItem'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                product_id: productId,
                quantity: newQuantity
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message || 'Could not update quantity.');
                }
            },
            error: function() {
                alert('Unable to update your cart. Please try again.');
            }
        });
    }

    //to remove item from cart
    function removeCartProduct(productId) {
        showConfirm({
            title: "Remove Product?",
            message: "Are you sure you want to remove this product from your cart?",
            confirmText: "Remove",
            callback: function() {
                $.ajax({
                    url: "<?= base_url('removeCartItem'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            location.reload();
                        } else {
                            showAlert(
                                "error",
                                "Unable to Remove Product",
                                response.message || "Could not remove this product from your cart."
                            );
                        }
                    },
                    error: function() {
                        showAlert(
                            "error",
                            "Something Went Wrong",
                            "Unable to remove this product. Please try again."
                        );
                    }
                });
            }
        });
    }


    function scrollCartRecommendations(direction) {
        const track = document.getElementById('cartRecommendationsTrack');

        if (!track) {
            return;
        }

        const card = track.querySelector('.cart-recommendation-card');

        if (!card) {
            return;
        }

        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        const scrollAmount = card.getBoundingClientRect().width + gap;

        track.scrollBy({
            left: scrollAmount * direction,
            behavior: 'smooth'
        });
    }

    //for selected items
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.querySelector('.cart-select-all');
        const itemCheckboxes = Array.from(
            document.querySelectorAll('.cart-item-check')
        );

        const subtotalElement = document.getElementById('cartSubtotal');
        const totalElement = document.getElementById('cartTotal');
        const selectedCountElement = document.getElementById('selectedItemCount');

        
        function updateCartSummary() {
            let subtotal = 0;
            let selectedCount = 0;

            itemCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    subtotal += parseFloat(checkbox.dataset.lineTotal) || 0;
                    selectedCount++;
                }
            });

            // GST is currently calculated on subtotal only.
            const gst = subtotal * 0.18;
            const total = subtotal + gst;

            if (selectAll) {
                selectAll.checked =
                    itemCheckboxes.length > 0 &&
                    itemCheckboxes.every(checkbox => checkbox.checked);

                selectAll.indeterminate =
                    itemCheckboxes.some(checkbox => checkbox.checked) &&
                    !selectAll.checked;
            }

            const formatINR = amount => '₹' + amount.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (subtotalElement) {
                subtotalElement.textContent = formatINR(subtotal);
            }

            if (totalElement) {
                totalElement.textContent = formatINR(total);
            }

            const gstElement = document.getElementById('cartGst');
            if (gstElement) {
                gstElement.textContent = formatINR(gst);
            }

            if (selectedCountElement) {
                selectedCountElement.textContent = selectedCount;
            }
        }

        // Select or deselect every product.
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                itemCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });

                updateCartSummary();
            });
        }

        // Recalculate when individual products are selected or deselected.
        itemCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateCartSummary);
        });

        updateCartSummary();
    });

    //for calculating shipping
    (function () {
        const toggleButton = document.getElementById('toggleShippingForm');
        const form = document.getElementById('shippingCalculator');
        const toggleIcon = document.getElementById('shippingToggleIcon');

        const stateSelect = document.getElementById('shippingState');
        const citySelect = document.getElementById('shippingCity');
        const pincodeInput = document.getElementById('shippingPincode');

        const submitButton = document.getElementById('calculateShippingSubmit');
        const message = document.getElementById('shippingCalculatorMessage');

        if (!toggleButton || !form) return;

        let locationData = null;
        let locationDataLoading = false;

        function showShippingMessage(text, type) {
            message.textContent = text;
            message.className = 'shipping-calculator-message ' + type;
            message.hidden = false;
        }

        function clearShippingMessage() {
            message.textContent = '';
            message.hidden = true;
            message.className = 'shipping-calculator-message';
        }

        function loadShippingStates() {
            if (locationData || locationDataLoading) return;

            locationDataLoading = true;
            stateSelect.disabled = true;

            $.getJSON("<?= base_url('getIndiaData'); ?>")
                .done(function (data) {
                    locationData = data;

                    stateSelect.innerHTML =
                        '<option value="">Select state</option>';

                    if (!data || !Array.isArray(data.states)) {
                        showShippingMessage(
                            'We could not load the state list. Please try again.',
                            'error'
                        );
                        return;
                    }

                    data.states.forEach(function (state) {
                        stateSelect.add(new Option(state.name, state.name));
                    });

                    stateSelect.disabled = false;
                })
                .fail(function () {
                    showShippingMessage(
                        'Unable to load states. Please close and reopen this form to try again.',
                        'error'
                    );
                })
                .always(function () {
                    locationDataLoading = false;
                });
        }

        toggleButton.addEventListener('click', function () {
            const willOpen = form.hidden;

            form.hidden = !willOpen;
            toggleButton.setAttribute('aria-expanded', String(willOpen));

            if (toggleIcon) {
                toggleIcon.className = willOpen
                    ? 'fa-solid fa-chevron-up'
                    : 'fa-solid fa-chevron-down';
            }

            if (willOpen) {
                loadShippingStates();
            }
        });

        stateSelect.addEventListener('change', function () {
            const selectedState = stateSelect.value;

            citySelect.innerHTML =
                '<option value="">Select city</option>';
            citySelect.disabled = true;

            clearShippingMessage();

            if (!selectedState || !locationData) return;

            const state = locationData.states.find(function (item) {
                return item.name === selectedState;
            });

            if (!state || !Array.isArray(state.cities)) return;

            state.cities.forEach(function (city) {
                citySelect.add(new Option(city.name, city.name));
            });

            citySelect.disabled = false;
        });

        pincodeInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
            clearShippingMessage();
        });

        citySelect.addEventListener('change', clearShippingMessage);

        //to calculate shipping cost
        submitButton.addEventListener('click', function () {
            const state = stateSelect.value;
            const city = citySelect.value;
            const pincode = pincodeInput.value.trim();

            clearShippingMessage();

            if (!state) {
                showShippingMessage('Please select a state.', 'error');
                stateSelect.focus();
                return;
            }

            if (!city) {
                showShippingMessage('Please select a city.', 'error');
                citySelect.focus();
                return;
            }

            if (!/^\d{6}$/.test(pincode)) {
                showShippingMessage('Please enter a valid 6-digit pincode.', 'error');
                pincodeInput.focus();
                return;
            }

            // Collect selected product IDs and quantities.
            const selectedItems = Array.from(
                document.querySelectorAll('.cart-item-check:checked')
            ).map(function (checkbox) {
                return {
                    product_id: checkbox.dataset.productId,
                    quantity: parseInt(checkbox.dataset.productQuantity, 10)
                };
            });

            if (selectedItems.length === 0) {
                showShippingMessage('Please select at least one product.', 'error');
                return;
            }

            submitButton.disabled = true;

            $.ajax({
                url: "<?= base_url('calculateShipping'); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    state: state,
                    city: city,
                    pincode: pincode,
                    selected_items: JSON.stringify(selectedItems)
                },
                success: function (response) {
                    if (response.status !== 'success') {
                        showShippingMessage(
                            response.message || 'Unable to calculate shipping.',
                            'error'
                        );
                        return;
                    }

                    const shippingCost = Number(response.data.shipping_cost);
                    const courierName = response.data.courier_name;

                    // Get the current selected-products subtotal.
                    const subtotalText = document.getElementById('cartSubtotal').textContent;
                    const subtotal = Number(subtotalText.replace(/[₹,\s]/g, '')) || 0;

                    // GST is 18% on subtotal + shipping.
                    const gst = (subtotal + shippingCost) * 0.18;

                    // Total includes subtotal, shipping and GST.
                    const total = subtotal + shippingCost + gst;

                    const formatINR = amount => '₹' + amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    // Update shipping fee.
                    document.getElementById('shippingFee').textContent = formatINR(shippingCost);

                    // Update GST.
                    document.getElementById('cartGst').textContent = formatINR(gst);

                    // Update total amount.
                    document.getElementById('cartTotal').textContent = formatINR(total);

                    // Close the dropdown.
                    form.hidden = true;
                    toggleButton.setAttribute('aria-expanded', 'false');

                    if (toggleIcon) {
                        toggleIcon.className = 'fa-solid fa-chevron-down';
                    }

                },
                error: function (xhr) {
                    showShippingMessage(
                        'Unable to contact the server. Please try again.',
                        'error'
                    );
                    console.error(xhr.responseText);
                },
                complete: function () {
                    submitButton.disabled = false;
                }
            });
        });
    })();
    
    //to handle checkout on click
    document.addEventListener('DOMContentLoaded', function () {

        const checkoutButton = document.getElementById('proceedToCheckout');

        if (!checkoutButton) {
            return;
        }

        checkoutButton.addEventListener('click', function () {

            const selectedCheckboxes = document.querySelectorAll(
                '.cart-item-check:checked'
            );

            const selectedProductIds = Array.from(selectedCheckboxes)
                .map(function (checkbox) {
                    return checkbox.dataset.productId;
                })
                .filter(Boolean);

            if (selectedProductIds.length === 0) {
                showAlert(
                    'warning',
                    'No Products Selected',
                    'Please select at least one product to proceed to checkout.'
                );
                return;
            }

            const params = new URLSearchParams();

            selectedProductIds.forEach(function (productId) {
                params.append('product_ids[]', productId);
            });

            window.location.href =
                "<?= base_url('checkout'); ?>?" + params.toString();
        });

    });


    
</script>


<?= $this->endSection(); ?>
