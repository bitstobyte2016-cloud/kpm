<div class="wishlist-detail-page">

    <!-- Back -->
    <a
        href="javascript:void(0);"
        class="wishlist-detail-back"
        onclick="openWishlist()"
    >
        <i class="fa-solid fa-arrow-left"></i>
        Back to Wishlists
    </a>


    <!-- Wishlist Header -->
    <div class="wishlist-detail-header">

        <div class="wishlist-detail-title-wrap">

            <h1>
                <?= esc($wishlist['wishlist_name']); ?>

                <button
                    type="button"
                    class="wishlist-rename-btn"
                    onclick="renameWishlist(<?= (int)$wishlist['id']; ?>)"
                >
                    <i class="fa-solid fa-pen"></i>
                </button>
            </h1>

            <div class="wishlist-detail-meta">

                <span>
                    <?= (int)$wishlist['item_count']; ?> items
                </span>

            </div>

        </div>


        <button
            type="button"
            class="wishlist-detail-rename-button"
            onclick="renameWishlist(<?= (int)$wishlist['id']; ?>)"
        >
            Rename
        </button>

    </div>


    <!-- Wishlist Items -->
    <div class="wishlist-detail-items">

        <?php if (!empty($wishlist['products'])): ?>

            <?php foreach ($wishlist['products'] as $product): ?>

                <div
                    class="wishlist-detail-item"
                >

                    <!-- Product Image -->
                    <div class="wishlist-detail-product-image">

                        <img
                            src="<?= esc($product['main_image']); ?>"
                            alt="<?= esc($product['product_name']); ?>"
                        >

                    </div>


                    <!-- Product Information -->
                    <div class="wishlist-detail-product-info">

                        <h3>
                            <?= esc($product['product_name']); ?>
                        </h3>
                    </div>


                    <!-- Actions -->
                    <div class="wishlist-detail-actions">

                        <?php if ((int)$product['is_in_stock'] === 1): ?>

                            <button
                                type="button"
                                class="wishlist-move-cart-btn"
                                onclick="event.stopPropagation(); moveWishlistToCart(<?= (int)$product['product_id']; ?>, <?= (int)$wishlist['id']; ?>);"
                            >
                                <i class="fa-solid fa-cart-shopping"></i>
                                Move to Cart
                            </button>

                        <?php else: ?>

                            <button
                                type="button"
                                class="wishlist-notify-btn"
                                onclick="event.stopPropagation(); notifyWishlistProduct(<?= (int)$product['product_id']; ?>);"
                            >
                                <i class="fa-regular fa-bell"></i>
                                Notify Me
                            </button>

                        <?php endif; ?>

                        <?php if ($product['acquired_status'] === 'Y'): ?>
                            <button
                                type="button"
                                class="wishlist-fulfilled-btn fulfilled"
                                disabled
                            >
                                <i class="fa-solid fa-check"></i>
                                Fulfilled
                            </button>
                        <?php else: ?>
                            <button
                                type="button"
                                class="wishlist-fulfilled-btn"
                                onclick="event.stopPropagation(); markWishlistFulfilled(<?= (int)$product['id']; ?>, <?= (int)$wishlist['id']; ?>);"
                            >
                                <i class="fa-solid fa-check"></i>
                                Fulfill
                            </button>
                        <?php endif; ?>

                        <button
                            type="button"
                            class="wishlist-item-delete-btn"
                            onclick="event.stopPropagation(); deleteWishlistItem(<?= (int)$product['id']; ?>, <?= (int)$wishlist['id']; ?>);"
                        >
                            <i class="fa-regular fa-trash-can"></i>
                            Delete
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="wishlist-detail-empty">

                <div class="wishlist-detail-empty-icon">
                    <i class="fa-regular fa-heart"></i>
                </div>

                <h3>
                    This wishlist is empty
                </h3>

                <p>
                    Add products to this wishlist to see them here.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>