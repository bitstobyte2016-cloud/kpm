<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">

    <!-- =========================================
        BREADCRUMB
    ========================================== -->

    <div class="breadcrumb-area">

        <a href="<?= base_url(); ?>">
            Home
        </a>

        <span>/</span>

        <span class="active">
            Products
        </span>

    </div>


    <!-- =========================================
        MAIN LAYOUT
    ========================================== -->

    <div class="products-page-layout">

        <!-- =====================================
            SIDEBAR
        ====================================== -->

        <aside class="products-sidebar">

            <!-- PRODUCT TYPE -->

            <div class="filter-box">

                <div class="filter-title">
                    Product Type
                </div>

                <div class="filter-options">

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="all"
                            checked
                        >

                        <span>All Products</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="albums"
                        >

                        <span>Albums</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="dvds"
                        >

                        <span>DVDs</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="merch"
                        >

                        <span>Merchandise</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="unofficial"
                        >

                        <span>Unofficial Products</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="merchbox"
                        >

                        <span>Merch Boxes</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="ptype"
                            value="pins"
                        >

                        <span>Enamel Pins</span>

                    </label>

                </div>

            </div>


            <!-- AVAILABILITY -->

            <div class="filter-box">

                <div class="filter-title">
                    Availability
                </div>

                <div class="filter-options">

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="availability"
                            value="all"
                            checked
                        >

                        <span>All</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="availability"
                            value="instock"
                        >

                        <span>In Stock</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="availability"
                            value="onhand"
                        >

                        <span>On Hand</span>

                    </label>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="availability"
                            value="preorder"
                        >

                        <span>Pre-Order</span>

                    </label>

                </div>

            </div>


            <!-- RESET -->

            <button
                class="reset-filter-btn"
                id="resetFilters"
            >

                <i class="fa-solid fa-rotate-left"></i>

                Reset Filters

            </button>

        </aside>



        <!-- =====================================
            PRODUCTS CONTENT
        ====================================== -->

        <div class="products-content">

            <!-- TOPBAR -->

            <div class="products-topbar">

                <div>

                    <h1 class="products-page-title">

                        <?= $search_query; ?>

                    </h1>

                    <div
                        class="products-count"
                        id="productsCount"
                    >

                        Showing
                        <?= count($products); ?>
                        results

                    </div>

                </div>


                <!-- SORT -->

                <div class="products-sort">

                    <select id="sortProducts">

                        <option value="newest">
                            Newest First
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


            <!-- PRODUCTS GRID -->

            <div
                class="products-grid"
                id="productsGrid"
            ></div>


            <!-- NO RESULTS -->

            <div
                class="no-filter-results"
                id="noFilterResults"
                style="display:none;"
            >

                No items for this filter

            </div>


            <!-- PAGINATION -->

            <div class="products-pagination-wrap">

                <!-- ITEMS PER PAGE -->

                <div class="products-per-page">

                    <span>Show</span>

                    <select id="itemsPerPage">

                        <option value="20">
                            20
                        </option>

                        <option value="40">
                            40
                        </option>

                        <option value="60">
                            60
                        </option>

                        <option value="80">
                            80
                        </option>

                        <option value="100">
                            100
                        </option>

                    </select>

                    <span>items</span>

                </div>


                <!-- PAGINATION -->

                <div
                    class="products-pagination"
                    id="paginationWrap"
                ></div>

            </div>

        </div>

    </div>

</div>


<script>

const allProducts =
    <?= json_encode($products); ?>;

</script>


<script>

let filteredProducts =
    [...allProducts];

let currentPage = 1;

let itemsPerPage = 20;


// =========================================
// RENDER PRODUCTS
// =========================================

function renderProducts()
{
    const grid =
        document.getElementById(
            "productsGrid"
        );

    const noResults =
        document.getElementById(
            "noFilterResults"
        );

    grid.innerHTML = "";

    // NO RESULTS

    if(filteredProducts.length == 0){

        grid.style.display = "none";

        noResults.style.display = "flex";

        document.getElementById(
            "paginationWrap"
        ).innerHTML = "";

        document.getElementById(
            "productsCount"
        ).innerHTML = `
            Showing 0 results
        `;

        return;
    }

    grid.style.display = "grid";

    noResults.style.display = "none";

    // PAGINATION

    const start =
        (currentPage - 1)
        * itemsPerPage;

    const end =
        start + itemsPerPage;

    const paginatedProducts =
        filteredProducts.slice(start, end);

    // LOOP

    paginatedProducts.forEach(product => {

        let labelClass = "";
        let labelText = "";

        // OUT OF STOCK

        if(
            parseInt(product.product_qty) <= 0
        ){

            labelClass = "out-stock";
            labelText = "Out of Stock";

        }

        // ON HAND

        else if(product.on_hand == 'Y'){

            labelClass = "on-hand";
            labelText = "On Hand";

        }

        // PREORDER

        else if(
            new Date(product.release_date)
            > new Date()
        ){

            labelClass = "pre-order";
            labelText = "Pre-Order";

        }

        // PRICE

        let priceHTML = "";

        if(
            product.discounted_price
            &&
            parseFloat(
                product.discounted_price
            ) > 0
        ){

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
        else{

            priceHTML = `

                <div class="product-price">

                    ₹${Number(product.price).toLocaleString()}

                </div>

            `;
        }

        // CARD

        grid.innerHTML += `

            <div class="product-card">

                ${
                    labelText != ""
                    ?

                    `<div class="product-label ${labelClass}">
                        ${labelText}
                    </div>`

                    :

                    ""
                }

                <a
                    href="/product/${product.id}"
                    class="product-image"
                >

                    <img
                        src="${product.main_image}"
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

    updateCounts();

    renderPagination();
}


// =========================================
// COUNTS
// =========================================

function updateCounts()
{
    document.getElementById(
        "productsCount"
    ).innerHTML = `

        Showing
        ${filteredProducts.length}
        results

    `;
}


// =========================================
// FILTERS
// =========================================

function applyFilters()
{
    let ptype =
        document.querySelector(
            'input[name="ptype"]:checked'
        )?.value;

    let availability =
        document.querySelector(
            'input[name="availability"]:checked'
        )?.value;

    filteredProducts =
        [...allProducts];

    // PRODUCT TYPE

    if(
        ptype
        &&
        ptype != "all"
    ){

        const typeMap = {

            albums : 1,
            dvds : 2,
            merch : 3,
            unofficial : 5,
            merchbox : 6,
            pins : 34

        };

        filteredProducts =
            filteredProducts.filter(p =>

                parseInt(p.cat_id)
                === typeMap[ptype]

            );
    }

    // AVAILABILITY

    if(
        availability
        &&
        availability != "all"
    ){

        filteredProducts =
            filteredProducts.filter(p => {

                // IN STOCK

                if(
                    availability
                    == "instock"
                ){

                    return parseInt(
                        p.product_qty
                    ) > 0;
                }

                // ON HAND

                if(
                    availability
                    == "onhand"
                ){

                    return p.on_hand == 'Y';
                }

                // PREORDER

                if(
                    availability
                    == "preorder"
                ){

                    return new Date(
                        p.release_date
                    ) > new Date();
                }

                return true;

            });
    }

    currentPage = 1;

    applySorting();
}


// =========================================
// SORTING
// =========================================

function applySorting()
{
    const sort =
        document.getElementById(
            "sortProducts"
        ).value;

    // NEWEST

    if(sort == "newest"){

        filteredProducts.sort((a,b) =>

            new Date(b.release_date)
            -
            new Date(a.release_date)

        );
    }

    // A-Z

    else if(sort == "az"){

        filteredProducts.sort((a,b) =>

            a.product_name.localeCompare(
                b.product_name
            )

        );
    }

    // Z-A

    else if(sort == "za"){

        filteredProducts.sort((a,b) =>

            b.product_name.localeCompare(
                a.product_name
            )

        );
    }

    // PRICE LOW

    else if(sort == "price_low"){

        filteredProducts.sort((a,b) =>

            parseFloat(a.price)
            -
            parseFloat(b.price)

        );
    }

    // PRICE HIGH

    else if(sort == "price_high"){

        filteredProducts.sort((a,b) =>

            parseFloat(b.price)
            -
            parseFloat(a.price)

        );
    }

    renderProducts();
}


// =========================================
// PAGINATION
// =========================================

function renderPagination()
{
    const wrap =
        document.getElementById(
            "paginationWrap"
        );

    wrap.innerHTML = "";

    const totalPages =
        Math.ceil(
            filteredProducts.length
            / itemsPerPage
        );

    if(totalPages <= 1){
        return;
    }

    // =====================================
    // PREV BUTTON
    // =====================================

    wrap.innerHTML += `

        <button
            onclick="changePage(${currentPage - 1})"
            ${currentPage == 1 ? 'disabled' : ''}
        >

            <i class="fa-solid fa-chevron-left"></i>

        </button>

    `;

    // =====================================
    // PAGE RANGE
    // =====================================

    let startPage =
        Math.max(currentPage - 2, 1);

    let endPage =
        Math.min(currentPage + 2, totalPages);

    // FIRST PAGE

    if(startPage > 1){

        wrap.innerHTML += `

            <button
                onclick="changePage(1)"
            >
                1
            </button>

        `;

        // DOTS

        if(startPage > 2){

            wrap.innerHTML += `

                <span class="pagination-dots">
                    ...
                </span>

            `;
        }
    }

    // MIDDLE PAGES

    for(
        let i = startPage;
        i <= endPage;
        i++
    ){

        wrap.innerHTML += `

            <button
                class="
                    ${i == currentPage ? 'active' : ''}
                "
                onclick="changePage(${i})"
            >

                ${i}

            </button>

        `;
    }

    // LAST PAGE

    if(endPage < totalPages){

        // DOTS

        if(endPage < totalPages - 1){

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

    // =====================================
    // NEXT BUTTON
    // =====================================

    wrap.innerHTML += `

        <button
            onclick="changePage(${currentPage + 1})"
            ${currentPage == totalPages ? 'disabled' : ''}
        >

            <i class="fa-solid fa-chevron-right"></i>

        </button>

    `;
}


// =========================================
// CHANGE PAGE
// =========================================

function changePage(page)
{
    const totalPages =
        Math.ceil(
            filteredProducts.length
            / itemsPerPage
        );

    if(
        page < 1
        ||
        page > totalPages
    ){
        return;
    }

    currentPage = page;

    renderProducts();

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}


// =========================================
// RESET FILTERS
// =========================================

document
.getElementById("resetFilters")
.addEventListener(
    "click",
    function(){

        document.querySelector(
            'input[name="ptype"][value="all"]'
        ).checked = true;

        document.querySelector(
            'input[name="availability"][value="all"]'
        ).checked = true;

        document.getElementById(
            "sortProducts"
        ).value = "newest";

        applyFilters();

    }
);


// =========================================
// EVENTS
// =========================================

document
.querySelectorAll(
    'input[name="ptype"]'
)
.forEach(el => {

    el.addEventListener(
        "change",
        applyFilters
    );

});


document
.querySelectorAll(
    'input[name="availability"]'
)
.forEach(el => {

    el.addEventListener(
        "change",
        applyFilters
    );

});


document
.getElementById("sortProducts")
.addEventListener(
    "change",
    applySorting
);


document
.getElementById("itemsPerPage")
.addEventListener(
    "change",
    function(){

        itemsPerPage =
            parseInt(this.value);

        currentPage = 1;

        renderProducts();

    }
);


// =========================================
// INITIAL RENDER
// =========================================

applySorting();

</script>

<?= $this->endSection(); ?>