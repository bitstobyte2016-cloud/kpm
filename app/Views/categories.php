<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb-area">

        <a href="<?= base_url(); ?>">
            Home
        </a>
        <span>/</span>
        <a href="<?php echo base_url().'categories/1'; ?>">
            Categories
        </a>
        <span>/</span>
        <span class="active">
            <?= esc($category_name); ?>
        </span>
    </div>

    <!-- PRODUCTS -->
    <div class="products-page-layout">
        <div class="products-content">
            <!-- TOP BAR -->
            <div class="products-topbar">
                <div>
                    <h1 class="products-page-title">
                        <?= esc($category_name); ?>
                    </h1>
                    <div class="products-count" id="productsCount">
                        Showing 0 results
                    </div>
                    <!-- HIDE OUT OF STOCK -->
                    <label class="hide-out-stock">
                        <input
                            type="checkbox"
                            id="hideOutOfStock"
                        >
                        <span>
                            Hide out of stock
                        </span>
                    </label>
                </div>

                <!-- SORT -->
                <div class="products-sort">
                    <select id="sortProducts">
                        <option value="newest">
                            Sort by: Newest
                        </option>
                        <option value="az">
                            A-Z
                        </option>
                        <option value="za">
                            Z-A
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
    let filteredProducts = [...allProducts];
    let currentPage = 1;
    let itemsPerPage = 24;

    //render products
    function renderProducts()
    {
        const grid = document.getElementById("productsGrid");
        grid.innerHTML = "";
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedProducts = filteredProducts.slice(start, end);

        //product count
        updateProductsCount();

        //no products
        if(filteredProducts.length === 0)
        {
            grid.innerHTML = `
                <div class="search-empty">
                    No products found.
                </div>
            `;
            renderPagination();
            return;
        }


        //product cards
        paginatedProducts.forEach(product => {
            let labelClass = "";
            let labelText = "";

            //product status
            if(parseInt(product.product_qty) <= 0)
            {
                labelClass = "out-stock";
                labelText = "Out of Stock";
            }
            else if(product.on_hand == 'Y')
            {
                labelClass = "on-hand";
                labelText = "On Hand";
            }
            else if( new Date(product.release_date) > new Date() )
            {
                labelClass = "pre-order";
                labelText = "Pre-Order";
            }

            //price
            let priceHTML = "";

            if( product.discounted_price && parseFloat(product.discounted_price) > 0 )
            {
                priceHTML = `
                    <div class="product-price-wrap">
                        <div class="product-old-price">
                            ₹${Number(product.price).toLocaleString()}
                        </div>
                        <div class="product-price">
                            ₹${Number(product.discounted_price).toLocaleString()}
                        </div>
                    </div>
                `;
            }
            else
            {
                priceHTML = `
                    <div class="product-price">
                        ₹${Number(product.price).toLocaleString()}
                    </div>
                `;
            }

            //card
            grid.innerHTML += `
                <div class="product-card">
                    ${
                        labelText != ""  ?
                        ` <div class="product-label ${labelClass}">
                            ${labelText}
                        </div> ` :  ""
                    }
                    <a
                        href="/product/${product.id}"
                        class="product-image"
                    >
                        <img
                            src="${product.main_image}"
                            alt="${product.product_name}"
                        >
                    </a>
                    <div class="product-content">
                        <a
                            href="/product/${product.id}"
                            class="product-title"
                        >
                            ${product.product_name}
                        </a>
                        ${priceHTML}
                        <div class="product-actions">
                            <button class="add-cart-btn">
                                <i class="fa-solid fa-cart-shopping"></i>
                                Add to Cart
                            </button>
                            <button class="wishlist-btn">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        renderPagination();

    }

    //update count
    function updateProductsCount()
    {
        const countElement = document.getElementById("productsCount");
        const total = filteredProducts.length;

        if(total === 0)
        {
            countElement.innerHTML ="Showing 0 results";
            return;
        }

        const start = ((currentPage - 1) * itemsPerPage) + 1;
        const end = Math.min( currentPage * itemsPerPage, total );

        countElement.innerHTML =  `Showing ${start}–${end} of ${total} results`;
    }

    //sort
    function applySorting()
    {
        const sort =  document.getElementById("sortProducts").value;

        if(sort == "newest")
        {
            filteredProducts.sort((a, b) =>
                new Date(b.release_date)   -  new Date(a.release_date)
            );
        }
        else if(sort == "az")
        {
            filteredProducts.sort((a, b) =>
                a.product_name.localeCompare( b.product_name  )
            );
        }
        else if(sort == "za")
        {
            filteredProducts.sort((a, b) =>
                b.product_name.localeCompare(  a.product_name )
            );
        }
        else if(sort == "price_low")
        {
            filteredProducts.sort((a, b) =>
                parseFloat(a.price) - parseFloat(b.price)  );
        }
        else if(sort == "price_high")
        {
            filteredProducts.sort((a, b) =>
                parseFloat(b.price) - parseFloat(a.price) );
        }

        currentPage = 1;

        renderProducts();
    }


    //pagination
    function renderPagination()
    {
        const wrap = document.getElementById("paginationWrap");
        wrap.innerHTML = "";

        const totalPages = Math.ceil( filteredProducts.length / itemsPerPage  );

        if(totalPages <= 1)
        {
            return;
        }


        //previous
        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage - 1})"
                ${currentPage == 1 ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        `;

        let startPage = Math.max(currentPage - 2, 1);
        let endPage = Math.min(currentPage + 2, totalPages);

        //first page
        if(startPage > 1)
        {
            wrap.innerHTML += `
                <button
                    onclick="changePage(1)"
                >
                    1
                </button>
            `;

            if(startPage > 2)
            {
                wrap.innerHTML += `
                    <span class="pagination-dots">
                        ...
                    </span>
                `;
            }
        }

        //page numbers
        for( let i = startPage;  i <= endPage;  i++ )
        {
            wrap.innerHTML += `
                <button
                    class="${i == currentPage ? 'active' : ''}"
                    onclick="changePage(${i})"
                >
                    ${i}
                </button>
            `;
        }

        //last page
        if(endPage < totalPages)
        {
            if(endPage < totalPages - 1)
            {
                wrap.innerHTML += `
                    <span class="pagination-dots">
                        ...
                    </span>
                `;
            }

            wrap.innerHTML += `
                <button
                    onclick="changePage(${totalPages})"
                >
                    ${totalPages}
                </button>
            `;
        }

        //next
        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage + 1})"
                ${currentPage == totalPages ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        `;
    }


    //change page
    function changePage(page)
    {
        const totalPages =Math.ceil( filteredProducts.length / itemsPerPage  );

        if( page < 1 || page > totalPages )
        {
            return;
        }

        currentPage = page;
        renderProducts();

        window.scrollTo({
            top: 0,
            behavior: "smooth"  });
    }


    //sort change
    document
        .getElementById("sortProducts")
        .addEventListener(
            "change",
            applySorting
        );

    //hide out of stock
    document
        .getElementById("hideOutOfStock")
        .addEventListener( "change", function()
            {
                const hideOutOfStock = this.checked;

                if(hideOutOfStock)
                {
                    filteredProducts =
                        allProducts.filter(
                            product => parseInt(  product.product_qty ) > 0
                        );
                }else
                {
                    filteredProducts =[...allProducts];
                }

                currentPage = 1;
                applySorting();
            }
        );


    //initial load
    applySorting();

</script>


<?= $this->endSection(); ?>