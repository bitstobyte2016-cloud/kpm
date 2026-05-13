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
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                <div class="carousel-slide">
                    <img src="/Images/img2.jpg" alt="Slide 1">
                </div>
                <div class="carousel-slide">
                    <img src="/Images/img8.jpg" alt="Slide 2">
                </div>
                <div class="carousel-slide">
                    <img src="/Images/img9.jpg" alt="Slide 3">
                </div>
            </div>

            <button class="carousel-btn prev-btn" id="prevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="carousel-btn next-btn" id="nextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="carousel-dots" id="carouselDots">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
                <span class="dot" data-index="2"></span>
            </div>
        </div>

        <div style="padding: 20px;">
            <h1>Welcome to K-Pop Merch Store</h1>
            <p>This is the home page. <i class="fa-solid fa-heart"></i></p>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('carouselTrack');
            const slides = document.querySelectorAll('.carousel-slide');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dots = document.querySelectorAll('.dot');

            let currentIndex = 0;
            const totalSlides = slides.length;
            let slideInterval;

            function updateCarousel() {
                // Shift track
                track.style.transform = `translateX(-${currentIndex * 100}%)`;

                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateCarousel();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                updateCarousel();
            }

            // Event Listeners
            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetInterval();
            });

            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetInterval();
            });

            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    currentIndex = parseInt(e.target.dataset.index);
                    updateCarousel();
                    resetInterval();
                });
            });

            // Auto slide
            function startInterval() {
                slideInterval = setInterval(nextSlide, 5000);
            }

            function resetInterval() {
                clearInterval(slideInterval);
                startInterval();
            }

            startInterval();
        });
    </script>
<?= view('footer') ?>
</body>
</html>
