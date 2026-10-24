<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        <?= $page_title ?? 'K-Pop Merch'; ?>
    </title>

    <link 
        rel="icon"
        type="image/png"
        href="<?= base_url('Images/logo.png'); ?>"
    >


    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet"
          href="<?= base_url('css/theme.css'); ?>">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>

<body>

    <!-- HEADER -->

    <?= view('layouts/header'); ?>


    <!-- PAGE CONTENT -->

    <?= $this->renderSection('content'); ?>


    <!-- FOOTER -->

    <?= view('layouts/footer'); ?>
    
    <!-- for alerts -->
    <div id="themeAlert" class="theme-alert">

        <div class="theme-alert-overlay"></div>

        <div class="theme-alert-box">

            <div class="theme-alert-icon">
                <i id="themeAlertIcon"></i>
            </div>

            <h3 id="themeAlertTitle"></h3>

            <p id="themeAlertMessage"></p>

            <div
                id="themeAlertButtons"
                class="theme-alert-buttons">

            </div>

        </div>

    </div>
    
    <!-- custom loader -->
    <div id="siteLoader">
        <div class="site-loader-content">
            <img
                src="<?= base_url('assets/loader.gif'); ?>"
                alt="Loading"
            >
        </div>
    </div>

    <!-- Wishlist Popup -->
    <div id="wishlistModal" class="wishlist-modal">

        <div class="wishlist-modal-overlay" onclick="closeWishlistModal()"></div>

        <div class="wishlist-modal-box">

            <div class="wishlist-modal-header">
                <h3>ADD TO WISHLIST</h3>

                <button type="button" onclick="closeWishlistModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <button
                type="button"
                class="wishlist-create-btn"
                onclick="openCreateWishlist()"
            >
                <i class="fa-solid fa-plus"></i>
                ADD NEW WISHLIST
            </button>

            <div id="wishlistList" class="wishlist-list">
                <!-- Wishlists will load here -->
            </div>

        </div>

    </div>
    
    <!-- added to cart pop-up -->
    <div id="addToCartModal" class="add-to-cart-overlay" style="display: none;">
        <div class="add-to-cart-popup">
            <button type="button"
                    class="add-to-cart-close"
                    onclick="closeAddToCartPopup()"
                    aria-label="Close popup">
                &times;
            </button>

            <div class="add-to-cart-icon">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2>Added to Cart!</h2>
            <p>Your product has been added to your cart.</p>

            <div class="add-to-cart-actions">
                <button type="button"
                        class="add-to-cart-go"
                        onclick="goToCart()">
                    Go to Cart
                </button>

                <button type="button"
                        class="add-to-cart-continue"
                        onclick="closeAddToCartPopup()">
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>
    
</body>



<script>

    //*********************************
    //****** pop-up functions *********
    //*********************************

    // Escape HTML to safely display text
    function escapeHtml(text)
    {
        return $('<div>')
            .text(text || '')
            .html();
    }

    function closeAlert()
    {
        $("#themeAlert").fadeOut(200);
    }

    function showAlert(type, title, message)
    {
        let icon = 'fa-circle-info';

        if(type == 'success')
        {
            icon = 'fa-circle-check';
        }

        if(type == 'error')
        {
            icon = 'fa-circle-xmark';
        }

        if(type == 'warning')
        {
            icon = 'fa-triangle-exclamation';
        }

        $("#themeAlertIcon")
            .attr("class", "fa-solid " + icon);

        $(".theme-alert-icon")
            .removeClass("success error warning info")
            .addClass(type);

        $("#themeAlertTitle").html(title);

        $("#themeAlertMessage").html(message);

        $("#themeAlertButtons").html(
            '<button ' +
            'class="theme-alert-btn theme-alert-confirm" ' +
            'onclick="closeAlert()">' +
            'OK' +
            '</button>'
        );

        $("#themeAlert").fadeIn(200);
    }

    function showConfirm(options)
    {
        $("#themeAlertIcon")
        .attr("class", "fa-solid fa-circle-exclamation"
        );

        $(".theme-alert-icon")
        .removeClass(
            "success error warning info"
        )
        .addClass("warning");

        $("#themeAlertTitle") .html(options.title);

        $("#themeAlertMessage") .html(options.message);

        $("#themeAlertButtons").html(
            '<button '+
            'class="theme-alert-btn theme-alert-cancel" '+
            'onclick="closeAlert()">'+
            'Cancel'+
            '</button>'+
            '<button '+
            'id="themeConfirmBtn" '+
            'class="theme-alert-btn theme-alert-confirm">'+
            (options.confirmText || 'Confirm')+
            '</button>'
        );

        $("#themeAlert") .fadeIn(200);

        $("#themeConfirmBtn")
        .off("click") .on( "click",function(){

                closeAlert();
                if(options.callback)
                {
                    options.callback();
                }
            }
        );
    }
    
    //for input alert
    function showInputAlert(options)
    {
        $("#themeAlertIcon")
            .attr("class", "fa-solid " + (options.icon || "fa-envelope"));

        $(".theme-alert-icon")
            .removeClass("success error warning info")
            .addClass(options.type || "info");

        $("#themeAlertTitle").html(options.title || "");

        $("#themeAlertMessage").html(options.message || "");

        $("#themeAlertButtons").html(
            '<div class="theme-alert-input-wrap">' +
                '<input ' +
                    'type="' + (options.inputType || "text") + '" ' +
                    'id="themeAlertInput" ' +
                    'class="theme-alert-input" ' +
                    'placeholder="' + (options.placeholder || "") + '">' +
            '</div>' +

            '<button ' +
                'id="themeInputAlertBtn" ' +
                'class="theme-alert-btn theme-alert-confirm">' +
                (options.buttonText || "Submit") +
            '</button>'
        );

        $("#themeAlert").fadeIn(200);

        $("#themeAlertInput").focus();

        $("#themeInputAlertBtn")
            .off("click")
            .on("click", function(){

                let value = $("#themeAlertInput").val().trim();

                if(options.callback)
                {
                    options.callback(value);
                }
            });

        $("#themeAlertInput")
            .off("keypress")
            .on("keypress", function(e){

                if(e.which == 13)
                {
                    $("#themeInputAlertBtn").click();
                }
            });
    }
    
    function showLoader()
    {
        $("#siteLoader").fadeIn(100);
    }

    function hideLoader()
    {
        $("#siteLoader").fadeOut(100);
    }
    
    
    //*********************************
    //****** header functions *********
    //*********************************
    
    
    //to open band page
    function openBand(brandId, brandName)
    {
        if(!brandId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByBand"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_id',
                value: brandId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_name',
                value: brandName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
     //to open per category page
    function openCategory(catId, catName)
    {
        if(!catId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByCategory"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'cat_id',
                value: catId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'category_name',
                value: catName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
    
    
    //*********************************
    //****** wishlist functions *******
    //*********************************
       
    
    //add to whishlist
    let wishlistProductId = '';

    function addProductToWishlist(productId)
    {
        if(!productId)
        {
            return;
        }

        wishlistProductId = productId;

        <?php if (!session()->get('customer_id')): ?>

            showAlert(
                "info",
                "Sign In Required",
                "Please sign in to add products to your wishlist."
            );

            setTimeout(function(){
                window.location.href =
                    "<?= base_url('signin'); ?>?redirect=" +
                    encodeURIComponent(window.location.href);
            }, 1200);

            return;

        <?php endif; ?>

        $("#wishlistList").html(
            '<div class="wishlist-loading">Loading wishlists...</div>'
        );

        $("#wishlistModal").fadeIn(200);

        $.post(
            "<?= base_url('getCustomerWishlists'); ?>",
            {},
            function(response)
            {
                if(response.status)
                {
                    let html = '';

                    if(response.wishlists.length > 0)
                    {
                        response.wishlists.forEach(function(wishlist)
                        {
                            html += `
                                <button
                                    type="button"
                                    class="wishlist-option"
                                    onclick="selectWishlist('${wishlist.id}')"
                                >
                                    <i class="fa-regular fa-heart"></i>
                                    <span>${escapeHtml(wishlist.wishlist_name)}</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            `;
                        });
                    }
                    else
                    {
                        html = `
                            <div class="wishlist-empty">
                                You haven't created any wishlists yet.
                            </div>
                        `;
                    }

                    $("#wishlistList").html(html);
                }
                else
                {
                    closeWishlistModal();

                    showAlert(
                        "error",
                        "Unable to Load Wishlists",
                        response.message
                    );
                }
            },
            "json"
        ).fail(function(xhr)
        {
            closeWishlistModal();
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load your wishlists. Please try again."
            );
        });
    }

    function closeWishlistModal()
    {
        $("#wishlistModal").fadeOut(200);
    }

    //to select whishlist from popup
    function selectWishlist(wishlistId)
    {
        if(!wishlistId || !wishlistProductId)
        {
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('addProductToWishlist'); ?>",
            {
                wishlist_id: wishlistId,
                product_id: wishlistProductId
            },
            function(response)
            {
                hideLoader();

                if(response.status)
                {
                    closeWishlistModal();

                    showAlert(
                        "success",
                        "Added to Wishlist",
                        response.message
                    );
                }
                else
                {
                    showAlert(
                        "error",
                        "Unable to Add Product",
                        response.message
                    );
                }
            },
            "json"
        ).fail(function(xhr)
        {
            hideLoader();
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to add this product to your wishlist."
            );
        });
    }

    //to create new whishlist
    function openCreateWishlist()
    {
        showInputAlert({
            type: "info",
            icon: "fa-heart",
            title: "Create New Wishlist",
            message: "Enter a name for your new wishlist.",
            inputType: "text",
            placeholder: "Wishlist name",
            buttonText: "Create",
            callback: function(wishlistName)
            {
                if(wishlistName == '')
                {
                    showAlert(
                        "error",
                        "Name Required",
                        "Please enter a name for your wishlist."
                    );
                    return;
                }

                if(wishlistName.length > 255)
                {
                    showAlert(
                        "error",
                        "Name Too Long",
                        "Wishlist name cannot be longer than 255 characters."
                    );
                    return;
                }

                showLoader();

                $.post(
                    "<?= base_url('createWishlist'); ?>",
                    {
                        wishlist_name: wishlistName,
                        product_id: wishlistProductId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            closeWishlistModal();

                            showAlert(
                                "success",
                                "Wishlist Created & Item Added",
                                response.message
                            );
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Create Wishlist",
                                response.message
                            );
                        }
                    },
                    "json"
                ).fail(function(xhr)
                {
                    hideLoader();
                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to create your wishlist. Please try again."
                    );
                });
            }
        });
    }


    //*********************************
    //****** order functions *********
    //*********************************


    //to add notify 
    function notifyProduct(productId)
    {
        if(!productId)
        {
            return;
        }

        <?php if (!session()->get('customer_id')): ?>

            showAlert(
                "info",
                "Sign In Required",
                "Please sign in to receive back-in-stock notifications."
            );

            setTimeout(function(){
                window.location.href =
                    "<?= base_url('signin'); ?>?redirect=" +
                    encodeURIComponent(window.location.href);
            }, 1200);

            return;

        <?php endif; ?>

        showConfirm({
            title: "Notify Me",
            message: "Would you like to receive an email when this product is back in stock?",
            confirmText: "Yes, Notify Me",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('subscribeProductNotification'); ?>",
                    {
                        product_id: productId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Notification Set",
                                response.message
                            );
                        }
                        else
                        {
                            showAlert(
                                "info",
                                "Notification",
                                response.message
                            );
                        }
                    },
                    "json"
                ).fail(function(xhr)
                {
                    hideLoader();

                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to set up your notification. Please try again."
                    );
                });
            }
        });
    }

   // SAVE NEW ADDRESS
    function saveAccountAddress(isCheck)
    {
        const form = document.getElementById("addAddressForm");

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let addressTitle = $("#addressTitle").val().trim();
        let firstName = $("#addressFirstName").val().trim();
        let lastName = $("#addressLastName").val().trim();
        let contactNo = $("#addressContactNo").val().trim();
        let addressLine1 = $("#addressLine1").val().trim();
        let addressLine2 = $("#addressLine2").val().trim();
        let state = $("#addressState").val();
        let city = $("#addressCity").val();
        let zip = $("#addressZip").val().trim();
        let companyName = $("#addressCompanyName").val().trim();
        let companyGstin = $("#addressCompanyGstin").val().trim();
        let isDefault = $("#addressIsDefault").is(":checked") ? "Y" : "N";

        if (contactNo.length !== 10) {
            showAlert("error", "Invalid Phone Number",
                "Please enter a valid 10-digit phone number.");
            return;
        }

        if (zip.length !== 6) {
            showAlert("error", "Invalid Pincode",
                "Please enter a valid 6-digit pincode.");
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('saveAccountAddress'); ?>",
            {
                address_title: addressTitle,
                first_name: firstName,
                last_name: lastName,
                contact_no: contactNo,
                address_line1: addressLine1,
                address_line2: addressLine2,
                country: "India",
                state: state,
                city: city,
                zip: zip,
                company_name: companyName,
                company_gstin: companyGstin,
                is_default: isDefault
            },
            function(response) {
                hideLoader();

                if (response.status) {
                    closeAddAddress();

                    showAlert(
                        "success",
                        "Address Saved",
                        response.message
                    );

                    if(isCheck){
                        window.location.reload();
                    }else{
                        // Refresh the address page.
                        openAddresses();
                    }
                } else {
                    showAlert(
                        "error",
                        "Unable to Save Address",
                        response.message || "Please try again."
                    );
                }
            },
            "json"
        ).fail(function(xhr) {
            hideLoader();
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to save your address. Please try again."
            );
        });
    }


    //*********************************
    //****** cart functions *********
    //*********************************


    //to add prduct to cart      
    function addProductToCart(productId, quantity = 1)
    {
        if (!productId) return;

        quantity = parseInt(quantity, 10);

        if (!Number.isInteger(quantity) || quantity < 1 || quantity > 25) {
            showAlert("error", "Invalid Quantity", "Please select a quantity between 1 and 25.");
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('addProductToCart'); ?>",
            {
                product_id: productId,
                product_quantity: quantity
            },
            function(response) {
                hideLoader();

                if (response.status) {
                    if (typeof response.cartCount !== 'undefined') {
                        $(".cart-badge").text(response.cartCount);
                    }

                    showAddToCartPopup();
                } else {
                    showAlert(
                        "error",
                        "Unable to Add Product",
                        response.message || "Unable to add this product to your cart."
                    );
                }
            },
            "json"
        ).fail(function(xhr) {
            hideLoader();
            console.error(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to add this product to your cart. Please try again."
            );
        });
    }

    function showAddToCartPopup()
    {
        $("#addToCartModal").fadeIn(200);
    }

    function closeAddToCartPopup()
    {
        $("#addToCartModal").fadeOut(200);
    }

    function goToCart()
    {
        window.location.href = "<?= base_url('cart'); ?>";
    }


</script>

</html>