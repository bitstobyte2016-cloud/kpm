<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

    <div class="container">
        <div class="breadcrumb-area">
            <a href="<?= base_url(); ?>">Home </a>
            <span>/</span>
            <span class="active">
                Categories
            </span>
        </div>

        <div class="category-page-layout">
            <aside class="category-sidebar">
                <div class="category-sidebar-box">
                    <div class="category-sidebar-title">
                        Categories
                    </div>
                    
                    <div class="category-sidebar-list">
                        <?php foreach($category as $row){ ?>
                            <?php if(empty($row['scat'])){ ?>
                                <a href="javascript:void(0)" class="category-sidebar-item
                                        <?= ($selected_category== $row['id']) ? 'active' : ''; ?> "
                                    data-category="<?= $row['id']; ?>" >

                                    <div class="category-sidebar-left">
                                        <img src="<?= $row['cat_img']; ?>">
                                        <span>
                                            <?= $row['category_name']; ?>
                                        </span>
                                    </div>
                                </a>

                            <?php } else { ?>
                                <div
                                    class="category-parent-title " >
                                    <img src="<?= $row['cat_img']; ?>" >
                                    <span>
                                        <?= $row['category_name']; ?>
                                    </span>
                                </div>

                                <?php foreach($row['scat']as $sub){ ?>
                                    <a href="javascript:void(0)"
                                        class="category-sidebar-item subcategory-item
                                            <?= ($selected_category== $sub['id'])? 'active' : '';?> "
                                        data-category="<?= $sub['id']; ?>" >
                                        <span>
                                            <?= $sub['category_name']; ?>
                                        </span>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </aside>



            <div class="category-products-content">
                <div class="category-products-topbar">
                    <div>
                        <h1 class="category-page-title">
                            <?= $category_name; ?>
                        </h1>
                        <div class="category-page-count">
                            Showing
                            <?= count($products); ?>
                            results
                        </div>
                    </div>

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

                <div
                    class="products-grid"
                    id="productsGrid"
                ></div>

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

    let allProducts =<?= json_encode($products); ?>;
    let filteredProducts = [...allProducts];
    let currentPage = 1;
    let itemsPerPage = 24;

    //fill product cards
    function renderProducts()
    {
        const grid =document.getElementById( "productsGrid");
        grid.innerHTML = "";

        const start =(currentPage - 1)* itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedProducts = filteredProducts.slice(start, end);

        paginatedProducts.forEach(product => {
            let labelClass = "";
            let labelText = "";


            if(parseInt(product.product_qty) <= 0){
                labelClass = "out-stock";
                labelText = "Out of Stock";
            } else if(product.on_hand == 'Y'){
                labelClass = "on-hand";
                labelText = "On Hand";
            }else if(
                new Date(product.release_date)
                > new Date()
            ){
                labelClass = "pre-order";
                labelText = "Pre-Order";
            }

            let priceHTML = "";

            if(product.discounted_price && parseFloat( product.discounted_price ) > 0 ){
                priceHTML = `
                    <div class="product-price-wrap">
                        <div class="product-old-price">
                            ₹${Number(product.price).toLocaleString()}
                        </div>
                        <div class="product-price">
                            ₹${Number(product.discounted_price).toLocaleString()}
                        </div>
                    </div> `;
            }else{
                priceHTML = `
                    <div class="product-price">
                        ₹${Number(product.price).toLocaleString()}
                    </div> `;
            }
            grid.innerHTML += `
                <div class="product-card">
                    ${ labelText != ""
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
                </div>`;
        });

        renderPagination();
    }

    //to apply sorting to products
    function applySorting()
    {
        const sort =document.getElementById( "sortProducts" ).value;

        if(sort == "newest"){
            filteredProducts.sort((a,b) =>
                new Date(b.release_date)
                -
                new Date(a.release_date)
            );
        }else if(sort == "az"){
            filteredProducts.sort((a,b) =>
                a.product_name.localeCompare(
                    b.product_name
                )
            );
        }else if(sort == "za"){
            filteredProducts.sort((a,b) =>
                b.product_name.localeCompare(
                    a.product_name
                )
            );
        }else if(sort == "price_low"){
            filteredProducts.sort((a,b) =>
                parseFloat(a.price)
                -
                parseFloat(b.price)
            );
        }else if(sort == "price_high"){
            filteredProducts.sort((a,b) =>
                parseFloat(b.price)
                -
                parseFloat(a.price)
            );
        }
        currentPage = 1;
        renderProducts();
    }

    //apply pagination
    function renderPagination()
    {
        const wrap =document.getElementById( "paginationWrap" );
        wrap.innerHTML = "";
        const totalPages =Math.ceil( filteredProducts.length / itemsPerPage );

        if(totalPages <= 1){
            return;
        }

        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage - 1})"
                ${currentPage == 1 ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        `;

        let startPage =Math.max(currentPage - 2, 1);
        let endPage = Math.min(currentPage + 2, totalPages);

        if(startPage > 1){
            wrap.innerHTML += `
                <button
                    onclick="changePage(1)"
                >
                    1
                </button>
            `;

            if(startPage > 2){
                wrap.innerHTML += `
                    <span class="pagination-dots">
                        ...
                    </span>
                `;
            }
        }

        for( let i = startPage; i <= endPage; i++ ){
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
        
        if(endPage < totalPages){
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

        wrap.innerHTML += `
            <button
                onclick="changePage(${currentPage + 1})"
                ${currentPage == totalPages ? 'disabled' : ''}
            >
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        `;
    }

    //to change page
    function changePage(page)
    {
        const totalPages =Math.ceil( filteredProducts.length / itemsPerPage  );

        if( page < 1||page > totalPages ){
            return;
        }

        currentPage = page;
        renderProducts();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    //listener for sorting dropdown
    document.getElementById("sortProducts").addEventListener("change",applySorting);

    //listener for category change
    $(document).on( "click",  ".category-sidebar-item", function(e){
            e.preventDefault();

            $(".category-sidebar-item").removeClass("active");

            $(this) .addClass("active");

            let cat_id =  $(this).data("category");

            $.post( "<?= site_url(  'getProductsByCat'  ); ?>", { cat_id : cat_id }, function(response){
                    allProducts = JSON.parse(response);
                    filteredProducts = [...allProducts];
                    currentPage = 1;
                    applySorting();
                }
            );
        }
    );

    //call function to apply initial sorting
    applySorting();

</script>

<?= $this->endSection(); ?>