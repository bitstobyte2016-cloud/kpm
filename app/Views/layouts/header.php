<header class="main-header">

    <div class="header-top-bar">
        <div class="top-bar-item">
            <i class="fa-regular fa-circle-check"></i> 100% Official Products
        </div>
        <div class="top-bar-item">
            <i class="fa-solid fa-truck-fast"></i> Secure Shipping
        </div>
        <div class="top-bar-item">
            <i class="fa-solid fa-shoe-prints"></i> Tracked Orders
        </div>
        <div class="top-bar-item">
            <i class="fa-solid fa-lock"></i> Secure Payments
        </div>
    </div>

    <div class="header-main-bar">
                
        <a href="<?= base_url(); ?>"class="logo-area">
            <img src="/Images/logo.png" alt="K-Pop Merch Logo" class="header-logo">
            <div class="site-title-area">
                <div class="site-title">
                    K-Pop Merch
                </div>
                <div class="site-subtitle">
                    Your Ultimate K-Pop Store
                </div>
            </div>
        </a>

        <div class="search-area">
            <form action="/search" method="get" class="search-form">
                <input type="text" name="q"
                    placeholder="Search for albums, merch, artists..."
                    class="search-input" id="liveSearchInput" autocomplete="off" >

                <button type="submit" class="search-button">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
            <!-- SEARCH DROPDOWN -->
            <div class="live-search-results" id="liveSearchResults"></div>
        </div>

        <div class="actions-area">
            <a href="/register" class="action-item sign-in-action">
                <i class="fa-regular fa-user"></i>
                <span>Sign In / Sign Up</span>
            </a>
            <a href="/cart" class="action-item cart-action">
                <div class="cart-icon-wrapper">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-badge">0</span>
                </div>
                <span>Cart</span>
            </a>
        </div>
    </div>

    <div class="header-nav-bar">
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item has-dropdown">
                    <a href="#">Categories <i class="fa-solid fa-chevron-down nav-chevron"></i></a>
                    <div class="dropdown-menu mega-menu">
                        <div class="mega-menu-content">
                            <div class="mega-menu-list">
                                <ul class="categories-list">
                                        <?php foreach($categories as $row){ 
                                            if(count($row["scat"]) == 0){ 
                                                ?>
                                                    <li>
                                                        <a href="<?php echo base_url()."category/".$row["category_name"]."/".$row["id"]."/1/0/20";?>"><img class="cat-icon" src="<?php echo base_url().$row["cat_img"];?>">
                                                                    <?php echo $row["category_name"];?></a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><a href="<?php echo base_url()."officialgoods/".$row["category_name"]."/".$row["id"]."/1/0/20/0";?>"><img  class="cat-icon" src="<?php echo $row["cat_img"];?>">
                                                        <?php echo $row["category_name"];?></a>
                                                    </li>
                                                <?php } 
                                        } ?>
                                </ul>
                                <a href="/categories_all" class="view-all-link">View all categories &rarr;</a>
                            </div>
                            <div class="mega-menu-featured">
                                <img src="/Images/cat_img.png" alt="Featured Categories" class="featured-img">
                                <h4>Explore our wide range</h4>
                                <p>From albums to merch, find everything you need in one place!</p>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item has-dropdown">
                    <a href="#">Bands <i class="fa-solid fa-chevron-down nav-chevron"></i></a>
                    <div class="dropdown-menu mega-menu">
                        <div class="mega-menu-content">
                            <div class="mega-menu-list">
                                <ul class="bands-grid-list">
                                    <?php foreach($brands as $row){ 
                                                ?>
                                                    <li>
                                                        <a href="<?php echo base_url()."artist/".$row["brand_name"]."/".$row["id"]."/1/0/20";?>">
                                                                    <?php echo $row["brand_name"];?></a>
                                                    </li>
                                        <?php } ?>
                                </ul>
                                <a href="/bands_all" class="view-all-link">View all bands &rarr;</a>
                            </div>
                            <div class="mega-menu-featured bands-featured">
                                <div class="featured-text-area">
                                    <h4>Shop by your<br>favorite band</h4>
                                    <p>Browse official merch<br>from all your favorite<br>K-Pop artists.</p>
                                </div>
                                <img src="/Images/img9.jpg" alt="Featured Bands" class="featured-band-img">
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item"><a href="/preorder">Pre-order / New In</a></li>
                <li class="nav-item"><a href="/onhand">On-hand</a></li>
            </ul>
        </nav>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

    let searchTimer;

    $(".search-input").on("keyup", function(){

        let query =$(this).val().trim();
        clearTimeout(searchTimer);
        // MINIMUM CHARACTERS
        if(query.length < 2){
            $("#liveSearchResults").hide().html("");
            return;
        }

        // DEBOUNCE
        searchTimer = setTimeout(function(){
            $.post(
                "<?= base_url('live-search') ?>",{query : query},
                function(data){

                    // CLEAR RESULTS
                    $("#liveSearchResults").html("");
                    
                    // NO RESULTS
                    if(data.length === 0){
                        $("#liveSearchResults").html(`
                            <div class="search-empty">
                                No products found
                            </div>
                        `);
                        $("#liveSearchResults").show();
                        return;
                    }

                    // CREATE GRID
                    $("#liveSearchResults").html(
                        '<div class="live-search-grid"></div>'
                    );

                    // LOOP PRODUCTS
                    $.each(data, function(index, product){
                        let stockLabel = "";
                        let qty =parseInt(product.product_qty) || 0;

                        // OUT OF STOCK
                        if(qty <= 0)
                        {
                            stockLabel = `
                                <div class="search-out-stock">
                                    Out of Stock
                                </div>
                            `;
                        }

                        // ON HAND
                        else if(product.on_hand == 'Y')
                        {
                            stockLabel = `
                                <div class="search-on-hand">
                                    On Hand
                                </div>
                            `;
                        }

                        // CARD
                        let card = `
                            <a href="/product/${product.id}" class="search-result-card">
                                <div class="search-card-image">
                                    <img 
                                        src="${product.main_image}"
                                        alt="${product.product_name}">
                                </div>
                                <div class="search-card-content">
                                    ${stockLabel}
                                    <div class="search-product-name">
                                        ${product.product_name}
                                    </div>
                                </div>
                            </a>
                        `;

                        $(".live-search-grid").append(card);
                    });
                    $("#liveSearchResults").show();
                },
                "json"
            );
        }, 300);
    });


    // CLOSE DROPDOWN
    $(document).on("click", function(e){
        if(!$(e.target).closest(".search-area").length){
            $("#liveSearchResults").hide();
        }

    });

</script>