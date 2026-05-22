<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<main>

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
                                alt="Banner Image"
                            >
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


            <!-- =====================================
                DOTS
            ====================================== -->

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

</script>

<?= $this->endSection(); ?>