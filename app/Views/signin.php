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
                            name="email"
                            placeholder="EMAIL ADDRESS" >

                    </div>

                    <div class="form-group">
                        <input
                            type="password"
                            name="password"
                            placeholder="PASSWORD">
                    </div>

                    <div class="forgot-password">
                        <a href="#">
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

<?= $this->endSection(); ?>