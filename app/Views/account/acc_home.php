
            <main class="account-content">
                <!-- profile -->
                <div class="account-profile-card">
                    <div class="profile-avatar">
                        <i class="fa-regular fa-user"></i>
                    </div>

                    <div class="profile-details">
                        <h2>
                            Hello, <?= esc($customer['first_name'] . ' ' . $customer['last_name']); ?>
                            <span class="profile-wave">👋</span>
                        </h2>
                        <p>
                            <i class="fa-regular fa-envelope"></i>
                            <?= esc($customer['email']); ?>
                        </p>
                        <?php

                        if($customer['acc_status'] == 1){
                            $accountStatus = 'Active';
                        } elseif($customer['acc_status'] == 2){
                            $accountStatus = 'On-Hold';
                        } elseif($customer['acc_status'] == 3){
                            $accountStatus = 'Blacklisted';
                        } else {
                            $accountStatus = 'Unknown';
                        } ?>
                        <p>
                            <i class="fa-solid fa-circle-check"></i>
                            Account Status: <?= $accountStatus; ?>
                        </p>
                        
                        <!-- add lines for account status -->
                        <?php if($customer['acc_status'] == 2): ?>
                            <div class="account-status-notice on-hold">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>
                                    Only Pre-paid orders will be accepted for this account. 
                                    All COD orders will be cancelled automatically.
                                </span>
                            </div>
                        <?php elseif($customer['acc_status'] == 3): ?>

                            <div class="account-status-notice blacklisted">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>
                                    Your account is blacklisted and all orders will be cancelled automatically.
                                    Please contact support.
                                </span>
                            </div>

                        <?php endif; ?>
                    </div>
                    <button
                        type="button"
                        class="edit-profile-btn"
                        onclick="openEditProfile()"
                    >
                        <i class="fa-solid fa-pen"></i>
                        Edit Profile
                    </button>

                </div>

                <!-- change password -->
                <div class="account-section">
                    <div class="account-section-header">
                        <div>
                            <i class="fa-solid fa-lock"></i>
                            <h3>
                                Change Password
                            </h3>
                        </div>
                    </div>

                    <div class="password-form">
                        <div class="form-group">
                            <label>
                                Old Password
                            </label>
                            <div class="password-input-wrap">
                                <input
                                    type="password"
                                    id="oldPassword"
                                    placeholder="Enter old password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('oldPassword', this)"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                New Password
                            </label>
                            <div class="password-input-wrap">
                                <input
                                    type="password"
                                    id="newPassword"
                                    placeholder="Enter new password"
                                >
                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('newPassword', this)"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>

                            </div>

                        </div>

                        <div class="form-group">

                            <label>
                                Confirm New Password
                            </label>

                            <div class="password-input-wrap">

                                <input
                                    type="password"
                                    id="confirmPassword"
                                    placeholder="Confirm new password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('confirmPassword', this)"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>

                            </div>

                        </div>

                        <div class="password-button-wrap">

                            <button
                                type="button"
                                class="change-password-btn"
                                onclick="changePassword()"
                            >
                                <i class="fa-solid fa-key"></i>
                                Change Password
                            </button>

                        </div>

                    </div>

                </div>
                
                <!-- store credit -->
                <?php if((float)$customer['store_credit'] > 0): ?>
                    <div class="account-store-credit">
                        <i class="fa-solid fa-wallet"></i>
                        <span>
                            Store Credit:
                            <strong>
                                ₹<?= number_format((float)$customer['store_credit'], 2); ?>
                            </strong>
                        </span>
                        <p>
                            Use your store credit at checkout to place orders. 
                            This credit has no expiration date.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- REWARDS + COUPONS -->
                <div class="account-cards-grid">

                    <!-- REWARDS -->
                    <div class="account-section rewards-card">

                        <div class="account-section-header">

                            <div>
                                <i class="fa-solid fa-gift"></i>
                                <h3>
                                    Rewards
                                </h3>
                            </div>

                        </div>

                        <div class="rewards-summary">

                            <div class="reward-points">
                                <strong>
                                    <?= number_format((int)$customer['redeem_points_balance']); ?>
                                </strong>

                                <span>
                                    Reward Points
                                </span>
                            </div>

                            <p>
                                Use your reward points to get discounts
                                on your next purchase.
                            </p>

                        </div>

                        <div class="reward-list">
                            <?php
                            $rewardPoints = (int)$customer['redeem_points_balance'];

                            $rewards = [
                                ['amount' => 5,   'points' => 500],
                                ['amount' => 10,  'points' => 1000],
                                ['amount' => 20,  'points' => 2000],
                                ['amount' => 50,  'points' => 5000],
                                ['amount' => 100, 'points' => 10000] ];
                            ?>

                            <?php foreach($rewards as $reward): ?>
                                <div class="reward-item">
                                    <div class="reward-icon">
                                        <i class="fa-solid fa-coins"></i>
                                    </div>
                                    <div class="reward-info">
                                        <span>
                                            Get 
                                        </span>
                                        <strong>
                                            ₹<?= $reward['amount']; ?>
                                        </strong>
                                        <span>
                                            <?= " for ".number_format($reward['points']); ?> Points
                                        </span>
                                    </div>
                                    <?php if($rewardPoints >= $reward['points']): ?>
                                        <button
                                            type="button"
                                            class="reward-action"
                                            onclick="redeemReward(<?= $reward['points']; ?>, <?= $reward['amount']; ?>)"
                                        >
                                            REDEEM
                                        </button>
                                    <?php else: ?>
                                        <button
                                            type="button"
                                            class="reward-action"
                                            disabled
                                        >
                                            REDEEM
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- COUPONS -->
                    <div class="account-section coupons-card">
                        <div class="account-section-header">
                            <div>
                                <i class="fa-solid fa-ticket"></i>
                                <h3>
                                    Coupons
                                </h3>
                            </div>
                        </div>

                        <div class="coupon-summary">
                            <p>
                                Apply your available coupons at checkout.
                            </p>
                        </div>

                        <div class="coupon-list">
                            <?php if(!empty($coupons)): ?>
                                <?php foreach($coupons as $coupon): ?>
                                    <div class="coupon-item">
                                        <div class="coupon-icon">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>
                                        <div class="coupon-info">
                                            <strong>
                                                <?= esc($coupon['coupon_code']); ?>
                                            </strong>

                                            <span>
                                                <?php if($coupon['amount_type'] == 'percent'): ?>
                                                    <?= number_format((float)$coupon['amount'], 0); ?>% Off
                                                <?php else: ?>
                                                    ₹<?= number_format((float)$coupon['amount'], 2); ?> Off
                                                <?php endif; ?>
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            class="coupon-action"
                                            onclick="copyCouponCode('<?= esc($coupon['coupon_code']); ?>')"
                                        >
                                            APPLY
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="account-no-coupons">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>No active coupons</span>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </main>