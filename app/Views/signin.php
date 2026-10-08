<?php
    $redirectUrl = $_GET['redirect'] ?? '';
    if(empty($redirectUrl))
    {
        $redirectUrl = base_url();
    }
?>

<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<section class="login-page">

    <div class="container">

        <div class="search-breadcrumb">

            <a href="<?= base_url(); ?>">
                Home
            </a>

            <span>/</span>

            <span>
                Login
            </span>

        </div>

        <div class="login-wrapper">

            <!-- LEFT -->

            <div class="login-info-card">
                <div class="login-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <h2>
                    NEW HERE?
                </h2>

                <p>
                    Create an account to enjoy a better shopping experience with K-Pop Merch.
                </p>

                <div class="login-divider"></div>

                <ul class="login-benefits">
                    <li>Faster checkout process</li>
                    <li>Track your orders easily</li>
                    <li>Save items to your wishlist</li>
                    <li>Manage multiple shipping addresses</li>
                    <li>Get exclusive updates & offers</li>

                </ul>

                <a
                    href="<?= base_url('register'); ?>"
                    class="auth-btn">
                    CREATE AN ACCOUNT
                </a>

            </div>


            <!-- RIGHT -->

            <div class="login-form-card">
                <div class="login-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <h2>
                    WELCOME BACK
                </h2>

                <p>
                    Sign in to continue to your account
                </p>

               <form id="loginForm">

                    <div class="form-group">
                        <input
                            type="email"
                            id="loginEmail"
                            name="email"
                            placeholder="EMAIL ADDRESS"
                        >
                    </div>

                    <div class="form-group">
                        <input
                            type="password"
                            id="loginPassword"
                            name="password"
                            placeholder="PASSWORD"
                        >
                    </div>

                    <div class="forgot-password">
                        <a href="javascript:void(0);" onclick="openForgotPassword()">
                            Forgot your password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="auth-btn"
                    >
                        SIGN IN
                    </button>
                </form>
            </div>

        </div>

    </div>

</section>

<script>
    $("#loginForm").on("submit", function(e){
        e.preventDefault();

        let email = $("#loginEmail").val().trim();
        let password = $("#loginPassword").val();

        if(email === ""){
            showAlert("error", "Missing Email", "Please enter your email address.");
            return;
        }

        if(password === ""){
            showAlert("error", "Missing Password", "Please enter your password.");
            return;
        }

        $("#siteLoader").fadeIn(200);

        $.post(
            "<?= base_url('login'); ?>",
            {
                email: email,
                password: password,
                redirect: "<?= esc($redirectUrl); ?>"
            },
            function(response){

                $("#siteLoader").fadeOut(200);

                if(response.status){

                    setTimeout(function(){ window.location.href = response.redirect; },500);

                } else {

                    showAlert(
                        "error",
                        "Sign In Failed",
                        response.message
                    );

                }

            },
            "json"
        ).fail(function(xhr){

            $("#siteLoader").fadeOut(200);

            console.log(xhr);

            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to sign you in. Please try again."
            );

        });

    });
    
    //forgot password
    function openForgotPassword()
    {
        showInputAlert({
            type: "info",
            icon: "fa-envelope",
            title: "Forgot Password?",
            message: "No worries! Enter your email address and we'll send you a new temporary password.",
            inputType: "email",
            placeholder: "Enter your email address",
            buttonText: "Submit",
            callback: function(email)
            {
                if(email == '')
                {
                    showAlert(
                        "error",
                        "Email Required",
                        "Please enter your email address."
                    );

                    return;
                }

                showLoader();

                $.post(
                    "<?= base_url('forgotPassword'); ?>",
                    {
                        email: email
                    },
                    function(response)
                    {
                        hideLoader();

                        if(response.status)
                        {
                            closeAlert();

                            showAlert(
                                "success",
                                "Password Reset email sent.",
                                response.message
                            );
                        }
                        else
                        {
                            showAlert(
                                "error",
                                "Unable to Reset Password",
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
                        "Unable to reset your password. Please try again."
                    );
                });
            }
        });
    }
    
</script>


<?= $this->endSection(); ?>