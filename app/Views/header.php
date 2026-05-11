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
        <div class="logo-area">
            <img src="/Images/logo.png" alt="K-Pop Merch Logo" class="header-logo">
            <div class="site-title-area">
                <div class="site-title">K-Pop Merch</div>
                <div class="site-subtitle">Your Ultimate K-Pop Store</div>
            </div>
        </div>

        <div class="search-area">
            <form action="/search" method="get" class="search-form">
                <input type="text" name="q" placeholder="Search for albums, merch, artists..." class="search-input">
                <button type="submit" class="search-button">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
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
                                    <li><a href="#"><img src="/Images/Icons/ic_cd.png" class="cat-icon" alt=""> Photobooks</a></li>
                                    <li><a href="#"><img src="/Images/Icons/ic_dvd.png" class="cat-icon" alt=""> DVDs</a></li>
                                    <li><a href="#"><img src="/Images/Icons/ic_goods.png" class="cat-icon" alt=""> Merch / Goods</a></li>
                                    <li><a href="#"><img src="/Images/Icons/ic_unoff.png" class="cat-icon" alt=""> Unofficial Goods</a></li>
                                    <li><a href="#"><img src="/Images/Icons/ic_mb.png" class="cat-icon" alt=""> Merch Boxes</a></li>
                                </ul>
                                <a href="/categories" class="view-all-link">View all categories &rarr;</a>
                            </div>
                            <div class="mega-menu-featured">
                                <img src="/Images/img8.jpg" alt="Featured Categories" class="featured-img">
                                <h4>Explore our wide range</h4>
                                <p>From albums to merch, find everything you need in one place.</p>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item has-dropdown">
                    <a href="#">Bands <i class="fa-solid fa-chevron-down nav-chevron"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">BTS</a></li>
                        <li><a href="#">BLACKPINK</a></li>
                        <li><a href="#">TWICE</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="#">Pre-order / New In</a></li>
                <li class="nav-item"><a href="#">On-hand</a></li>
            </ul>
        </nav>
    </div>
</header>
