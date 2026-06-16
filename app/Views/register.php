<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<section class="register-page">

    <div class="container">

        <div class="search-breadcrumb">
            <a href="<?= base_url(); ?>">Home</a>
            <span>/</span>
            <span>Create Account</span>
        </div>

        <div class="register-card">

            <div class="register-header">

                <div class="register-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <h1>Create An Account</h1>

                <p>
                    Join K-Pop Merch and enjoy a better shopping experience.
                </p>

            </div>

            <form id="registerForm">

                <div class="register-form">

                    <div class="form-row">
                        <label>Email Address <span>*</span></label>
                        <input type="email" name="email">
                    </div>

                    <div class="form-row">
                        <label>Password <span>*</span></label>
                        <input type="password" name="password">
                    </div>

                    <div class="form-row">
                        <label>Confirm Password <span>*</span></label>
                        <input type="password" name="confirm_password">
                    </div>

                    <div class="form-row">
                        <label>First Name <span>*</span></label>
                        <input type="text" name="fname">
                    </div>

                    <div class="form-row">
                        <label>Last Name <span>*</span></label>
                        <input type="text" name="lname">
                    </div>

                    <div class="form-row">
                        <label>Address Line 1 <span>*</span></label>
                        <input type="text" name="address1">
                    </div>

                    <div class="form-row">
                        <label>Address Line 2</label>
                        <input type="text" name="address2">
                    </div>

                    <div class="form-row">
                        <label>State <span>*</span></label>

                        <select
                            id="state"
                            name="state"
                        >
                            <option value="">
                                Select State
                            </option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label>City <span>*</span></label>

                        <select
                            id="city"
                            name="city"
                        >
                            <option value="">
                                Select City
                            </option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label>Country</label>

                        <input
                            type="text"
                            value="India"
                            readonly
                            class="readonly-input"
                        >

                        <input
                            type="hidden"
                            name="country"
                            value="India"
                        >
                    </div>

                    <div class="form-row">
                        <label>Pincode <span>*</span></label>
                        <input type="text" name="pincode">
                    </div>

                    <div class="form-row">
                        <label>Phone Number <span>*</span></label>
                        <input type="text" name="phone">
                    </div>

                    <div class="form-row">
                        <label>Company Name</label>
                        <input type="text" name="company_name">
                    </div>

                    <div class="form-row">
                        <label>Company GST Number</label>
                        <input type="text" name="gst_no">
                    </div>

                </div>

                <div class="register-terms">

                    <label>

                        <input
                            type="checkbox"
                            id="terms"
                        >

                        By registering you agree to our

                        <a href="<?= base_url('terms'); ?>">

                            Terms and Conditions

                        </a>

                    </label>

                </div>

                <button
                    type="button"
                    class="auth-btn"
                    onclick="window.location='<?= base_url('verify'); ?>'"
                >

                    CREATE ACCOUNT

                </button>

                <div class="register-login">

                    Already have an account?

                    <a href="<?= base_url('login'); ?>">

                        Sign In

                    </a>

                </div>

            </form>

        </div>

    </div>

</section>

<script>

let indiaData=null;

$(document).ready(function(){

    $.getJSON(

        "<?= base_url('assets/countries+states+cities.json'); ?>",

        function(data){

            indiaData=
            data.find(
                c=>c.name==="India"
            );

            let html=
            '<option value="">Select State</option>';

            $.each(

                indiaData.states,

                function(i,state){

                    html+=
                    '<option value="'+
                    state.name+
                    '">'+
                    state.name+
                    '</option>';

                }

            );

            $("#state").html(html);

        }

    );

});


$("#state").change(function(){

    let selectedState=
    $(this).val();

    let state=
    indiaData.states.find(
        s=>s.name===selectedState
    );

    let html=
    '<option value="">Select City</option>';

    if(state){

        $.each(

            state.cities,

            function(i,city){

                html+=
                '<option value="'+
                city.name+
                '">'+
                city.name+
                '</option>';

            }

        );

    }

    $("#city").html(html);

});

</script>

<?= $this->endSection(); ?>