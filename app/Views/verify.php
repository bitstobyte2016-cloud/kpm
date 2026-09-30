<div
    id="verifyModal"
    class="verify-modal"
    style="display:none;"
>

    <div class="verify-overlay"></div>

    <div class="verify-popup">

        <button
            type="button"
            class="verify-close"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="verify-header">
            <div class="verify-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h2>
                    Verify Your Account
                </h2>
                <p>
                    Please enter the OTPs sent to your email to continue.
                </p>
            </div>
        </div>


        <!-- EMAIL OTP -->
        <div class="verify-section">
            <div class="verify-title">
                <i class="fa-regular fa-envelope"></i>
                <div>
                    <strong>
                        OTP sent to your email
                    </strong>
                    <span id="verifyEmailText"></span>
                </div>
                <a
                    href="javascript:void(0)"
                    id="resendEmailOtp"
                >
                    Resend OTP
                </a>
            </div>

            <div class="otp-group">
                <input maxlength="1" class="otp-box email-otp">
                <input maxlength="1" class="otp-box email-otp">
                <input maxlength="1" class="otp-box email-otp">
                <input maxlength="1" class="otp-box email-otp">
                <input maxlength="1" class="otp-box email-otp">
                <input maxlength="1" class="otp-box email-otp">
            </div>
        </div>

        <button
            type="button"
            id="verifyAccountBtn"
            class="btn-primary verify-btn">
            Verify & Continue
        </button>
    </div>
</div>


<script>

    // Put email and mobile values
    $("#verifyEmailText").text("<?= $email ?>");


    // Auto move to next OTP box
    $(".otp-box").on( "input", function(){
            if($(this).val().length == 1)
            {
                $(this) .next(".otp-box").focus();
            }
        }
    );


    // Backspace support
    $(".otp-box").on( "keydown",  function(e){
            if(
                e.key === "Backspace" &&
                $(this).val() === ""
            )
            {
                $(this) .prev(".otp-box") .focus();
            }
        }
    );


    // Close popup
    $(document).on( "click", ".verify-close,.verify-overlay", function(){

            showConfirm({
                title:'Cancel Verification?',
                message:'You will need to request a new OTP if you leave this page.',
                confirmText:'Close',
                callback:function(){
                    $("#verifyModal") .fadeOut(200);
                }
            });
        }
    );


    // Verify button
    $("#verifyAccountBtn").click(function(){

        let emailOtp = '';

        $(".email-otp").each(function(){
            emailOtp += $(this).val();
        });

        if(emailOtp.length != 6)
        {
            showAlert(
                "warning",
                "Email OTP Required",
               "Please enter the complete email OTP."
            );

            return;
        }

        showLoader();
        $.post("<?= base_url('verifyOtp'); ?>", {
                e_otp : emailOtp
            },function(response){

                if(response.success)
                {
                    hideLoader();
                    // next step: redirect back to register page
                   $("#verifyModal").fadeOut(200, function(){
                        $("#verifyModalContainer").empty();
                        registerUser();
                    });
                }
                else
                {
                    hideLoader();
                    showAlert(
                        "error",
                        "Verification Failed",
                        response.message
                    );

                    $(".otp-box").val('');
                    $(".email-otp:first").focus();
                }
            },
            "json"
        ).fail(function(){
            hideLoader();
            showAlert(
                "error",
                "Error occurred.",
                "Something went wrong"
            );
        });
    });


    // Resend Email OTP
    $("#resendEmailOtp").click(function(){

        showLoader();

        $.post( "<?= base_url('resendE'); ?>", {},

            function(response){
                hideLoader();
                if(response.success)
                {
                    showAlert(
                        "success",
                        "OTP Sent",
                        "A new OTP has been sent to your email."
                    );
                }
            },
            "json"
        ) .fail(function(){
            hideLoader();
        });
    });



    // Paste OTP into first email box
    $(".email-otp:first").on( "paste", function(e){

            e.preventDefault();
            let pasted =(e.originalEvent || e) .clipboardData .getData("text") .trim();

            if(pasted.length == 6)
            {
                $(".email-otp").each(function(i){
                    $(this).val(pasted[i] );
                });
            }
        }
    );


</script>