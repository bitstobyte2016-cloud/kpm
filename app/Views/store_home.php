<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Home</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icon Pack -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="/css/theme.css">
</head>
<body>
    <?= view('header') ?>
    
    <main>
        
        <!-- banner section -->
        <section class="hero-banner-section">
            <div class="container">
                <div class="hero-slider">
                    <!-- Slides -->
                    <div class="hero-slides">
                        <?php if (!empty($data['home_imgs'])) : ?>
                            <?php foreach ($data['home_imgs'] as $index => $banner) : ?>
                                <a href="<?= base_url('product/' . $banner['product_id']) ?>" 
                                    class="hero-slide <?= ($index === 0) ? 'active' : '' ?>">
                                    <img src="<?= base_url($banner['img_link']) ?>" 
                                        alt="Banner Image" >
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Navigation Buttons -->
                    <button class="hero-nav prev-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="hero-nav next-btn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <!-- Dots -->
                    <div class="hero-dots">
                        <?php if (!empty($hero_banners)) : ?>
                            <?php foreach ($hero_banners as $index => $banner) : ?>
                                <span class="hero-dot <?= ($index === 0) ? 'active' : '' ?>"></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
      
        
    </main>
    
    <?= view('footer') ?>

    <!-- banner section -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const slides = document.querySelectorAll(".hero-slide");
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

                let prevIndex = currentSlide - 1;

                if (prevIndex < 0) {
                    prevIndex = slides.length - 1;
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
    
    
</body>
</html>
