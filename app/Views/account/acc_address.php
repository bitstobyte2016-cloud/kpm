<div class="account-addresses">

    <!-- Page Header -->
    <div class="account-content-header">
        <div>
            <h2>My Addresses</h2>
            <p>Manage your saved addresses for faster checkout.</p>
        </div>

        <button
            type="button"
            class="account-add-address-btn"
            onclick="openAddAddress()"
        >
            <i class="fa-solid fa-plus"></i>
            Add New Address
        </button>
    </div>

    <!-- Addresses -->
    <div class="account-address-list">

        <?php if(!empty($addresses)): ?>

            <?php foreach($addresses as $address): ?>

                <div class="account-address-card">
                    <!-- Address Icon -->
                    <div class="account-address-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    <!-- Address Details -->
                    <div class="account-address-details">
                        <h3>
                            <?= esc($address['address_title']); ?>
                        </h3>
                        <p>
                            <?= esc($address['first_name']); ?>

                            <?php if(!empty($address['last_name'])): ?>
                                <?= esc($address['last_name']); ?>
                            <?php endif; ?>
                        </p>
                        <?php if(!empty($address['company_name'])): ?>
                            <p>
                                <?= esc($address['company_name']); ?>
                            </p>
                        <?php endif; ?>
                        <p>
                            <?= esc($address['address_line1']); ?>
                        </p>
                        <?php if(!empty($address['address_line2'])): ?>
                            <p>
                                <?= esc($address['address_line2']); ?>
                            </p>
                        <?php endif; ?>
                        <p>
                            <?= esc($address['city']); ?>,
                            <?= esc($address['state']); ?> -
                            <?= esc($address['zip']); ?>
                        </p>
                        <p>
                            <?= esc($address['country']); ?>
                        </p>
                        <p>
                            <?= esc($address['contact_no']); ?>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="account-address-actions">

                        <?php if($address['is_default'] == 'Y'): ?>
                            <div class="account-address-default">
                                <i class="fa-solid fa-circle-check"></i>
                                Default Address
                            </div>
                        <?php else: ?>
                            <button
                                type="button"
                                class="account-address-set-default"
                                onclick="setDefaultAddress(<?= (int)$address['id']; ?>)"
                            >
                                <i class="fa-solid fa-location-dot"></i>
                                Set Default
                            </button>
                        <?php endif; ?>

                        <button
                            type="button"
                            class="account-address-edit"
                            onclick="editAddress(<?= (int)$address['id']; ?>)"
                        >
                            <i class="fa-solid fa-pen"></i>
                            Edit
                        </button>

                        <button
                            type="button"
                            class="account-address-delete"
                            onclick="deleteAddress(<?= (int)$address['id']; ?>)"
                        >
                            <i class="fa-regular fa-trash-can"></i>
                            Delete
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- No Addresses -->
            <div class="account-no-addresses">

                <i class="fa-solid fa-location-dot"></i>

                <h3>No Saved Addresses</h3>

                <p>
                    You haven't added any addresses yet.
                </p>

                <button
                    type="button"
                    class="add-address-btn"
                    onclick="openAddAddress()"
                >
                    <i class="fa-solid fa-plus"></i>
                    Add New Address
                </button>

            </div>

        <?php endif; ?>

    </div>

</div>
