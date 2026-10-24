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
        
        <!-- Mobile hamburger menu -->
        <button
            type="button"
            class="mobile-menu-toggle"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="mobileMenuPanel"
            onclick="toggleMobileMenu()"
        >
            <i class="fa-solid fa-bars"></i>
        </button>
                
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

            <?php if (session()->has('customer_id')): ?>
                <div class="action-item user-greeting">
                    <span>Hello <?= esc(session()->get('customer_name')); ?></span>
                </div>
                <a href="<?= base_url('userhome'); ?>" class="action-item account-action">
                    <i class="fa-regular fa-user"></i>
                    <span>Account</span>
                </a>

            <?php else: ?>

                <a href="<?= base_url('signin'); ?>?redirect=<?= urlencode(current_url()); ?>" class="action-item sign-in-action">
                    <i class="fa-regular fa-user"></i>
                    <span>Sign In / Sign Up</span>
                </a>

            <?php endif; ?>

            <a href="/cart" class="action-item cart-action">
                <div class="cart-icon-wrapper">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-badge">
                        <?= (int) ($cart_count ?? 0); ?>
                    </span>
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
                                                        <a href="javascript:void(0);"   onclick="openCategory(<?= $row["id"]; ?>, '<?= esc($row["category_name"]); ?>')">
                                                            <img class="cat-icon" src="<?php echo base_url().$row["cat_img"];?>">
                                                                    <?php echo $row["category_name"];?></a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><a href="javascript:void(0);"   onclick="openCategory(<?= $row["id"]; ?>, '<?= esc($row["category_name"]); ?>')">
                                                           <img  class="cat-icon" src="<?php echo $row["cat_img"];?>">
                                                        <?php echo $row["category_name"];?></a>
                                                    </li>
                                                <?php } 
                                        } ?>
                                </ul>
                                <a href="<?php echo base_url().'categories/1'; ?>" class="view-all-link">View all categories &rarr;</a>
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
                                    <?php foreach($bands as $row){   ?>
                                            <li>
                                                 <a href="javascript:void(0);"   onclick="openBand(<?= $row["id"]; ?>, '<?= esc($row["brand_name"]); ?>')" >
                                                        <?php echo $row["brand_name"];?></a>
                                            </li>
                                    <?php } ?>
                                </ul>
                                <a href="/bands_all" class="view-all-link">View all bands &rarr;</a>
                            </div>
                            <div class="mega-menu-featured">
                                <img src="/Images/brand_img.png" alt="Featured Bands" class="featured-img">
                                <h4>Shop by your favorite band</h4>
                                <p>Browse official merch from all your favorite K-Pop Artist!</p>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item"><a href="<?php echo base_url().'preorder'; ?>">Pre-order / New In</a></li>
                <li class="nav-item"><a href="<?php echo base_url().'onhand'; ?>">On-hand</a></li>
            </ul>
        </nav>
    </div>
    

    <!-- Mobile menu backdrop -->
    <div
        class="mobile-menu-backdrop"
        id="mobileMenuBackdrop"
        onclick="closeMobileMenu()"
    ></div>

    <!-- Mobile slide-out menu -->
    <nav
        class="mobile-menu-panel"
        id="mobileMenuPanel"
        aria-label="Mobile navigation"
        aria-hidden="true"
    >
        <div class="mobile-menu-heading">
            <span>Explore K-Pop Merch</span>

            <button
                type="button"
                aria-label="Close menu"
                onclick="closeMobileMenu()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Categories dropdown -->
        <button
            type="button"
            class="mobile-menu-accordion"
            aria-expanded="false"
            aria-controls="mobileCategories"
            onclick="toggleMobileSubmenu(this, 'mobileCategories')"
        >
            <span><i class="fa-solid fa-border-all"></i> Categories</span>
            <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="mobile-menu-submenu" id="mobileCategories">
            <?php foreach ($categories as $row): ?>
                <a href="javascript:void(0);"   onclick="openCategory(<?= $row["id"]; ?>, '<?= esc($row["category_name"]); ?>')">
                    <img
                        src="<?= base_url($row['cat_img']); ?>"
                        alt=""
                    >
                    <span><?= esc($row['category_name']); ?></span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endforeach; ?>

            <a class="mobile-view-all" href="<?= base_url('categories/1'); ?>">
                View all categories →
            </a>
        </div>

        <!-- Bands dropdown -->
        <button
            type="button"
            class="mobile-menu-accordion"
            aria-expanded="false"
            aria-controls="mobileBands"
            onclick="toggleMobileSubmenu(this, 'mobileBands')"
        >
            <span><i class="fa-solid fa-users"></i> Bands</span>
            <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="mobile-menu-submenu" id="mobileBands">
            <?php foreach ($bands as $row): ?>
                <a href="javascript:void(0);"  onclick="openBand(<?= $row["id"]; ?>, '<?= esc($row["brand_name"]); ?>')" >
                    <span><?= esc($row['brand_name']); ?></span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endforeach; ?>

            <a class="mobile-view-all" href="<?= base_url('bands_all'); ?>">
                View all bands →
            </a>
        </div>

        <!-- Other menu links -->
        <a class="mobile-menu-link" href="<?= base_url('preorder'); ?>">
            <i class="fa-regular fa-calendar"></i>
            <span>Pre-order / New In</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a class="mobile-menu-link" href="<?= base_url('onhand'); ?>">
            <i class="fa-solid fa-box"></i>
            <span>On-hand</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </nav>
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
    
    //to open band detail
    function openBand(brandId, brandName)
    {
        if(!brandId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByBand"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_id',
                value: brandId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_name',
                value: brandName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
    //to open per category page
    function openCategory(catId, catName)
    {
        if(!catId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByCategory"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'cat_id',
                value: catId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'category_name',
                value: catName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
    
    function toggleMobileMenu() {
        const panel = document.getElementById('mobileMenuPanel');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        const button = document.querySelector('.mobile-menu-toggle');

        if (!panel || !backdrop || !button) return;

        const isOpen = !panel.classList.contains('is-open');

        panel.classList.toggle('is-open', isOpen);
        backdrop.classList.toggle('is-open', isOpen);
        document.body.classList.toggle('mobile-menu-open', isOpen);

        button.setAttribute('aria-expanded', String(isOpen));
        panel.setAttribute('aria-hidden', String(!isOpen));
    }

    function closeMobileMenu() {
        document.getElementById('mobileMenuPanel')
            ?.classList.remove('is-open');

        document.getElementById('mobileMenuBackdrop')
            ?.classList.remove('is-open');

        document.body.classList.remove('mobile-menu-open');

        document.querySelector('.mobile-menu-toggle')
            ?.setAttribute('aria-expanded', 'false');

        document.getElementById('mobileMenuPanel')
            ?.setAttribute('aria-hidden', 'true');
    }

    function toggleMobileSubmenu(button, submenuId) {
        const submenu = document.getElementById(submenuId);

        if (!submenu) return;

        const isOpen = !submenu.classList.contains('is-open');

        submenu.classList.toggle('is-open', isOpen);
        button.classList.toggle('is-open', isOpen);
        button.setAttribute('aria-expanded', String(isOpen));
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMobileMenu();
        }
    });


</script>