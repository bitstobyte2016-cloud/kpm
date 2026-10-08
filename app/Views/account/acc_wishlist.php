<div class="wishlist-page">

    <!-- Wishlist Header -->
    <div class="wishlist-page-header">

        <div>
            <h1>My Wishlists</h1>

            <p>
                Create and manage your wishlists.
            </p>
        </div>

        <button
            type="button"
            class="wishlist-new-btn"
            onclick="createWishlist()"
        >
            <i class="fa-solid fa-plus"></i>
            New Wishlist
        </button>

    </div>


    <!-- Wishlist List -->
    <div class="wishlist-list">

        <?php if (!empty($wishlists)): ?>

            <?php foreach ($wishlists as $wishlist): ?>

                <div
                    class="wishlist-card"
                    onclick="openWishlistDetail(<?= (int)$wishlist['id']; ?>)"
                >

                    <!-- Wishlist Icon -->
                    <div class="wishlist-card-icon">
                        <i class="fa-regular fa-heart"></i>
                    </div>


                    <!-- Wishlist Information -->
                    <div class="wishlist-card-info">

                        <h3>
                            <?= esc($wishlist['wishlist_name']); ?>
                        </h3>

                        <span>
                            <?= (int)$wishlist['item_count']; ?> items
                        </span>

                    </div>


                    <!-- Product Preview Images -->
                    <div class="wishlist-product-preview">

                        <?php if (!empty($wishlist['products'])): ?>

                            <?php foreach ($wishlist['products'] as $product): ?>

                                <?php if (!empty($product['main_image'])): ?>

                                    <div class="wishlist-preview-image">

                                        <img
                                            src="<?= esc($product['main_image']); ?>"
                                            alt="<?= esc($product['product_name']); ?>"
                                        >

                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>


                            <?php if (
                                !empty($wishlist['remaining_count']) &&
                                $wishlist['remaining_count'] > 0
                            ): ?>

                                <div class="wishlist-more-count">
                                    +<?= (int)$wishlist['remaining_count']; ?>
                                </div>

                            <?php endif; ?>

                        <?php endif; ?>

                    </div>


                    <!-- Delete -->
                    <button
                        type="button"
                        class="wishlist-delete-btn"
                        onclick="
                            event.stopPropagation();
                            deleteWishlist(<?= (int)$wishlist['id']; ?>);
                        "
                    >
                        <i class="fa-regular fa-trash-can"></i>
                        Delete
                    </button>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="wishlist-empty">

                <div class="wishlist-empty-icon">
                    <i class="fa-regular fa-heart"></i>
                </div>

                <h3>
                    No wishlists yet
                </h3>

                <p>
                    Create your first wishlist and start saving your favourite K-Pop products.
                </p>

                <button
                    type="button"
                    class="wishlist-new-btn"
                    onclick="createWishlist()"
                >
                    <i class="fa-solid fa-plus"></i>
                    New Wishlist
                </button>

            </div>

        <?php endif; ?>

    </div>

</div>