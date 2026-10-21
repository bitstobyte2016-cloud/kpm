<?php

    /*
     * This view is used for BOTH:
     *
     * 1. Adding a new address
     * 2. Editing an existing address
     *
     * When editing, the controller should pass:
     * $address
     *
     * When adding, $address will be empty/not set.
     */

    $isEdit = !empty($address);

    $addressId = $isEdit
        ? ($address['id'] ?? '')
        : '';

    $modalTitle = $isEdit
        ? 'Edit Address'
        : 'Add New Address';

    $modalDescription = $isEdit
        ? 'Update your address details below.'
        : 'Add a shipping address to your account.';

    $saveButtonText = $isEdit
        ? 'Update Address'
        : 'Save Address';

    $saveButtonFunction = $isEdit
        ? 'updateAccountAddress(' . (int)$addressId . ')'
        : 'saveAccountAddress('.$isCheckout.')';

?>


<div
    class="address-modal-overlay"
    id="addAddressModal"
    style="display:none;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="addressModalTitle"
    aria-describedby="addressModalDescription"
>

    <div class="address-modal">

        <!-- HEADER -->
        <div class="address-modal-header">

            <div>

                <h2 id="addressModalTitle">
                    <?= esc($modalTitle); ?>
                </h2>

                <p id="addressModalDescription">
                    <?= esc($modalDescription); ?>
                </p>

            </div>


            <button
                type="button"
                class="address-modal-close"
                onclick="closeAddAddress()"
                aria-label="Close address form"
            >
                <i
                    class="fa-solid fa-xmark"
                    aria-hidden="true"
                ></i>
            </button>

        </div>


        <!-- FORM -->
        <div class="address-modal-body">

            <form
                id="addAddressForm"
                onsubmit="return false;"
            >

                <!-- ADDRESS TITLE -->
                <div class="address-form-group">

                    <label for="addressTitle">
                        Address Title <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="addressTitle"
                        name="address_title"
                        placeholder="e.g. Home, Office, Parents' Home"
                        value="<?= esc($address['address_title'] ?? ''); ?>"
                        required
                        autocomplete="address-line"
                    >

                </div>


                <!-- FIRST + LAST NAME -->
                <div class="address-form-row">

                    <div class="address-form-group">

                        <label for="addressFirstName">
                            First Name <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="addressFirstName"
                            name="first_name"
                            placeholder="Enter first name"
                            value="<?= esc($address['first_name'] ?? ''); ?>"
                            required
                            autocomplete="given-name"
                        >

                    </div>


                    <div class="address-form-group">

                        <label for="addressLastName">
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="addressLastName"
                            name="last_name"
                            placeholder="Enter last name"
                            value="<?= esc($address['last_name'] ?? ''); ?>"
                            autocomplete="family-name"
                        >

                    </div>

                </div>


                <!-- PHONE -->
                <div class="address-form-group">

                    <label for="addressContactNo">
                        Phone Number <span>*</span>
                    </label>

                    <div class="address-phone-wrap">

                        <div
                            class="address-phone-code"
                            aria-label="Country code India"
                        >
                            +91
                        </div>

                        <input
                            type="tel"
                            id="addressContactNo"
                            name="contact_no"
                            maxlength="10"
                            inputmode="numeric"
                            placeholder="Enter 10 digit phone number"
                            value="<?= esc($address['contact_no'] ?? ''); ?>"
                            required
                            autocomplete="tel-national"
                        >

                    </div>

                </div>


                <!-- COMPANY NAME -->
                <div class="address-form-group">

                    <label for="addressCompanyName">
                        Company Name
                        <span class="optional">(Optional)</span>
                    </label>

                    <input
                        type="text"
                        id="addressCompanyName"
                        name="company_name"
                        placeholder="Enter company name"
                        value="<?= esc($address['company_name'] ?? ''); ?>"
                        autocomplete="organization"
                    >

                </div>


                <!-- GSTIN -->
                <div class="address-form-group">

                    <label for="addressCompanyGstin">
                        Company GSTIN
                        <span class="optional">(Optional)</span>
                    </label>

                    <input
                        type="text"
                        id="addressCompanyGstin"
                        name="company_gstin"
                        placeholder="Enter GSTIN"
                        maxlength="15"
                        value="<?= esc($address['company_gstin'] ?? ''); ?>"
                    >

                </div>


                <!-- ADDRESS LINE 1 -->
                <div class="address-form-group">

                    <label for="addressLine1">
                        Address Line 1 <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="addressLine1"
                        name="address_line1"
                        placeholder="House number, building name, street name"
                        value="<?= esc($address['address_line1'] ?? ''); ?>"
                        required
                        autocomplete="address-line1"
                    >

                </div>


                <!-- ADDRESS LINE 2 -->
                <div class="address-form-group">

                    <label for="addressLine2">
                        Address Line 2
                        <span class="optional">(Optional)</span>
                    </label>

                    <input
                        type="text"
                        id="addressLine2"
                        name="address_line2"
                        placeholder="Apartment, suite, unit, floor, landmark, etc."
                        value="<?= esc($address['address_line2'] ?? ''); ?>"
                        autocomplete="address-line2"
                    >

                </div>


                <!-- COUNTRY -->
                <div class="address-form-group">

                    <label>
                        Country
                    </label>

                    <div
                        class="address-fixed-field"
                        aria-label="Country India"
                    >

                        <span>India</span>

                        <i
                            class="fa-solid fa-lock"
                            aria-hidden="true"
                        ></i>

                    </div>

                </div>


                <!-- STATE + CITY -->
                <div class="address-form-row">

                    <div class="address-form-group">

                        <label for="addressState">
                            State <span>*</span>
                        </label>

                        <select
                            id="addressState"
                            name="state"
                            required
                            autocomplete="address-level1"
                            aria-label="State"
                        >

                            <option value="">
                                Select state
                            </option>

                        </select>

                    </div>


                    <div class="address-form-group">

                        <label for="addressCity">
                            City <span>*</span>
                        </label>

                        <select
                            id="addressCity"
                            name="city"
                            disabled
                            required
                            autocomplete="address-level2"
                            aria-label="City"
                        >

                            <option value="">
                                Select city
                            </option>

                        </select>

                    </div>

                </div>


                <!-- PINCODE -->
                <div class="address-form-group">

                    <label for="addressZip">
                        Pincode <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="addressZip"
                        name="zip"
                        maxlength="6"
                        inputmode="numeric"
                        placeholder="Enter pincode"
                        value="<?= esc($address['zip'] ?? ''); ?>"
                        required
                        autocomplete="postal-code"
                    >

                </div>


                <!-- DEFAULT ADDRESS -->
                <div class="address-default-wrap">

                    <label
                        class="address-checkbox"
                        for="addressIsDefault"
                    >

                        <input
                            type="checkbox"
                            id="addressIsDefault"
                            name="is_default"
                            value="Y"
                            <?= (($address['is_default'] ?? 'N') === 'Y') ? 'checked' : ''; ?>
                        >

                        <span
                            class="address-checkbox-box"
                            aria-hidden="true"
                        ></span>

                        <span class="address-checkbox-text">

                            <strong>
                                Set as default address
                            </strong>

                            <small>
                                This address will be used as your default shipping address.
                            </small>

                        </span>

                    </label>

                </div>

            </form>

        </div>


        <!-- FOOTER -->
        <div class="address-modal-footer">

            <button
                type="button"
                class="address-cancel-btn"
                onclick="closeAddAddress()"
            >
                Cancel
            </button>


            <button
                type="button"
                class="address-save-btn"
                onclick="<?= $saveButtonFunction; ?>"
            >

                <i
                    class="fa-solid fa-check"
                    aria-hidden="true"
                ></i>

                <?= esc($saveButtonText); ?>

            </button>

        </div>

    </div>

</div>


<script>

    let addAddressLocationData = null;


    // LOAD INDIAN STATES
    function loadAddressStates()
    {
        $.getJSON(
            "<?= base_url('getIndiaData'); ?>",
            function(data)
            {
                addAddressLocationData = data;

                let stateSelect = $("#addressState");

                stateSelect.html(
                    '<option value="">Select state</option>'
                );

                if(
                    !addAddressLocationData ||
                    !addAddressLocationData.states
                ){
                    return;
                }

                addAddressLocationData.states.forEach(function(state)
                {
                    stateSelect.append(
                        $("<option>", {
                            value: state.name,
                            text: state.name
                        })
                    );
                });


                <?php if($isEdit && !empty($address['state'])): ?>

                    let existingState = <?= json_encode($address['state']); ?>;
                    stateSelect.val(existingState);
                    loadAddressCities(
                        existingState,
                        <?= json_encode($address['city'] ?? ''); ?>
                    );

                <?php endif; ?>

            }
        )
        .fail(function(xhr)
        {
            console.log(xhr.responseText);

            showAlert(
                "error",
                "Unable to Load Locations",
                "We could not load the state and city list. Please try again."
            );
        });
    }


    // ---------------------------------------------------------
    // LOAD CITIES FOR SELECTED STATE
    // ---------------------------------------------------------

    function loadAddressCities(
        selectedState,
        selectedCity = ''
    )
    {
        let citySelect = $("#addressCity");

        citySelect.html(
            '<option value="">Select city</option>'
        );

        citySelect.prop(
            "disabled",
            true
        );


        if(
            !selectedState ||
            !addAddressLocationData ||
            !addAddressLocationData.states
        ){
            return;
        }


        let state = addAddressLocationData.states.find(
            function(item)
            {
                return item.name === selectedState;
            }
        );


        if(
            !state ||
            !state.cities
        ){
            return;
        }


        state.cities.forEach(function(city)
        {
            citySelect.append(
                $("<option>", {
                    value: city.name,
                    text: city.name
                })
            );
        });


        citySelect.prop(
            "disabled",
            false
        );


        if(selectedCity)
        {
            citySelect.val(selectedCity);
        }
    }


    // ---------------------------------------------------------
    // STATE CHANGE
    // ---------------------------------------------------------

    $(document).on(
        "change",
        "#addressState",
        function()
        {
            let selectedState = $(this).val();

            loadAddressCities(
                selectedState
            );
        }
    );


    // ---------------------------------------------------------
    // PHONE - NUMBERS ONLY
    // ---------------------------------------------------------

    $(document).on(
        "input",
        "#addressContactNo",
        function()
        {
            this.value = this.value
                .replace(/\D/g, "")
                .substring(0, 10);
        }
    );

    // PINCODE - NUMBERS ONLY
    $(document).on(
        "input",
        "#addressZip",
        function()
        {
            this.value = this.value
                .replace(/\D/g, "")
                .substring(0, 6);
        }
    );

    // CLOSE ADDRESS POPUP
    function closeAddAddress()
    {
        $("#addAddressModal").fadeOut(
            200,
            function()
            {
                $(this).remove();
            }
        );
    }

    // LOAD STATES WHEN POPUP OPENS
    loadAddressStates();

</script>