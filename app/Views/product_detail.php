<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">

    <!-- BREADCRUMB -->
    <div class="product-detail-breadcrumb">

        <a href="<?= base_url(); ?>">
            Home
        </a>

        <i class="fa-solid fa-chevron-right"></i>

        <span>
            <?= esc($product['product_name']); ?>
        </span>

    </div>


    <!-- PRODUCT TOP SECTION -->
    <div class="product-detail-top">

        <!-- LEFT : PRODUCT IMAGE -->
        <div class="product-detail-image-section">

            <div class="product-detail-image">

                <?php if (!empty($product['main_image'])): ?>

                    <img
                        src="<?= esc($product['main_image']); ?>"
                        alt="<?= esc($product['product_name']); ?>"
                        id="productMainImage"
                    >

                <?php else: ?>

                    <div class="product-detail-no-image">
                        <i class="fa-regular fa-image"></i>
                    </div>

                <?php endif; ?>


                <button
                    type="button"
                    class="product-image-zoom-btn"
                    onclick="zoomProductImage()"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

            </div>

        </div>


        <!-- RIGHT : PRODUCT INFORMATION -->
        <div class="product-detail-info">

            <?php
                $isInStock = ( (int)$product['product_qty'] > 0 );
                $isOnHand = ($product['on_hand'] === 'Y');
                $isPreOrder = false;

                if (!empty($product['release_date']))
                {
                    $isPreOrder = strtotime($product['release_date']) > strtotime(date('Y-m-d'));
                }

                $price = (float)$product['price'];
                $discountedPrice = !empty($product['discounted_price']) ? (float)$product['discounted_price'] : 0;
                $hasDiscount = ( $discountedPrice > 0 && $discountedPrice < $price  );
            ?>
            
            <!-- Top tag -->
            <?php if (!$isInStock): ?>

                <div class="product-detail-stock-tag out-of-stock">
                    OUT OF STOCK
                </div>

            <?php elseif ($isOnHand): ?>

                <div class="product-detail-stock-tag on-hand">
                    ON-HAND
                </div>

            <?php elseif ($hasDiscount): ?>

                <div class="product-detail-stock-tag discounted">
                    DISCOUNTED
                </div>

            <?php elseif ($isPreOrder): ?>

                <div class="product-detail-stock-tag pre-order">
                    PRE-ORDER
                </div>

            <?php endif; ?>


            <!-- PRODUCT NAME -->
            <h1 class="product-detail-title">
                <?= esc($product['product_name']); ?>
            </h1>


            <!-- PRICE -->
            <div class="product-detail-price-wrap">

                <?php if ($hasDiscount): ?>

                    <div class="product-detail-discount-price">
                        ₹<?= number_format($discountedPrice, 0); ?>
                    </div>

                    <div class="product-detail-original-price">
                        ₹<?= number_format($price, 0); ?>
                    </div>

                <?php else: ?>

                    <div class="product-detail-price">
                        ₹<?= number_format($price, 0); ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- RELEASE DATE -->
            <?php if (!empty($product['release_date'])): ?>

                <div class="product-detail-meta">

                    <div class="product-detail-meta-row">

                        <i class="fa-regular fa-calendar"></i>

                        <span>
                            Release Date:
                        </span>

                        <strong>
                            <?= date('M d, Y', strtotime($product['release_date'])); ?>
                        </strong>

                    </div>

                </div>

            <?php endif; ?>


            <!-- QUANTITY -->
            <div class="product-detail-quantity-row">

                <span class="product-detail-quantity-label">
                    Quantity
                </span>

                <div class="product-detail-quantity">

                    <button
                        type="button"
                        onclick="changeProductQuantity(-1)"
                    >
                        −
                    </button>

                    <span id="productQuantity">
                        1
                    </span>

                    <button
                        type="button"
                        onclick="changeProductQuantity(1)"
                    >
                        +
                    </button>

                </div>

                <?php if ($isInStock): ?>

                    <span class="product-detail-max">
                        Max 25 per customer
                    </span>

                <?php endif; ?>

            </div>


            <!-- ADD TO CART / NOTIFY -->
            <?php if ($isInStock): ?>

                <button
                    type="button"
                    class="product-detail-cart-btn"
                    onclick="addProductToCart('<?= esc($product['id']); ?>', productQuantity)"
                >
                    <i class="fa-solid fa-cart-shopping"></i>
                    ADD TO CART
                </button>

            <?php else: ?>

                <button
                    type="button"
                    class="product-detail-notify-btn"
                    onclick="notifyProduct('<?= esc($product['id']); ?>')"
                >
                    <i class="fa-regular fa-bell"></i>
                    NOTIFY ME
                </button>

            <?php endif; ?>

            <!-- ADD TO WISHLIST -->
            <button
                type="button"
                class="product-detail-wishlist-btn"
                onclick="addProductToWishlist('<?= esc($product['id']); ?>')"
            >
                <i class="fa-regular fa-heart"></i>
                ADD TO WISHLIST
            </button>

            <!-- SHARE -->
            <div class="product-detail-share">

                <span>
                    Share this product
                </span>

                <div class="product-detail-share-icons">

                    <button
                        type="button"
                        onclick="copyProductLink()"
                        title="Copy Link"
                    >
                        <i class="fa-solid fa-link"></i>
                    </button>

                    <button
                        type="button"
                        onclick="shareProductFacebook()"
                        title="Share on Facebook"
                    >
                        <i class="fa-brands fa-facebook-f"></i>
                    </button>

                    <button
                        type="button"
                        onclick="shareProductWhatsapp()"
                        title="Share on WhatsApp"
                    >
                        <i class="fa-brands fa-whatsapp"></i>
                    </button>

                    <button
                        type="button"
                        onclick="shareProductInstagram()"
                        title="Share on Instagram"
                    >
                        <i class="fa-brands fa-instagram"></i>
                    </button>

                </div>

            </div>

        </div>

    </div>
    
    <!-- Product Detail Tabs -->
    <div class="product-detail-tabs">
        <div class="product-detail-tab-buttons">

            <button type="button"
                    class="product-detail-tab active"
                    onclick="openProductTab('productDetails', this)">
                PRODUCT DETAILS
            </button>

            <button type="button"
                        class="product-detail-tab <?= empty($additionalImages) ? 'active' : ''; ?>"
                        onclick="openProductTab('description', this)">
                    DESCRIPTION
            </button>

            <button type="button"
                    class="product-detail-tab"
                    onclick="openProductTab('shippingInfo', this)">
                SHIPPING INFO
            </button>

            <button type="button"
                    class="product-detail-tab"
                    onclick="openProductTab('returnExchange', this)">
                RETURN & EXCHANGE
            </button>

            <button type="button"
                    class="product-detail-tab"
                    onclick="openProductTab('orderInfo', this)">
                ORDER INFO
            </button>

        </div>

        <button type="button"
                class="product-mobile-accordion-toggle active"
                data-target="productDetails"
                aria-expanded="true">
            <span><i class="fa-solid fa-cube"></i> Product Details</span>
            <i class="fa-solid fa-chevron-up accordion-chevron"></i>
        </button>
        <!-- Product Details -->
        <div id="productDetails" class="product-detail-tab-content active">

            <?php
            $descriptionImages = [];

            if (!empty($additionalImages))
            {
                foreach ($additionalImages as $image)
                {
                    $imagePath = trim($image['image_path']);

                    if ($imagePath == '' || stripos($imagePath, 'alt="') !== false)
                    {
                        continue;
                    }

                    $imageList = explode(',', $imagePath);

                    foreach ($imageList as $singleImage)
                    {
                        $singleImage = trim($singleImage);

                        if ($singleImage == '' || stripos($singleImage, 'alt="') !== false)
                        {
                            continue;
                        }

                        $descriptionImages[] = $singleImage;
                    }
                }
            }
            ?>

            <?php if (!empty($descriptionImages)): ?>
                <?php foreach ($descriptionImages as $descriptionImage): ?>
                    <img src="<?= esc($descriptionImage); ?>"
                         alt="<?= esc($product['product_name']); ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <div class="product-detail-no-description-image">
                    There are currently no description images available for this item.
                </div>
            <?php endif; ?>

        </div>

        <!-- Description -->
        <?php if (!empty($product['product_description'])): ?>
                <button type="button"
                        class="product-mobile-accordion-toggle"
                        data-target="description"
                        aria-expanded="false">
                    <span><i class="fa-regular fa-file-lines"></i> Description</span>
                    <i class="fa-solid fa-chevron-down accordion-chevron"></i>
                </button>
            <div id="description"
                 class="product-detail-tab-content"
                 <?= !empty($additionalImages) ? 'style="display:none;"' : ''; ?>>

                <?= $product['product_description']; ?>

            </div>
        <?php endif; ?>

        <!-- Shipping Info -->
        <button type="button"
                class="product-mobile-accordion-toggle active"
                data-target="shippingInfo"
                aria-expanded="true">
            <span><i class="fa-solid fa-truck"></i> Shipping Info</span>
            <i class="fa-solid fa-chevron-up accordion-chevron"></i>
        </button>
        <div id="shippingInfo"
             class="product-detail-tab-content"
             style="display:none;">

            <img src="<?= base_url('Images/shipping_info.png'); ?>"
                 alt="Shipping Info">

        </div>

        <!-- Return & Exchange -->
        <button type="button"
                class="product-mobile-accordion-toggle active"
                data-target="returnExchange"
                aria-expanded="true">
            <span><i class="fa-solid fa-arrows-rotate"></i> Return & Exchange</span>
            <i class="fa-solid fa-chevron-up accordion-chevron"></i>
        </button>
        <div id="returnExchange"
             class="product-detail-tab-content"
             style="display:none;">

            <img src="<?= base_url('Images/return_exchange.png'); ?>"
                 alt="Return & Exchange">

        </div>

        <!-- Order Info -->
        <button type="button"
                class="product-mobile-accordion-toggle active"
                data-target="orderInfo"
                aria-expanded="true">
            <span><i class="fa-solid fa-circle-info"></i> Order Info</span>
            <i class="fa-solid fa-chevron-up accordion-chevron"></i>
        </button>
        <div id="orderInfo"
             class="product-detail-tab-content"
             style="display:none;">
            <img src="<?= base_url('Images/order_info.png'); ?>"
                 alt="Order Info">
        </div>

    </div>
    
    <!-- Reviews -->
    <div class="product-detail-reviews">
        <div class="product-detail-review-header">
            <div class="product-detail-review-summary">

                <div class="product-detail-review-count">
                    <?= $reviewCount; ?> <?= $reviewCount == 1 ? 'Review' : 'Reviews'; ?>
                </div>

                <?php if ($reviewCount > 0): ?>

                    <div class="product-detail-review-rating">

                        <strong><?= number_format($averageRating, 1); ?></strong>

                        <div class="product-detail-review-stars">

                            <?php
                            $fullStars = floor($averageRating);
                            $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                            ?>

                            <?php for ($i = 1; $i <= 5; $i++): ?>

                                <?php if ($i <= $fullStars): ?>

                                    <i class="fa-solid fa-star"></i>

                                <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>

                                    <i class="fa-solid fa-star-half-stroke"></i>

                                <?php else: ?>

                                    <i class="fa-regular fa-star"></i>

                                <?php endif; ?>

                            <?php endfor; ?>

                        </div>

                    </div>

                    <div class="product-detail-review-based">
                        Based on <?= $reviewCount; ?> <?= $reviewCount == 1 ? 'review' : 'reviews'; ?>
                    </div>

                <?php endif; ?>

            </div>


            <?php if ($reviewCount > 0): ?>

                <div class="product-detail-rating-breakdown">

                    <?php for ($rating = 5; $rating >= 1; $rating--): ?>

                        <?php
                        $count = $ratingCounts[$rating];
                        $percentage = ($count / $reviewCount) * 100;
                        ?>

                        <div class="product-detail-rating-row">

                            <span class="product-detail-rating-number">
                                <?= $rating; ?> <i class="fa-solid fa-star"></i>
                            </span>

                            <div class="product-detail-rating-bar">
                                <div class="product-detail-rating-bar-fill"
                                     style="width:<?= $percentage; ?>%;">
                                </div>
                            </div>

                            <span class="product-detail-rating-count">
                                <?= $count; ?>
                            </span>

                        </div>

                    <?php endfor; ?>

                </div>
            
                <!-- Write a Review -->
                <div class="product-detail-write-review-wrap">

                    <button type="button"
                            class="product-detail-write-review-btn"
                            onclick="writeProductReview('<?= esc($product['id']); ?>')">
                        WRITE A REVIEW
                    </button>

                </div>

            <?php endif; ?>

        </div>

        <?php if (!empty($reviews)): ?>
            <div class="product-detail-reviews-slider">
                <button type="button"
                        class="product-detail-review-arrow left"
                        onclick="scrollProductReviews(-1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="product-detail-reviews-scroll" id="productReviewsScroll">
                    <?php foreach ($reviews as $review): ?>
                        <div class="product-detail-review-card">
                            <div class="product-detail-review-customer">
                                <?= esc(trim($review['first_name'].' '.$review['last_name'])); ?>
                            </div>

                            <div class="product-detail-review-card-stars">

                                <?php
                                $reviewStars = (int)$review['review_star_points'];
                                ?>

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <?php if ($i <= $reviewStars): ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php else: ?>
                                        <i class="fa-regular fa-star"></i>
                                    <?php endif; ?>

                                <?php endfor; ?>

                            </div>

                            <?php if (!empty($review['review_title'])): ?>
                                <div class="product-detail-review-title">
                                    <?= esc($review['review_title']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($review['review_text'])): ?>
                                <div class="product-detail-review-text">
                                    <?= esc($review['review_text']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($reviewCount > 3): ?>
                    <div class="product-detail-load-more-wrap">
                        <button
                            type="button"
                            id="loadMoreProductReviews"
                            class="product-detail-load-more-btn"
                            data-product-id="<?= esc($product['id']); ?>"
                            data-page="1"
                            onclick="loadMoreProductReviews()">
                            VIEW ALL REVIEWS (<?= $reviewCount; ?>)
                        </button>
                    </div>
                <?php endif; ?>

                <button type="button"
                        class="product-detail-review-arrow right"
                        onclick="scrollProductReviews(1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

        <?php else: ?>
            <div class="product-detail-no-reviews">
                <div class="product-detail-no-reviews-title">
                    No reviews yet
                </div>

                <div class="product-detail-no-reviews-text">
                    Be the first to review this product.
                </div>
                
            <!-- Write a Review -->
            <div class="product-detail-write-review-wrap">

                <button type="button"
                        class="product-detail-write-review-btn"
                        onclick="writeProductReview('<?= esc($product['id']); ?>')">
                    WRITE A REVIEW
                </button>

            </div>
            </div>
        
        <?php endif; ?>

    </div>
    
    <!-- Recommendations -->
    <?php if (!empty($recommendations)): ?>
        <div class="product-detail-recommendations">

            <div class="product-detail-recommendations-title">
                You May Also Like
            </div>

            <div class="product-detail-recommendations-grid">

                <?php foreach ($recommendations as $recommendation): ?>

                    <?php
                    $recommendationPrice = (float)$recommendation['price'];
                    $recommendationDiscountedPrice = !empty($recommendation['discounted_price'])
                        ? (float)$recommendation['discounted_price']
                        : 0;

                    $recommendationHasDiscount = (
                        $recommendationDiscountedPrice > 0 &&
                        $recommendationDiscountedPrice < $recommendationPrice
                    );
                    ?>

                    <div class="product-card">

                        <a href="<?= base_url('product/'.$recommendation['id']); ?>"
                           class="product-image">

                            <img src="<?= esc($recommendation['main_image']); ?>"
                                 alt="<?= esc($recommendation['product_name']); ?>">

                        </a>


                        <div class="product-content">

                            <a href="<?= base_url('product/'.$recommendation['id']); ?>"
                               class="product-title">
                                <?= esc($recommendation['product_name']); ?>
                            </a>


                            <?php if ($recommendationHasDiscount): ?>

                                <div class="product-price-wrap">

                                    <div class="product-old-price">
                                        ₹<?= number_format($recommendationPrice, 0); ?>
                                    </div>

                                    <div class="product-price">
                                        ₹<?= number_format($recommendationDiscountedPrice, 0); ?>
                                    </div>

                                </div>

                            <?php else: ?>

                                <div class="product-price">
                                    ₹<?= number_format($recommendationPrice, 0); ?>
                                </div>

                            <?php endif; ?>


                            <div class="product-actions">

                                <button type="button"
                                        class="add-cart-btn"
                                        onclick="addProductToCart('<?= esc($recommendation['id']); ?>')">

                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Add to Cart

                                </button>

                                <button type="button"
                                        class="wishlist-btn"
                                        onclick="addProductToWishlist('<?= esc($recommendation['id']); ?>')">

                                    <i class="fa-regular fa-heart"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>
    
</div>

<!-- Write Review Modal -->
<div id="writeReviewModal" class="review-modal">

    <div class="review-modal-overlay" onclick="closeWriteReview()"></div>

    <div class="review-modal-box">

        <button type="button"
                class="review-modal-close"
                onclick="closeWriteReview()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="review-modal-title">
            Write a Review
        </div>

        <div class="review-modal-product">
            <?= esc($product['product_name']); ?>
        </div>


        <!-- Rating -->
        <div class="review-form-group">

            <label>
                Rating <span>*</span>
            </label>

            <div class="review-star-selector">

                <button type="button" onclick="selectReviewStar(1)">
                    <i class="fa-regular fa-star"></i>
                </button>

                <button type="button" onclick="selectReviewStar(2)">
                    <i class="fa-regular fa-star"></i>
                </button>

                <button type="button" onclick="selectReviewStar(3)">
                    <i class="fa-regular fa-star"></i>
                </button>

                <button type="button" onclick="selectReviewStar(4)">
                    <i class="fa-regular fa-star"></i>
                </button>

                <button type="button" onclick="selectReviewStar(5)">
                    <i class="fa-regular fa-star"></i>
                </button>

            </div>

            <input type="hidden" id="reviewStarPoints" value="">

        </div>


        <!-- Review Title -->
        <div class="review-form-group">

            <label for="reviewTitle">
                Review Title <span>*</span>
            </label>

            <input type="text"
                   id="reviewTitle"
                   maxlength="100"
                   placeholder="Give your review a title">

        </div>


        <!-- Review Text -->
        <div class="review-form-group">

            <label for="reviewText">
                Your Review
            </label>

            <textarea id="reviewText"
                      rows="5"
                      maxlength="500"
                      placeholder="Tell us about your experience with this product"></textarea>

        </div>


        <button type="button"
                class="review-submit-btn"
                onclick="submitProductReview()">
            SUBMIT REVIEW
        </button>

    </div>

</div>

<script>

    let productQuantity = 1;

    //to add quantity
    function changeProductQuantity(change)
    {
        let newQuantity = productQuantity + change;

        if(newQuantity < 1)
        {
            newQuantity = 1;
        }

        if(newQuantity > 5)
        {
            newQuantity = 5;
        }

        productQuantity = newQuantity;

        $("#productQuantity").text(productQuantity);
    }

    //to zoom main image
    function zoomProductImage()
    {
        let image = $("#productMainImage").attr("src");

        if(!image) return;

        window.open(image, "_blank");
    }

    // to copy product link
    function copyProductLink()
    {
        navigator.clipboard.writeText(window.location.href);

        showAlert(
            "success",
            "Link Copied",
            "The product link has been copied to your clipboard."
        );
    }

    //to share product to facebook
    function shareProductFacebook()
    {
        let url = encodeURIComponent(window.location.href);

        window.open(
            "https://www.facebook.com/sharer/sharer.php?u=" + url,
            "_blank",
            "width=600,height=500"
        );
    }

    //to share product on whatsapp
    function shareProductWhatsapp()
    {
        let url = encodeURIComponent(window.location.href);
        let text = encodeURIComponent("Check out this product on K-Pop Merch!");

        window.open(
            "https://wa.me/?text=" + text + "%20" + url,
            "_blank"
        );
    }

    //to share product on instagram
    function shareProductInstagram()
    {
        copyProductLink();

        showAlert(
            "info",
            "Link Copied",
            "The product link has been copied. You can now share it on Instagram."
        );
    }

    //to open detail tabs
    function openProductTab(tabId, button)
    {
        // Hide every tab panel first.
        $(".product-detail-tab-content")
            .stop(true, true)
            .hide()
            .removeClass("active");

        // Show only the selected panel.
        $("#" + tabId)
            .stop(true, true)
            .show()
            .addClass("active");

        // Update the desktop tab buttons only.
        $(".product-detail-tab-buttons .product-detail-tab")
            .removeClass("active");

        $(button).addClass("active");
    }
    
    //to horizontal scroll reviews
    function scrollProductReviews(direction)
    {
        let container = $("#productReviewsScroll");
        let card = container.find(".product-detail-review-card").first();

        if(card.length === 0) return;

        let cardWidth = card.outerWidth(true);

        container.animate({
            scrollLeft: container.scrollLeft() + (cardWidth * direction)
        }, 300);
    }
    
    //to load more reviews
    function loadMoreProductReviews()
    {
        const button = $("#loadMoreProductReviews");

        if (!button.length || button.data("loading")) {
            return;
        }

        const productId = button.data("product-id");
        const page = parseInt(button.attr("data-page"), 10) || 1;

        button.data("loading", true);
        button.prop("disabled", true).text("LOADING...");

        $.ajax({
            url: "<?= base_url('product/reviews/'); ?>" +
                 encodeURIComponent(productId),
            type: "GET",
            data: { page: page },
            dataType: "json",

            success: function(response)
            {
                if (!response.status) {
                    button.prop("disabled", false)
                          .text("VIEW ALL REVIEWS");
                    return;
                }

                const container = $("#productReviewsScroll");

                response.reviews.forEach(function(review)
                {
                    const card = $("<div>")
                        .addClass("product-detail-review-card");

                    $("<div>")
                        .addClass("product-detail-review-customer")
                        .text(
                            ((review.first_name || "") + " " +
                             (review.last_name || "")).trim()
                        )
                        .appendTo(card);

                    const stars = $("<div>")
                        .addClass("product-detail-review-card-stars");

                    const rating = Math.round(
                        parseFloat(review.review_star_points) || 0
                    );

                    for (let i = 1; i <= 5; i++) {
                        $("<i>")
                            .addClass(
                                i <= rating
                                    ? "fa-solid fa-star"
                                    : "fa-regular fa-star"
                            )
                            .appendTo(stars);
                    }

                    card.append(stars);

                    if (review.review_title) {
                        $("<div>")
                            .addClass("product-detail-review-title")
                            .text(review.review_title)
                            .appendTo(card);
                    }

                    if (review.review_text) {
                        $("<div>")
                            .addClass("product-detail-review-text")
                            .text(review.review_text)
                            .appendTo(card);
                    }

                    container.append(card);
                });

                button.attr("data-page", page + 1);

                if (!response.has_more) {
                    button.remove();
                } else {
                    button.prop("disabled", false)
                          .text("LOAD MORE REVIEWS");
                }
            },

            error: function()
            {
                button.prop("disabled", false)
                      .text("RETRY LOADING REVIEWS");
            },

            complete: function()
            {
                button.data("loading", false);
            }
        });
    }
    
    let selectedReviewStars = 0;

    // to open alert to write review
    function writeProductReview(productId)
    {
        <?php if (!session()->get('customer_id')): ?>

            showAlert(
                "info",
                "Sign In Required",
                "Please sign in to write a review."
            );

            setTimeout(function(){
                window.location.href ="<?= base_url('signin'); ?>?redirect=" +  encodeURIComponent(window.location.href);
            }, 1200);

            return;

        <?php endif; ?>

        selectedReviewStars = 0;

        $("#reviewStarPoints").val("");
        $("#reviewTitle").val("");
        $("#reviewText").val("");

        $(".review-star-selector button i")
            .removeClass("fa-solid")
            .addClass("fa-regular");

        $("#writeReviewModal").fadeIn(200);
    }


    function closeWriteReview()
    {
        $("#writeReviewModal").fadeOut(200);
    }

    //to select no of stars
    function selectReviewStar(star)
    {
        selectedReviewStars = star;

        $("#reviewStarPoints").val(star);

        $(".review-star-selector button i").each(function(index){

            if(index < star)
            {
                $(this)
                    .removeClass("fa-regular")
                    .addClass("fa-solid");
            }
            else
            {
                $(this)
                    .removeClass("fa-solid")
                    .addClass("fa-regular");
            }

        });
    }

    //to submit product review
    function submitProductReview()
    {
        let stars = $("#reviewStarPoints").val();
        let title = $.trim($("#reviewTitle").val());
        let reviewText = $.trim($("#reviewText").val());

        if(stars == "")
        {
            showAlert(
                "error",
                "Rating Required",
                "Please select a star rating."
            );

            return;
        }

        if(title == "")
        {
            showAlert(
                "error",
                "Title Required",
                "Please enter a title for your review."
            );

            return;
        }


        showLoader();

        $.post(
            "<?= base_url('product/save-review'); ?>",
            {
                product_id: "<?= esc($product['id']); ?>",
                review_star_points: stars,
                review_title: title,
                review_text: reviewText
            },
            function(response)
            {
                hideLoader();

                if(response.status)
                {
                    closeWriteReview();

                    showAlert(
                        "success",
                        "Review Submitted",
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
                        "Unable to Submit",
                        response.message
                    );
                }
            },
            "json"
        );
    }
    
    
    
    //for mobile accordian view
    $(function () {
        const mobileQuery = window.matchMedia("(max-width: 767px)");

        function setupProductAccordions() {
            const isMobile = mobileQuery.matches;

            if (!isMobile) {
                // Reset mobile accordion buttons.
                $(".product-mobile-accordion-toggle")
                    .removeClass("active")
                    .attr("aria-expanded", "false");

                $(".product-mobile-accordion-toggle .accordion-chevron")
                    .removeClass("fa-chevron-up")
                    .addClass("fa-chevron-down");

                // Hide every tab panel before initializing desktop tabs.
                $(".product-detail-tab-content")
                    .stop(true, true)
                    .hide()
                    .removeClass("active");

                // Show Product Details as the default desktop tab.
                $("#productDetails")
                    .show()
                    .addClass("active");

                // Reset desktop tab selection.
                $(".product-detail-tab-buttons .product-detail-tab")
                    .removeClass("active");

                $('.product-detail-tab-buttons .product-detail-tab')
                    .filter(function () {
                        return $(this).attr("onclick") &&
                               $(this).attr("onclick").includes(
                                   "openProductTab('productDetails'"
                               );
                    })
                    .addClass("active");

                return;
            }

            // Open Product Details by default, matching the reference.
            $(".product-detail-tab-content").hide();

            $(".product-mobile-accordion-toggle")
                .removeClass("active")
                .attr("aria-expanded", "false")
                .find(".accordion-chevron")
                .removeClass("fa-chevron-up")
                .addClass("fa-chevron-down");

            const detailsButton = $(
                '.product-mobile-accordion-toggle[data-target="productDetails"]'
            );

            detailsButton
                .addClass("active")
                .attr("aria-expanded", "true");

            detailsButton.find(".accordion-chevron")
                .removeClass("fa-chevron-down")
                .addClass("fa-chevron-up");

            $("#productDetails").show();
        }

        $(".product-mobile-accordion-toggle").on("click", function () {
            if (!mobileQuery.matches) {
                return;
            }

            const button = $(this);
            const target = button.data("target");
            const panel = $("#" + target);
            const isOpen = button.hasClass("active");

            // Close all panels first.
            $(".product-mobile-accordion-toggle")
                .removeClass("active")
                .attr("aria-expanded", "false");

            $(".product-mobile-accordion-toggle .accordion-chevron")
                .removeClass("fa-chevron-up")
                .addClass("fa-chevron-down");

            $(".product-detail-tab-content").stop(true, true).slideUp(180);

            // Open the selected panel if it was previously closed.
            if (!isOpen) {
                button.addClass("active").attr("aria-expanded", "true");

                button.find(".accordion-chevron")
                    .removeClass("fa-chevron-down")
                    .addClass("fa-chevron-up");

                panel.stop(true, true).slideDown(180);
            }
        });

        setupProductAccordions();

        if (mobileQuery.addEventListener) {
            mobileQuery.addEventListener("change", setupProductAccordions);
        } else {
            mobileQuery.addListener(setupProductAccordions);
        }
    });

</script>

<?= $this->endSection(); ?>