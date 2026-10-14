<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb-area">

        <a href="<?= base_url(); ?>">
            Home
        </a>
        <span>/</span>
        <a href="/bands_all">
            Brands
        </a>
        <span>/</span>
        <span class="active">
            <?= esc($brand_name); ?>
        </span>
    </div>


    <div class="category-page-layout">

        <!-- left bar -->
        <aside class="category-sidebar">
            <div class="category-sidebar-box">
                <div class="category-sidebar-title">
                    Filter by Category
                </div>

                <div class="category-sidebar-list">
                    <!-- ALL -->
                    <a
                        href="javascript:void(0)"
                        class="category-sidebar-item active"
                        data-category="all"
                    >
                        <div class="category-sidebar-left">
                            <span>
                                All Categories
                            </span>
                        </div>
                    </a>

                    <!-- ALBUMS -->
                    <a
                        href="javascript:void(0)"
                        class="category-sidebar-item"
                        data-category="1"
                    >
                        <div class="category-sidebar-left">
                            <span>
                                Albums
                            </span>
                        </div>
                    </a>

                    <!-- DVDS -->
                    <a
                        href="javascript:void(0)"
                        class="category-sidebar-item"
                        data-category="2"
                    >
                        <div class="category-sidebar-left">
                            <span>
                                DVDs
                            </span>
                        </div>
                    </a>

                    <!-- MERCHANDISE -->
                    <a
                        href="javascript:void(0)"
                        class="category-sidebar-item"
                        data-category="3"
                    >
                        <div class="category-sidebar-left">
                            <span>
                                Merchandise
                            </span>
                        </div>
                    </a>

                    <!-- UNOFFICIAL photocards-->
                    <a
                        href="javascript:void(0)"
                        class="category-sidebar-item"
                        data-category="17"
                    >
                        <div class="category-sidebar-left">
                            <span>
                                Unofficial Merch
                            </span>
                        </div>
                    </a>

                    <!-- HIDE OUT OF STOCK -->
                    <label class="filter-option">
                        <input
                            type="checkbox"
                            id="hideOutOfStock"
                        >
                        <span>
                            Hide out of stock
                        </span>
                    </label>
                </div>
            </div>
        </aside>

        <!-- right side-->
        <div class="category-products-content">
            <!-- TOP BAR -->
            <div class="category-products-topbar">
                <div>
                    <h1 class="category-page-title">
                        <?= esc($brand_name); ?>
                    </h1>

                    <div class="category-page-count">
                        Showing
                        <span id="productCount">
                            <?= count($products); ?>
                        </span>
                        results
                    </div>
                </div>

                <!-- SORT -->
                <div class="products-sort">
                    <select id="sortProducts">
                        <option value="newest">
                            Sort by: Newest
                        </option>
                        <option value="price_low">
                            Price : Low to High
                        </option>
                        <option value="price_high">
                            Price : High to Low
                        </option>
                    </select>
                </div>
            </div>

            <!-- PRODUCT GRID -->
            <div
                class="products-grid"
                id="productsGrid"
            ></div>

            <!-- PAGINATION -->
            <div class="products-pagination-wrap">
                <div
                    class="products-pagination"
                    id="paginationWrap"
                ></div>
            </div>
        </div>
    </div>
</div>


<script>

    let allProducts = <?= json_encode($products); ?>;
    let filteredProducts =  [...allProducts];
    let currentPage = 1;
    let itemsPerPage = 12;

    // render products
    function renderProducts()
    {
        const grid =  document.getElementById("productsGrid");
        grid.innerHTML = "";
        const start =(currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedProducts = filteredProducts.slice(start, end);
        document.getElementById("productCount").innerText = filteredProducts.length;

        paginatedProducts.forEach(product => {

            // product label

            let labelClass = "";
            let labelText = "";

            if(product.on_hand == "Y")
            {
                labelClass = "on-hand";
                labelText = "On Hand";
            }
            else if(
                product.release_date &&
                new Date(product.release_date) > new Date()
            )
            {
                labelClass = "pre-order";
                labelText = "Pre-Order";
            }
            else if(
                parseInt(product.product_qty) <= 0
            )
            {
                labelClass = "out-stock";
                labelText = "Out of Stock";
            }

            // price
            let priceHTML = "";

            if( product.discounted_price &&  parseFloat(product.discounted_price) > 0  )
            {
                priceHTML = `
                    <div class="product-price-wrap">
                        <div class="product-old-price">
                            ₹${Number(product.price).toLocaleString()}
                        </div>
                        <div class="product-price">
                            ₹${Number(product.discounted_price).toLocaleString()}
                        </div>
                    </div> `;
            }
            else
            {
                priceHTML = `
                    <div class="product-price">
                        ₹${Number(product.price).toLocaleString()}
                    </div> `;
            }



            // product card
            grid.innerHTML += `
                <div class="product-card">
                    ${labelText != "" ?
                        `
                        <div class="product-label ${labelClass}">
                            ${labelText}
                        </div>
                        ` :  "" }
                    <a
                        href="<?= base_url('product/'); ?>${product.id}"
                        class="product-image"
                    >
                        <img
                            src="${product.main_image}"
                            alt="${product.product_name}"
                        >
                    </a>

                    <div class="product-content">
                        <a
                            href="<?= base_url('product/'); ?>${product.id}"
                            class="product-title"
                        >
                            ${product.product_name}
                        </a>
                        ${priceHTML}
                        <div class="product-actions">
                            ${
                                parseInt(product.product_qty) <= 0
                                ?
                                `
                                <button
                                    class="add-cart-btn"
                                    type="button"
                                    onclick="notifyProduct('${product.id}')"
                                >
                                    <i class="fa-regular fa-bell"></i>
                                    Notify Me
                                </button>
                                `
                                :
                                `
                                <button
                                    class="add-cart-btn"
                                    type="button"
                                        onclick="addProductToCart('${product.id}')"
                                >
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Add to Cart
                                </button>
                                `
                            }

                            <button
                                class="wishlist-btn"
                                type="button"
                                onclick="addProductToWishlist('${product.id}')"
                            >
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div> `;
        });

        renderPagination();
    }

    // sorting
    function applySorting()
    {
        const sort = document.getElementById("sortProducts").value;

        if(sort == "newest")
        {
            filteredProducts.sort((a,b) => {
                let aNum =  parseInt( String(a.id).replace(/\D/g, "")  );
                let bNum = parseInt( String(b.id).replace(/\D/g, "")   );
                return bNum - aNum;
            });
        }
        else if(sort == "price_low")
        {
            filteredProducts.sort((a,b) =>
                parseFloat(a.price) -
                parseFloat(b.price)
            );
        }
        else if(sort == "price_high")
        {
            filteredProducts.sort((a,b) =>
                parseFloat(b.price) -
                parseFloat(a.price)
            );
        }

        currentPage = 1;
        renderProducts();
    }

    // category filter
    $(document).on( "click", ".category-sidebar-item", function(e)
        {
            e.preventDefault();

            $(".category-sidebar-item") .removeClass("active");

            $(this).addClass("active");

            let cat_id =  $(this).data("category");

            if(cat_id == "all")
            {
                filteredProducts =
                    [...allProducts];
            }
            else
            {
                filteredProducts =
                    allProducts.filter(product =>
                        String(product.cat_id) ==
                        String(cat_id)
                    );
            }
            applyStockFilter();

        }
    );


    // hide out of stock
    $(document).on(  "change", "#hideOutOfStock",
        function()
        {
            applyStockFilter();
        }
    );

    function applyStockFilter()
    {

        let activeCategory = $(".category-sidebar-item.active")  .data("category");

        // FIRST APPLY CATEGORY
        if(activeCategory == "all")
        {
            filteredProducts =
                [...allProducts];
        }
        else
        {
            filteredProducts =
                allProducts.filter(product =>
                    String(product.cat_id) ==
                    String(activeCategory)
                );
        }

        // THEN HIDE OUT OF STOCK
        if($("#hideOutOfStock").is(":checked") )
        {
            filteredProducts =
                filteredProducts.filter(product =>
                    parseInt(product.product_qty) > 0
                );
        }

        applySorting();

    }

    // pagination
    function renderPagination()
    {
        const wrap =  document.getElementById("paginationWrap");
        wrap.innerHTML = "";
        const totalPages = Math.ceil( filteredProducts.length /  itemsPerPage  );

        if(totalPages <= 1)
        {
            return;
        }

        // PREVIOUS
        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage - 1})"
                ${currentPage == 1 ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button> `;

        let startPage =Math.max( currentPage - 2,  1  );
        let endPage = Math.min( currentPage + 2, totalPages  );

        if(startPage > 1)
        {
            wrap.innerHTML += `
                <button
                    onclick="changePage(1)"
                >  1 </button> `;

            if(startPage > 2)
            {
                wrap.innerHTML += `
                    <span class="pagination-dots">
                        ...
                    </span>   `;
            }
        }

        for( let i = startPage;  i <= endPage; i++ )
        {
            wrap.innerHTML += `
                <button
                    class="${i == currentPage ? 'active' : ''}"
                    onclick="changePage(${i})"
                >
                    ${i}
                </button> `;
        }

        if(endPage < totalPages)
        {
            if(endPage < totalPages - 1)
            {
                wrap.innerHTML += `
                    <span class="pagination-dots">
                        ...
                    </span>  `;
            }

            wrap.innerHTML += `
                <button
                    onclick="changePage(${totalPages})"
                >
                    ${totalPages}
                </button>  `;
        }

        // NEXT
        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage + 1})"
                ${currentPage == totalPages ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-right"></i>
            </button>  `;
    }

    // change page
    function changePage(page)
    {
        const totalPages = Math.ceil(  filteredProducts.length /  itemsPerPage );

        if( page < 1 ||  page > totalPages  )
        {return; }

        currentPage = page;

        renderProducts();

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }

    // sort listner
    document
        .getElementById("sortProducts")
        .addEventListener(
            "change",
            applySorting
        );

    // initial load
    applySorting();

</script>

<?= $this->endSection(); ?>