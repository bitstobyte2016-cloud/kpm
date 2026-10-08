<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<section class="account-page">

    <div class="container">

        <!-- BREADCRUMB -->
        <div class="search-breadcrumb account-breadcrumb">

            <a href="<?= base_url(); ?>">
                Home
            </a>

            <span>/</span>

            <span>
                My Account
            </span>

        </div>


        <div class="account-layout">

            <!-- LEFT ACCOUNT SIDEBAR -->
            <aside class="account-sidebar">

                <a
                    href="javascript:void(0);"
                    class="account-menu-item active"
                    onclick="openAccountHome()"
                >
                    <i class="fa-solid fa-house"></i>
                    <span>Account Home</span>
                </a>

                <a href="javascript:void(0);"
                    class="account-menu-item"
                    onclick="openAddresses()">

                     <i class="fa-solid fa-location-dot"></i>
                     <span>Addresses</span>

                 </a>

                <a
                    href="javascript:void(0);"
                    class="account-menu-item"
                    onclick="openOrders()"
                >
                    <i class="fa-solid fa-box-open"></i>
                    <span>Orders</span>
                </a>

                <a
                    href="javascript:void(0);"
                    class="account-menu-item wishlist-menu-item"
                    onclick="openWishlist()"
                >
                    <i class="fa-regular fa-heart"></i>
                    <span>Wishlists</span>
                </a>

                <div class="account-menu-divider"></div>

                <a
                    href="javascript:void(0);"
                    class="account-menu-item logout-item"
                    onclick="confirmLogout()"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>

            </aside>

            
            <!-- RIGHT ACCOUNT CONTENT -->
            <main class="account-content">

                <?= $this->include($account_view); ?>

            </main>

        </div>

    </div>

</section>

<div id="editProfileModalContainer"></div>
<div id="addAddressModalContainer"></div>

<!-- alert for coupon copied -->
<div id="couponCopyToast" class="coupon-copy-toast">
    <i class="fa-solid fa-circle-check"></i>
    <span>Coupon code copied</span>
</div>

<!-- for account home -->
<script>

    //open home page
    function openAccountHome()
    {
        $.post(
            "<?= base_url('accountHomeContent'); ?>",
            {},
            function(response)
            {
                $(".account-content").html(response);

                $(".account-menu-item")
                    .removeClass("active");

                $(".account-menu-item")
                    .has("i.fa-house")
                    .addClass("active");
            }
        )
        .fail(function(xhr)
        {
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load your account home. Please try again."
            );
        });
    }
    
    //to logout user
    function confirmLogout()
    {
        showConfirm({
            title: "Logout from your account?",
            message: "You will need to sign in again to access your account.",
            confirmText: "Yes, Logout",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('logout'); ?>",
                    {},
                    function(response)
                    {
                        if(response.status)
                        {
                            window.location.href = "<?= base_url(); ?>";
                        }
                        else
                        {
                            hideLoader();

                            showAlert(
                                "error",
                                "Logout Failed",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();

                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to logout. Please try again."
                    );
                });
            }
        });
    }

    //for password show/hide toggle
    function togglePassword(inputId, button)
    {
        let input = $("#" + inputId);
        let icon = $(button).find("i");

        if(input.attr("type") === "password"){

            input.attr("type", "text");

            icon
                .removeClass("fa-eye")
                .addClass("fa-eye-slash");

        } else {

            input.attr("type", "password");

            icon
                .removeClass("fa-eye-slash")
                .addClass("fa-eye");

        }
    }
    
    //to open edit name pop-up
    function openEditProfile()
    {
        $("#siteLoader").fadeIn(200);

        $.post(
            "<?= base_url('editProfile'); ?>",
            {
                first_name: "<?= $customer['first_name']; ?>",
                last_name: "<?= $customer['last_name']; ?>"
            },
            function(response){
                $("#siteLoader").fadeOut(200);

                $("#editProfileModalContainer").html(response);
                $("#editProfileModal").fadeIn(200);
            }
        ).fail(function(xhr){
            $("#siteLoader").fadeOut(200);
            console.log(xhr.responseText);
            showAlert("error","Something Went Wrong","Unable to open edit profile.");
        });
    }
    
    //to update new password
    function changePassword()
    {
        let oldPassword = $("#oldPassword").val().trim();
        let newPassword = $("#newPassword").val().trim();
        let confirmPassword = $("#confirmPassword").val().trim();

        if(oldPassword == '')
        {
            showAlert(
                "error",
                "Old Password Required",
                "Please enter your old password."
            );
            return;
        }

        if(newPassword == '')
        {
            showAlert(
                "error",
                "New Password Required",
                "Please enter your new password."
            );
            return;
        }

        if(confirmPassword == '')
        {
            showAlert(
                "error",
                "Confirm Password",
                "Please confirm your new password."
            );
            return;
        }

        if(newPassword != confirmPassword)
        {
            showAlert(
                "error",
                "Passwords Do Not Match",
                "New password and confirm password must be the same."
            );
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('changePassword'); ?>",
            {
                old_password: oldPassword,
                new_password: newPassword
            },
            function(response)
            {
                hideLoader();

                if(response.status)
                {
                    $("#oldPassword").val('');
                    $("#newPassword").val('');
                    $("#confirmPassword").val('');

                    showAlert(
                        "success",
                        "Password Changed",
                        response.message
                    );
                }
                else
                {
                    showAlert(
                        "error",
                        "Unable to Change Password",
                        response.message
                    );
                }
            },
            "json"
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to change your password. Please try again."
            );
        });
    }
    
    //to redeem points
    function redeemReward(points, amount)
    {
        showConfirm({
            title: "Redeem Reward",
            message: "Redeem " + points.toLocaleString() + " points for ₹" + amount + " to store credit?",
            confirmText: "Redeem",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('redeemReward'); ?>",
                    {
                        points: points,
                        amount: amount
                    },
                    function(response)
                    {
                        hideLoader();
                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Reward Redeemed",
                                response.message
                            );
                            setTimeout(function(){
                                location.reload();
                            }, 1000);
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Redeem",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();
                    console.log(xhr.responseText);
                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to redeem your reward. Please try again."
                    );
                });
            }
        });
    }
    
    //to copy coupon code
    function copyCouponCode(couponCode)
    {
        navigator.clipboard.writeText(couponCode)
        .then(function()
        {
            $("#couponCopyToast").stop(true, true).fadeIn(200);

            setTimeout(function(){
                $("#couponCopyToast").fadeOut(200);
            }, 1800);
        })
        .catch(function()
        {
            let tempInput = $("<input>");
            $("body").append(tempInput);

            tempInput.val(couponCode).select();
            document.execCommand("copy");

            tempInput.remove();

            $("#couponCopyToast").stop(true, true).fadeIn(200);

            setTimeout(function(){
                $("#couponCopyToast").fadeOut(200);
            }, 1800);
        });
    }

</script>

<!-- for account addresses -->
<script>
    
    //to open address page
    function openAddresses()
    {
        $.post(
            "<?= base_url('accountAddresses'); ?>",
            {},
            function(response)
            {
                $(".account-content").html(response);

                $(".account-menu-item")
                    .removeClass("active");

                $(".account-menu-item")
                    .has("i.fa-location-dot")
                    .addClass("active");
            }
        )
        .fail(function(xhr)
        {
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load your addresses. Please try again."
            );
        });
    }

    // OPEN ADD ADDRESS POPUP
    function openAddAddress()
    {
        showLoader();

        $.post(
            "<?= base_url('accountAddAddress'); ?>",
            {},
            function(response)
            {
                hideLoader();

                $("body").append(response);

                $("#addAddressModal").fadeIn(200);
            }
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to open the address form. Please try again."
            );
        });
    }

    // OPEN EDIT ADDRESS POPUP
    function editAddress(addressId)
    {
        if(!addressId)
        {
            showAlert(
                "error",
                "Invalid Address",
                "Unable to open this address."
            );

            return;
        }

        showLoader();

        $.post(
            "<?= base_url('editAccountAddress'); ?>",
            {
                address_id: addressId
            },
            function(response)
            {
                hideLoader();

                $("body").append(response);

                $("#addAddressModal").fadeIn(200);
            }
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to open the address for editing. Please try again."
            );
        });
    }

    // UPDATE ADDRESS
    function updateAccountAddress(addressId)
    {
        if(!addressId)
        {
            showAlert(
                "error",
                "Invalid Address",
                "Unable to update this address."
            );

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

        let isDefault = $("#addressIsDefault").is(":checked")
            ? "Y"
            : "N";


        // Required fields
        if(addressTitle == '')
        {
            showAlert(
                "error",
                "Address Title Required",
                "Please enter an address title."
            );

            return;
        }

        if(firstName == '')
        {
            showAlert(
                "error",
                "First Name Required",
                "Please enter your first name."
            );

            return;
        }

        if(lastName == '')
        {
            showAlert(
                "error",
                "Last Name Required",
                "Please enter your last name."
            );

            return;
        }

        if(contactNo == '')
        {
            showAlert(
                "error",
                "Phone Number Required",
                "Please enter your phone number."
            );

            return;
        }

        if(contactNo.length != 10)
        {
            showAlert(
                "error",
                "Invalid Phone Number",
                "Please enter a valid 10-digit phone number."
            );

            return;
        }

        if(addressLine1 == '')
        {
            showAlert(
                "error",
                "Address Required",
                "Please enter your address."
            );

            return;
        }

        if(state == '')
        {
            showAlert(
                "error",
                "State Required",
                "Please select your state."
            );

            return;
        }

        if(city == '')
        {
            showAlert(
                "error",
                "City Required",
                "Please select your city."
            );

            return;
        }

        if(zip == '')
        {
            showAlert(
                "error",
                "Pincode Required",
                "Please enter your pincode."
            );

            return;
        }

        if(zip.length != 6)
        {
            showAlert(
                "error",
                "Invalid Pincode",
                "Please enter a valid 6-digit pincode."
            );

            return;
        }


        showLoader();

        $.post(
            "<?= base_url('updateAccountAddress'); ?>",
            {
                address_id: addressId,
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
            function(response)
            {
                hideLoader();

                if(response.status)
                {
                    closeAddAddress();

                    showAlert(
                        "success",
                        "Address Updated",
                        response.message
                    );

                    // Refresh address list
                    openAddresses();
                }
                else
                {
                    showAlert(
                        "error",
                        "Unable to Update Address",
                        response.message
                    );
                }
            },
            "json"
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to update your address. Please try again."
            );
        });
    }

    // SET DEFAULT ADDRESS
    function setDefaultAddress(addressId)
    {
        if(!addressId)
        {
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('setDefaultAddress'); ?>",
            {
                address_id: addressId
            },
            function(response)
            {
                hideLoader();

                if(response.status)
                {
                    showAlert(
                        "success",
                        "Default Address Updated",
                        response.message
                    );

                    openAddresses();
                }
                else
                {
                    showAlert(
                        "error",
                        "Something Went Wrong",
                        response.message
                    );
                }
            },
            "json"
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to set the default address. Please try again."
            );
        });
    }

    // DELETE ADDRESS
    function deleteAddress(addressId)
    {
        if(!addressId)
        {
            return;
        }

        showConfirm({
            title: "Delete Address?",
            message: "Are you sure you want to permanently delete this address?",
            confirmText: "Yes, Delete",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('deleteAccountAddress'); ?>",
                    {
                        address_id: addressId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Address Deleted",
                                response.message
                            );

                            openAddresses();
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Delete Address",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();

                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to delete this address. Please try again."
                    );
                });
            }
        });
    }

</script>

<!-- for account orders -->
<script>

    //to open orders page
    function openOrders()
    {
        $.post(
            "<?= base_url('accountOrders'); ?>",
            {},
            function(response)
            {
                $(".account-content").html(response);

                $(".account-menu-item")
                    .removeClass("active");

                $(".account-menu-item")
                    .has("i.fa-box-open")
                    .addClass("active");

                currentOrderPage = 1;

                renderOrderPage(1);
            }
        )
        .fail(function(xhr)
        {
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load your orders. Please try again."
            );
        });
    }
    
    //filter order on status 
    $(document).on(
        "change",
        "#orderStatusFilter",
        function()
        {
            let selectedStatus = $(this).val();

            let visibleOrders = 0;

            $(".account-order-card").each(function()
            {
                let orderStatus = $(this).data("order-status");

                if(
                    selectedStatus === "all" ||
                    orderStatus === selectedStatus
                )
                {
                    $(this).show();

                    visibleOrders++;
                }
                else
                {
                    $(this).hide();
                }
            });


            //show / hide filtered empty message
            let filteredEmpty = $("#filteredOrdersEmpty");

            if(visibleOrders === 0)
            {
                if(filteredEmpty.length === 0)
                {
                    $(".account-orders-list").append(`
                        <div
                            id="filteredOrdersEmpty"
                            class="account-orders-empty"
                        >
                            <i class="fa-solid fa-box-open"></i>

                            <h3>
                                No Orders Found
                            </h3>

                            <p>
                                You don't have any orders with this status.
                            </p>
                        </div>
                    `);
                }
            }
            else
            {
                filteredEmpty.remove();
            }
        }
    );
    
    //pagination
    const ordersPerPage = 5;
    let currentOrderPage = 1;

    function renderOrderPage(page)
    {
        currentOrderPage = page;

        let orders = $(".account-content .order-item");

        orders.hide();

        let start = (page - 1) * ordersPerPage;
        let end = start + ordersPerPage;

        orders
            .slice(start, end)
            .show();

        $(".account-content .order-page-btn")
            .removeClass("active");

        $('.account-content .order-page-btn[data-page="' + page + '"]')
            .addClass("active");

        updateOrderPaginationButtons();
    }

    function updateOrderPaginationButtons()
    {
        let totalPages =
            $(".account-content .order-page-btn").length;

        $(".account-content #orderPrev")
            .prop(
                "disabled",
                currentOrderPage <= 1
            );

        $(".account-content #orderNext")
            .prop(
                "disabled",
                currentOrderPage >= totalPages
            );
    }

    $(document).on(
        "click",
        ".account-content .order-page-btn",
        function()
        {
            renderOrderPage(
                parseInt($(this).data("page"))
            );
        }
    );

    $(document).on(
        "click",
        ".account-content #orderPrev",
        function()
        {
            if(currentOrderPage > 1)
            {
                renderOrderPage(
                    currentOrderPage - 1
                );
            }
        }
    );

    $(document).on(
        "click",
        ".account-content #orderNext",
        function()
        {
            let totalPages =
                $(".account-content .order-page-btn").length;

            if(currentOrderPage < totalPages)
            {
                renderOrderPage(
                    currentOrderPage + 1
                );
            }
        }
    );
    
    //to open individual order details
    function viewOrder(orderId)
    {
        if(!orderId)
        {
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('orderDetails'); ?>",
            {
                order_id: orderId
            },
            function(response)
            {
                hideLoader();

                if(!response)
                {
                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to load order details."
                    );

                    return;
                }

                //replace account content with order details
                $(".account-content").html(response);

                //keep Orders menu active
                $(".account-menu-item")
                    .removeClass("active");

                $(".account-menu-item")
                    .has("i.fa-box-open")
                    .addClass("active");

                
            }
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load order details. Please try again."
            );
        });
    }
    
    //to toggle the shipping section in order details
    function toggleShippingDetails()
    {
        $("#shippingDetailsContent").slideToggle(200);

        $("#shippingArrow").toggleClass(
            "shipping-arrow-up"
        );
    }
    
    //to toggle order chat
    function toggleOrderChat()
    {
        const content = $("#order-chat-content");
        const arrow = $("#order-chat-arrow");

        if (content.is(":visible"))
        {
            content.slideUp(200);

            arrow.removeClass(
                "order-chat-arrow-up"
            );
        }
        else
        {
            content.slideDown(200);

            arrow.addClass(
                "order-chat-arrow-up"
            );
        }
    }
    
    //to go back to orders list
    function backToOrders()
    {
        openOrders();
    }
    
    //to download invoice
    function downloadInvoice(orderId)
    {
        if(!orderId)
        {
            return;
        }

        showLoader();

        //get invoice ID
        $.post(
            "<?= base_url('getInvoiceID'); ?>",
            {
                order_id: orderId
            },
            function(response)
            {
                if(
                    !response ||
                    (
                        response.status !== "existing" &&
                        response.status !== "created"
                    )
                )
                {
                    hideLoader();

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        response && response.message
                            ? response.message
                            : "Unable to generate invoice."
                    );

                    return;
                }

                //copy existing invoice data
                let data = JSON.parse(
                    JSON.stringify(invoiceData)
                );

                //add invoice information
                data.invoice_id = response.invoice_id;
                data.invoice_date = response.invoice_date;

                //send data to generateInvoice
                $.ajax({
                    url: "<?= base_url('generateInvoice'); ?>",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),

                    xhrFields: {
                        responseType: "blob"
                    },

                    success: function(pdf)
                    {
                        hideLoader();

                        //create PDF URL
                        let pdfUrl = URL.createObjectURL(
                            pdf
                        );

                        //open PDF in new tab
                        let invoiceWindow = window.open(
                            pdfUrl,
                            "_blank"
                        );

                        //popup was blocked
                        if(!invoiceWindow)
                        {
                            showAlert(
                                "error",
                                "Popup Blocked",
                                "Please allow popups for this website to view your invoice."
                            );
                        }
                    },

                    error: function(xhr)
                    {
                        hideLoader();

                        console.log(
                            "GENERATE INVOICE ERROR:"
                        );

                        console.log(
                            "STATUS:",
                            xhr.status
                        );

                        console.log(
                            "RESPONSE:",
                            xhr.responseText
                        );

                        showAlert(
                            "error",
                            "Something Went Wrong",
                            "Unable to generate invoice. Please try again."
                        );
                    }
                });

            },
            "json"
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(
                "GET INVOICE ID ERROR:"
            );

            console.log(
                xhr.responseText
            );

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to create invoice. Please try again."
            );
        });
    }
    
    //to send new order message
    function sendOrderMessage(orderId)
    {
        if (!orderId) {
            return;
        }

        let message = $.trim( $('#orderChatMessage').val());

        if (!message) {
            return;
        }


        let sendButton = $('.order-chat-send-btn');
        sendButton.prop('disabled', true);

        $.post(
            "<?= base_url('sendOrderMessage'); ?>",
            {
                order_id: orderId,
                message: message
            },
            function(response)
            {
                if (
                    !response ||
                    response.status !== 'success'
                ) {

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        response && response.message
                            ? response.message
                            : "Unable to send message."
                    );

                    sendButton.prop('disabled', false);

                    return;
                }

                //clear textbox
                $('#orderChatMessage').val('');

                //reload order messages
                loadOrderMessages(orderId);

                sendButton.prop('disabled', false);

            },
            "json"
        )
        .fail(function(xhr)
        {
            console.log("SEND ORDER MESSAGE ERROR:");
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to send message. Please try again."
            );

            sendButton.prop('disabled', false);
        });
    }
    
    function loadOrderMessages(orderId)
    {
        if (!orderId) {
            return;
        }


        $.post(
            "<?= base_url('getOrderMessages'); ?>",
            {
                order_id: orderId
            },
            function(response)
            {
                if (
                    !response ||
                    response.status !== 'success'
                ) {
                    return;
                }


                let html = '';


                if (
                    response.messages &&
                    response.messages.length > 0
                ) {

                    response.messages.forEach(function(message)
                    {

                        let isUser =
                            parseInt(message.sender_id) !== 0;


                        html += `
                            <div class="order-chat-message ${isUser ? 'order-chat-user' : 'order-chat-support'}">

                                <div class="order-chat-bubble">

                                    <div class="order-chat-sender">
                                        ${isUser ? 'You:' : 'Support:'}
                                    </div>

                                    <div class="order-chat-text">
                                        ${escapeHtml(message.order_message).replace(/\n/g, '<br>')}
                                    </div>

                                    <div class="order-chat-date">
                                        ${message.formatted_date}
                                    </div>

                                </div>

                            </div>
                        `;
                    });

                } else {

                    html = `
                        <div class="order-chat-empty">
                            No messages yet.
                        </div>
                    `;
                }


                $('#orderChatMessages').html(html);

            },
            "json"
        )
        .fail(function(xhr)
        {
            console.log("GET ORDER MESSAGES ERROR:");
            console.log(xhr.responseText);
        });
    }
    
    //escape helper
    function escapeHtml(text)
    {
        return $('<div>')
            .text(text || '')
            .html();
    }
    
    //to cancel order
    function cancelOrder(orderId)
    {
        if (!orderId) {
            return;
        }

        showConfirm({
            title: "Cancel Order",
            message: "Are you sure you want to cancel this order?",
            confirmText: "Yes, Cancel Order",

            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('cancelOrder'); ?>",
                    {
                        order_id: orderId
                    },
                    function(response)
                    {
                        if (
                            !response ||
                            response.status !== "success"
                        ) {
                            hideLoader();
                            showAlert(
                                "error",
                                "Unable to Cancel Order",
                                response && response.message
                                    ? response.message
                                    : "Unable to cancel this order. Please try again." );
                            return;
                        }


                        //successful, call email
                        emailCancelOrder(orderId, response);

                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();

                    console.log(
                        "CANCEL ORDER ERROR:"
                    );

                    console.log(
                        xhr.responseText
                    );

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to cancel the order. Please try again."
                    );
                });
            }
        });
    }
    
    //to send email for cancel order
    function emailCancelOrder(orderId, cancelResponse)
    {
        if (!orderId) {
            hideLoader();
            return;
        }

        let emailData = JSON.parse(
            JSON.stringify(cancelOrderEmailData)
        );

        emailData.order.order_status = "cancelled";
        emailData.customer_email = customerEmail;

        //add cancellation information
        emailData.store_credit_refunded = cancelResponse.store_credit_refunded || "0.00";

        $.ajax({
            url: "<?= base_url('emailCancelOrder'); ?>",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(emailData),

            success: function(response)
            {
                hideLoader();
                location.reload();
            },

            error: function(xhr)
            {
                console.log(xhr.responseText);
                
                 showAlert(
                        "error",
                        "Something Went Wrong",
                        "Order cancelled but unable to send email."
                    );
            }
        });
    }

</script>


<!-- for account wishlist -->
<script>
    //to open wishlist page
    function openWishlist()
    {
        showLoader();

        //set active menu
        $(".account-menu-item").removeClass("active");
        $(".wishlist-menu-item").addClass("active");

        $.post(
            "<?= base_url('accountWishlists'); ?>",
            {},
            function(response)
            {
                hideLoader();
                $(".account-content").html(response);
            }
        )
        .fail(function(xhr)
        {
            hideLoader();
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load your wishlists. Please try again."
            );
        });
    }
    
    //to open wishlist detail page
    function openWishlistDetail(wishlistId)
    {
        if(!wishlistId)
        {
            return;
        }

        showLoader();

        $.post(
            "<?= base_url('accountWishlistDetail'); ?>",
            {
                wishlist_id: wishlistId
            },
            function(response)
            {
                hideLoader();

                $(".account-content").html(response);
            }
        )
        .fail(function(xhr)
        {
            hideLoader();

            console.log(xhr.responseText);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to load this wishlist. Please try again."
            );
        });
    }
    
    //to rename whishlist
    function renameWishlist(wishlistId)
    {
        if(!wishlistId)
        {
            return;
        }

        showInputAlert({
            type: "info",
            icon: "fa-pen",
            title: "Rename Wishlist",
            message: "Enter a new name for your wishlist.",
            inputType: "text",
            placeholder: "Wishlist name",
            buttonText: "Save",
            callback: function(newName)
            {
                if(newName == '')
                {
                    showAlert(
                        "error",
                        "Name Required",
                        "Please enter a name for your wishlist."
                    );

                    return;
                }

                if(newName.length > 255)
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
                    "<?= base_url('renameWishlist'); ?>",
                    {
                        wishlist_id: wishlistId,
                        wishlist_name: newName
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Wishlist Renamed",
                                response.message
                            );

                            setTimeout(function(){
                                openWishlistDetail(wishlistId);
                            }, 800);
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Rename",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();

                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to rename your wishlist. Please try again."
                    );
                });
            }
        });
    }
    
    //create new whishlist 
    function createWishlist()
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
                        wishlist_name: wishlistName
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Wishlist Created",
                                response.message
                            );

                            setTimeout(function(){
                                openWishlist();
                            }, 800);
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
                )
                .fail(function(xhr)
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
    
    //to delete whishlist
    function deleteWishlist(wishlistId)
    {
        if(!wishlistId)
        {
            return;
        }

        showConfirm({
            title: "Delete Wishlist",
            message: "Are you sure you want to delete this wishlist? All products saved in this wishlist will also be removed.",
            confirmText: "Yes, Delete",

            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('deleteWishlist'); ?>",
                    {
                        wishlist_id: wishlistId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Wishlist Deleted",
                                response.message
                            );

                            setTimeout(function(){
                                openWishlist();
                            }, 800);
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Delete",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();

                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to delete your wishlist. Please try again."
                    );
                });
            }
        });
    }
    
    //to delete whishlist item
    function deleteWishlistItem(itemId, wishlistId)
    {
        if(!itemId) return;

        showConfirm({
            title: "Delete Wishlist Item",
            message: "Are you sure you want to remove this item from your wishlist?",
            confirmText: "Delete",
            cancelText: "Cancel",
            type: "warning",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('deleteWishlistItem'); ?>",
                    {
                        item_id: itemId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Item Removed",
                                response.message
                            );

                            setTimeout(function(){
                                openWishlistDetail(wishlistId);
                            }, 800);
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Remove Item",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();
                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to remove this item from your wishlist. Please try again."
                    );
                });
            }
        });
    }
    
    //mark whishlist item as fulfilled
    function markWishlistFulfilled(itemId, wishlistId)
    {
        if(!itemId || !wishlistId) return;

        showConfirm({
            title: "Mark as Fulfilled",
            message: "Have you already acquired this item?",
            confirmText: "Yes, Mark Fulfilled",
            cancelText: "Cancel",
            type: "info",
            callback: function()
            {
                showLoader();

                $.post(
                    "<?= base_url('markWishlistFulfilled'); ?>",
                    {
                        item_id: itemId
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            showAlert(
                                "success",
                                "Item Fulfilled",
                                response.message
                            );

                            setTimeout(function(){
                                openWishlistDetail(wishlistId);
                            }, 800);
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Update Item",
                                response.message
                            );
                        }
                    },
                    "json"
                )
                .fail(function(xhr)
                {
                    hideLoader();
                    console.log(xhr.responseText);

                    showAlert(
                        "error",
                        "Something Went Wrong",
                        "Unable to mark this item as fulfilled. Please try again."
                    );
                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>