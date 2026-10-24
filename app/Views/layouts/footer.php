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
        
   
    /* Footer accordion buttons */
    .footer-accordion-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0;
        border: 0;
        background: transparent;
        color: var(--main-text);
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
    }

    .footer-accordion-toggle i {
        font-size: 12px;
        transition: transform 0.2s ease;
    }

    .footer-accordion-toggle[aria-expanded="true"] i {
        transform: rotate(180deg);
    }

    /* Desktop: preserve normal footer layout and visible links */
    @media (min-width: 768px) {
        .footer-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-accordion-toggle {
            display: block;
            pointer-events: none;
            font: inherit;
            color: inherit;
        }

        .footer-accordion-toggle i {
            display: none;
        }

        .footer-accordion .footer-links,
        .footer-accordion .footer-contact-info {
            display: block !important;
        }
    }

    /* Mobile footer */  
    @media (max-width: 767px) {
        .footer-container {
            padding-top: 18px;
            margin-top: 25px;
        }

        .footer-top {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: start;
            column-gap: 16px;
            row-gap: 0;
            padding: 0 16px 18px;
        }

        /* Brand stays in the left column */
        .footer-top > .footer-brand-column {
            grid-column: 1;
            grid-row: 1 / span 4;
            min-width: 0;
            width: auto;
            flex: none;
        }

        /* Accordion sections stack in the right column */
        .footer-top > .footer-accordion {
            grid-column: 2;
            min-width: 0;
            width: auto;
            flex: none;
            margin: 0;
        }

        .footer-top > .footer-accordion:nth-child(2) {
            grid-row: 1;
        }

        .footer-top > .footer-accordion:nth-child(3) {
            grid-row: 2;
        }

        .footer-top > .footer-accordion:nth-child(4) {
            grid-row: 3;
        }

        .footer-top > .footer-accordion:nth-child(5) {
            grid-row: 4;
        }

        .footer-brand {
            align-items: flex-start;
            gap: 9px;
            margin-bottom: 8px;
        }

        .footer-brand img {
            width: 55px;
            min-width: 55px;
            height: auto;
        }

        .footer-brand h3 {
            font-size: 14px;
            line-height: 1.4;
        }

        .footer-desc {
            font-size: 12px;
            line-height: 1.4;
            margin: 0 0 12px;
        }

        .footer-socials {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .footer-socials a {
            font-size: 17px;
        }

        .footer-accordion h4 {
            margin: 0;
            padding: 0;
            border-bottom: 1px solid var(--theme-border);
        }

        .footer-accordion-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 29px;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--main-text);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            appearance: none;
            -webkit-appearance: none;
        }

        .footer-accordion-toggle i {
            display: inline-block;
            font-size: 11px;
        }

        .footer-accordion .footer-links,
        .footer-accordion .footer-contact-info {
            display: none;
            padding: 4px 0 8px;
        }

        .footer-accordion.is-open .footer-links,
        .footer-accordion.is-open .footer-contact-info {
            display: block;
        }

        .footer-accordion .footer-links li {
            margin: 7px 0;
        }

        .footer-accordion .footer-links a,
        .footer-accordion .footer-contact-info a {
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .footer-bottom {
            padding: 12px 16px;
        }

        .footer-bottom-inner {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .footer-copyright {
            flex: 1;
            min-width: 0;
            font-size: 10px;
            line-height: 1.4;
        }

        .footer-payments {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
            gap: 6px;
        }

        .footer-payments i {
            font-size: 20px;
        }

        .footer-payments img {
            height: 22px;
            max-width: 46px;
            object-fit: contain;
        }

        .footer-payments li {
            list-style: none;
        }
    }

    
    
</style>

<footer class="footer-container">
    <div class="footer-top">
        <!-- Brand section -->
        <div class="footer-col brand-col footer-brand-column">
            <div class="footer-brand">
                <img src="<?= base_url('Images/logo.png') ?>" alt="K-Pop Merch Logo">
                <h3>K-Pop Merch</h3>
            </div>

            <p class="footer-desc">
                Your one-stop destination for official and unofficial K-Pop merchandise.
                We love K-Pop as much as you do!
            </p>

            <div class="footer-socials">
                <a href="https://www.instagram.com/kp0pmerch/" aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
        </div>

        <!-- SHOP -->
        <div class="footer-col footer-accordion">
            <h4>
                <button type="button" class="footer-accordion-toggle"
                        aria-expanded="false">
                    <span>SHOP</span>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </h4>

            <ul class="footer-links">
                <li><a href="<?= base_url('categories/1') ?>">All Products</a></li>
                <li><a href="<?= base_url('preorder') ?>">Pre-order / New In</a></li>
                <li><a href="<?= base_url('onhand') ?>">On-hand</a></li>
            </ul>
        </div>

        <!-- HELP & INFO -->
        <div class="footer-col footer-accordion">
            <h4>
                <button type="button" class="footer-accordion-toggle"
                        aria-expanded="false">
                    <span>HELP &amp; INFO</span>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </h4>

            <ul class="footer-links">
                <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
                <li><a href="<?= base_url('shipping_info') ?>">Shipping Info</a></li>
                <li><a href="<?= base_url('returns') ?>">Returns &amp; Exchange</a></li>
            </ul>
        </div>

        <!-- POLICIES -->
        <div class="footer-col footer-accordion">
            <h4>
                <button type="button" class="footer-accordion-toggle"
                        aria-expanded="false">
                    <span>POLICIES</span>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </h4>

            <ul class="footer-links">
                <li><a href="<?= base_url('terms') ?>">Terms &amp; Conditions</a></li>
                <li><a href="<?= base_url('privacy') ?>">Privacy Policy</a></li>
            </ul>
        </div>

        <!-- CONTACT -->
        <div class="footer-col footer-accordion">
            <h4>
                <button type="button" class="footer-accordion-toggle"
                        aria-expanded="false">
                    <span>CONTACT</span>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </h4>

            <div class="footer-contact-info">
                <div>
                    <i class="fa-regular fa-envelope"></i>
                    Email:
                    <a href="mailto:support@kpopmerch.in">support@kpopmerch.in</a>
                </div>

                <div>
                    <i class="fa-brands fa-whatsapp"></i>
                    WhatsApp:
                    <a href="tel:+917014879475">+91 70148 79475</a>
                </div>

                <div>
                    <i class="fa-regular fa-clock"></i>
                    Mon - Sat: 10AM - 7PM (IST)
                </div>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <div class="footer-copyright">
                &copy; 2019 K-Pop Merch. All Rights Reserved.
            </div>
            <div class="footer-payments">
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <li><img src="/Images/icons/ic_upi.png" class="band-icon" alt=""></li>
            </div>
        </div>
    </div>
    
    
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const footerAccordions = document.querySelectorAll(
            '.footer-container .footer-accordion'
        );

        footerAccordions.forEach(function (accordion) {
            const button = accordion.querySelector('.footer-accordion-toggle');

            if (!button) return;

            button.addEventListener('click', function () {
                // Accordions are interactive only on mobile.
                if (window.innerWidth > 767) return;

                const isOpen = accordion.classList.contains('is-open');

                // Close the other sections.
                footerAccordions.forEach(function (item) {
                    item.classList.remove('is-open');

                    const itemButton = item.querySelector(
                        '.footer-accordion-toggle'
                    );

                    if (itemButton) {
                        itemButton.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle the tapped section.
                if (!isOpen) {
                    accordion.classList.add('is-open');
                    button.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });

</script>
    
    
</footer>
