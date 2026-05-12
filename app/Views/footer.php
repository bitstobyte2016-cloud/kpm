<style>
    .footer-container {
        background-color: var(--section-bg);
        border-top: 1px solid var(--theme-border);
        padding: 40px 0 0 0;
        margin-top: 40px;
    }

    .footer-top {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 40px 20px;
        gap: 30px;
    }

    .footer-col {
        flex: 1;
        min-width: 200px;
    }

    .footer-col.brand-col {
        flex: 1.5;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .footer-brand img {
        width: 60px;
        height: auto;
    }

    .footer-brand h3 {
        font-family: 'Poppins', sans-serif;
        color: var(--main-text);
        font-size: 1.2rem;
        margin: 0;
    }

    .footer-desc {
        font-size: 0.9rem;
        color: var(--sec-text);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .footer-socials {
        display: flex;
        gap: 15px;
    }

    .footer-socials a {
        color: var(--main-text);
        font-size: 1.2rem;
        transition: color 0.2s;
    }

    .footer-socials a:hover {
        color: var(--theme-purple);
    }

    .footer-col h4 {
        font-family: 'Poppins', sans-serif;
        color: var(--main-text);
        font-size: 1rem;
        margin-top: 0;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: var(--sec-text);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s;
    }

    .footer-links a:hover {
        color: var(--theme-purple);
    }

    .footer-contact-info {
        font-size: 0.95rem;
        color: var(--sec-text);
        line-height: 1.8;
    }

    .footer-contact-info a {
        color: var(--sec-text);
        text-decoration: none;
        transition: color 0.2s;
    }

    .footer-contact-info a:hover {
        color: var(--theme-purple);
    }

    .footer-bottom {
        background-color: var(--theme-purple);
        padding: 15px 20px;
        color: #fff;
    }

    .footer-bottom-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .footer-copyright {
        font-size: 0.9rem;
    }

    .footer-payments {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .footer-payments i {
        font-size: 1.8rem;
        color: #fff;
    }

    .footer-payments img {
        height: 24px;
        background: #fff;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>

<footer class="footer-container">
    <div class="footer-top">
        <div class="footer-col brand-col">
            <div class="footer-brand">
                <img src="<?= base_url('Images/logo.png') ?>" alt="K-Pop Merch Logo">
                <h3>K-Pop Merch</h3>
            </div>
            <p class="footer-desc">
                Your one-stop destination for official and unofficial K-Pop merchandise. We love K-Pop as much as you do!
            </p>
            <div class="footer-socials">
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                <a href="#"><i class="fa-regular fa-envelope"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>SHOP</h4>
            <ul class="footer-links">
                <li><a href="<?= base_url('/') ?>">All Products</a></li>
                <li><a href="<?= base_url('preorder') ?>">Pre-order / New In</a></li>
                <li><a href="<?= base_url('onhand') ?>">On-hand</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>HELP & INFO</h4>
            <ul class="footer-links">
                <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
                <li><a href="<?= base_url('shipping-info') ?>">Shipping Info</a></li>
                <li><a href="<?= base_url('returns') ?>">Returns & Exchange</a></li>
                <li><a href="<?= base_url('contact') ?>">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>POLICIES</h4>
            <ul class="footer-links">
                <li><a href="<?= base_url('terms') ?>">Terms & Conditions</a></li>
                <li><a href="<?= base_url('privacy') ?>">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>CONTACT</h4>
            <div class="footer-contact-info">
                <div>Email: <a href="mailto:support@kpopmerch.in">support@kpopmerch.in</a></div>
                <div>WhatsApp: <a href="tel:+917014847875">+91 70148 47875</a></div>
                <div>Mon - Sat: 10AM - 7PM (IST)</div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <div class="footer-copyright">
                &copy; 2024 K-Pop Merch. All Rights Reserved.
            </div>
            <div class="footer-payments">
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <!-- Adding text or icons for UPI, RuPay, PhonePe roughly matching the image -->
                <span style="background: white; color: var(--theme-purple); font-weight: bold; font-style: italic; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">UPI</span>
                <span style="background: white; color: var(--theme-purple); font-weight: bold; font-style: italic; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">RuPay</span>
                <span style="background: white; color: var(--theme-purple); font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">PhonePe</span>
            </div>
        </div>
    </div>
</footer>