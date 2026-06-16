<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<main>

    <!-- banner section -->
    <section class="hero-banner-section">
        <div class="hero-slider">
            <div class="hero-slides">
                <?php if (!empty($home_imgs)) : ?>
                    <?php foreach ($home_imgs as $index => $banner) : ?>
                        <a 
                            href="<?= base_url('product/' . $banner['product_id']) ?>" 
                            class="hero-slide <?= ($index === 0) ? 'active' : '' ?>" >
                            <img 
                                src="<?= $banner['img_link'] ?>" 
                                alt="Banner Image">
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button class="hero-nav prev-btn">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="hero-nav next-btn">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="hero-dots">
                <?php if (!empty($home_imgs)) : ?>
                    <?php foreach ($home_imgs as $index => $banner) : ?>
                        <span 
                            class="hero-dot <?= ($index === 0) ? 'active' : '' ?>"
                        ></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- shop by section -->
    <section class="shopby-section">
        <div class="shopby-grid">

            <div class="shopby-box">
                <div class="shopby-header">
                    <h2>
                        Shop By Category
                    </h2>
                    <a href="#" class="view-all-btn">
                        View all
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="category-grid">
                    <?php foreach($categories as $row){ ?>
                        <a
                            href="#"
                            class="category-card"
                        >
                            <div class="category-image">
                                <img
                                    src="<?= base_url().$row['cat_img']; ?>"
                                    alt="<?= $row['category_name']; ?>"
                                >
                            </div>
                            <div class="category-name">
                                <?= $row['category_name']; ?>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="shopby-box">
                <div class="shopby-header">
                    <h2>
                        Shop By Brand
                    </h2>
                    <a href="#" class="view-all-btn">
                        View all
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="brand-grid">
                    <?php foreach($artists as $row){ ?>
                        <a
                            href="#"
                            class="brand-card"
                        >
                            <div class="brand-image">
                                <img
                                    src="<?= $row['brand_img']; ?>"
                                    alt="<?= $row['brand_name']; ?>"
                                >
                            </div>
                            <div class="brand-name">
                                <?= $row['brand_name']; ?>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- preorder on hand section -->
    <section class="home-products-section">
        <div class="home-products-grid">
            <div class="home-products-box">
                <div class="home-products-header">
                    <h2> PRE-ORDER / NEW IN </h2>

                    <a href="#" class="view-all-btn">
                        View all
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="home-slider-wrap">
                    <button
                        class="
                            home-slider-arrow
                            left"
                        onclick="slideProducts('preorderSlider',  -1 )"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div
                        class="home-products-slider"
                        id="preorderSlider"  >

                        <?php foreach($pre_order as $product){ ?>
                            <div class="home-product-slide">
                                <div class="product-card">
                                    <div class="
                                            product-label
                                            pre-order "  >
                                        Pre-Order
                                    </div>

                                    <a
                                        href="/product/<?= $product['id']; ?>"
                                        class="product-image"  >
                                        <img
                                            src="<?= $product['main_image']; ?>" >
                                    </a>

                                    <div class="product-content">
                                        <a
                                            href="/product/<?= $product['id']; ?>"
                                            class="product-title" >
                                            <?= $product['product_name']; ?>
                                        </a>

                                        <div class="product-price">
                                            ₹<?= number_format(
                                                $product['discounted_price']
                                                ?: $product['price']
                                            ); ?>
                                        </div>

                                        <div class="product-actions">
                                            <button class="add-cart-btn" >
                                                PRE-ORDER
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <button class=" home-slider-arrow right  "
                        onclick="slideProducts(  'preorderSlider', 1 )" >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>


            <div class="home-products-box">
                <div class="home-products-header">
                    <h2> ON-HAND (READY TO SHIP) </h2>
                    <a href="#" class="view-all-btn">
                        View all
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="home-slider-wrap">
                    <button  class="  home-slider-arrow left  "
                        onclick="slideProducts(  'onhandSlider',  -1 )"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div
                        class="home-products-slider"
                        id="onhandSlider"
                    >
                        <?php foreach($on_hand as $product){ ?>
                            <div class="home-product-slide">
                                <div class="product-card">
                                    <div class="
                                            product-label
                                            on-hand  " >
                                        On-Hand
                                    </div>
                                    <a
                                        href="/product/<?= $product['id']; ?>"
                                        class="product-image" >
                                        <img
                                            src="<?= $product['main_image']; ?>"  >
                                    </a>

                                    <div class="product-content">
                                        <a
                                            href="/product/<?= $product['id']; ?>"
                                            class="product-title" >
                                            <?= $product['product_name']; ?>
                                        </a>

                                        <div class="product-price">
                                            ₹<?= number_format(
                                                $product['discounted_price']
                                                ?: $product['price']
                                            ); ?>
                                        </div>

                                        <div class="product-actions">
                                            <button class="add-cart-btn" >
                                                ADD TO CART
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>


                    <button
                        class=" home-slider-arrow  right  "
                        onclick="slideProducts( 'onhandSlider', 1 )" >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- featured section -->
    <section class="featured-section">

        <div class="featured-box">

        <div class="featured-layout">

            <!-- LEFT TITLE COLUMN -->

            <div class="featured-left">

                <div class="featured-title">
                    FEATURED PICKS
                </div>

            </div>


            <!-- RIGHT PRODUCTS COLUMN -->

            <div class="featured-right">

                <div class="featured-slider-wrap">

                    <button class="featured-arrow left" onclick="slideFeatured(-1)">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="featured-slider" id="featuredSlider">

                        <?php foreach($featured_items as $row){ ?>

                        <a href="#" class="featured-card">

                            <div class="featured-img">
                                <img src="<?= $row['main_image'] ?>">
                            </div>

                            <div class="featured-content">

                                <div class="featured-name">
                                    <?= $row['product_name'] ?>
                                </div>

                                <div class="featured-price">

                                    <?php if(!empty($row["discounted_price"])){ ?>

                                        From ₹<?= number_format($row["discounted_price"]) ?>

                                    <?php } else { ?>

                                        From ₹<?= number_format($row["price"]) ?>

                                    <?php } ?>

                                </div>

                            </div>

                        </a>

                        <?php } ?>

                    </div>

                    <button class="featured-arrow right" onclick="slideFeatured(1)">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                </div>
            </div>
        </div>
        </div>
        </section>
    
    

</main>

<script>

    document.addEventListener("DOMContentLoaded", function () {

        const slides =document.querySelectorAll(".hero-slide");
        const dots = document.querySelectorAll(".hero-dot");
        const nextBtn = document.querySelector(".next-btn");
        const prevBtn = document.querySelector(".prev-btn");

        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide) => {
                slide.classList.remove("active");
            });
            dots.forEach((dot) => {
                dot.classList.remove("active");
            });

            slides[index].classList.add("active");
            dots[index].classList.add("active");
            currentSlide = index;
        }

        function nextSlide() {
            let nextIndex = currentSlide + 1;
            if (nextIndex >= slides.length) {
                nextIndex = 0;
            }

            showSlide(nextIndex);
        }

        function prevSlideFunc() {
            let prevIndex =currentSlide - 1;
            if (prevIndex < 0) {
                prevIndex =
                    slides.length - 1;
            }

            showSlide(prevIndex);
        }

        nextBtn.addEventListener("click", () => {
            nextSlide();
            resetInterval();

        });

        prevBtn.addEventListener("click", () => {
            prevSlideFunc();
            resetInterval();

        });

        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                showSlide(index);
                resetInterval();

            });

        });

        function startSlider() {
            slideInterval = setInterval(() => {
                nextSlide();
            }, 7000);

        }

        function resetInterval() {
            clearInterval(slideInterval);
            startSlider();
        }

        startSlider();

    });

    //to scroll preorder or onhand items
    function slideProducts( sliderId,  direction ){

        const slider = document.getElementById( sliderId );
        const slide = slider.querySelector(".home-product-slide" );
        const gap = 18;
        const scrollAmount =(slide.offsetWidth * 3) + (gap * 3);

        slider.scrollBy({
            left: scrollAmount * direction,
            behavior: 'smooth'
        });

    }
    
    //to scroll featured items
    function slideFeatured(direction){

        let slider=document.getElementById("featuredSlider");
        let card=slider.querySelector(".featured-card");
        let gap=20;
        let scrollAmount=card.offsetWidth+gap;

        slider.scrollBy({
        left:scrollAmount*direction,
        behavior:'smooth'
        });

    }
    
    


</script>

<?= $this->endSection(); ?>