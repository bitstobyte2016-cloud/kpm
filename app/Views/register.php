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

                <p>Join K-Pop Merch and enjoy a better shopping experience.</p>

            </div>

            <form id="registerForm">

                <div class="register-form">

                    <div class="form-row">
                        <label>Email Address <span>*</span></label>
                        <input type="email" name="email" id="email">
                        <div
                            class="field-error"
                            id="email_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Password <span>*</span></label>
                        <input type="password" name="password" id="password">
                        <div
                            class="field-error"
                            id="password_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Confirm Password <span>*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password">
                        <div
                            class="field-error"
                            id="confirm_password_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>First Name <span>*</span></label>
                        <input type="text" name="fname" id="fname">
                        <div
                            class="field-error"
                            id="first_name_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Last Name <span>*</span></label>
                        <input type="text" name="lname" id="lname">
                        <div
                            class="field-error"
                            id="last_name_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Address Line 1 <span>*</span></label>
                        <input type="text" name="address1" id="address1">
                        <div
                            class="field-error"
                            id="address_1_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Address Line 2</label>
                        <input type="text" name="address2" id="address2">
                    </div>

                    <div class="form-row">
                        <label>State <span>*</span></label>
                        <select
                            id="state" name="state">
                            <option value="">
                                Select State
                            </option>
                        </select>
                        <div
                            class="field-error"
                            id="state_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>City <span>*</span></label>
                        <select
                            id="city" name="city">
                            <option value="">
                                Select City
                            </option>
                        </select>
                        <div
                            class="field-error"
                            id="city_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Country</label>
                        <input
                            type="text"
                            value="India"
                            readonly
                            class="readonly-input">
                        <input
                            type="hidden"
                            name="country"
                            value="India">
                    </div>

                    <div class="form-row">
                        <label>Pincode <span>*</span></label>
                        <input type="text" name="pincode" id="pincode">
                        <div
                            class="field-error"
                            id="pincode_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Phone Number <span>*</span></label>
                        <input type="text" name="phone" id="phone">
                        <div
                            class="field-error"
                            id="phone_error"
                        ></div>
                    </div>

                    <div class="form-row">
                        <label>Company Name</label>
                        <input type="text" name="company_name" id="company_name">
                    </div>

                    <div class="form-row">
                        <label>Company GST Number</label>
                        <input type="text" name="gst_no" id="gst_no">
                    </div>

                </div>

                <div class="register-terms">
                    <label>
                        <input
                            type="checkbox" id="terms">
                        By registering you agree to our
                        <a href="<?= base_url('terms'); ?>">
                            Terms and Conditions
                        </a>
                    </label>
                </div>

                <button
                    type="button"
                    class="auth-btn"
                    id="createAccountBtn">
                    CREATE ACCOUNT
                </button>

                <div class="register-login">
                    Already have an account?
                    <a href="<?= base_url('signin'); ?>">
                        Sign In
                    </a>
                </div>

            </form>

        </div>
        
        <div id="verifyModalContainer"></div>

    </div>

</section>


<script>

    let indiaData=null;

    //load state and cities data on page ready
    $(document).ready(function(){

        $.ajax({url:"<?= base_url('getIndiaData'); ?>", type:"POST", dataType:"json",
            success:function(response){
                indiaData=response;

                let stateHtml= '<option value="">Select State</option>';

                $.each( indiaData.states, function(i,state){
                        stateHtml+= '<option value="'+
                        state.name+ '">'+
                        state.name+ '</option>';
                    }
                );

                $("#state").html(stateHtml);

            },

            error:function(xhr,status,error){
                console.log("FAILED");
                console.log(status);
                console.log(error);
                console.log(xhr.responseText);
            }
        });
    });

    //load cities when state changed
    $("#state").on( "change",function(){

            if(!indiaData)
            {return;}

            let stateName=$(this).val();
            let selectedState=indiaData.states.find(state=>state.name===stateName);
            let cityHtml='<option value="">Select City</option>';

            if(selectedState)
            {
                $.each(selectedState.cities,function(i,city){
                        cityHtml+= '<option value="'+  city.name+ '">'+ city.name+ '</option>';
                    }
                );
            }

            $("#city").html(cityHtml);
        }
    );
    
    //check that all fields are filled
    function validateRegisterForm()
    {
        let valid=true;

        $(".field-error").html("");
        $("input,select").removeClass("input-error");

        let firstName=$("#fname").val().trim();
        let lastName=$("#lname").val().trim();
        let email=$("#email").val().trim();
        let mobile=$("#phone").val().trim();
        let password= $("#password").val();
        let confirmPassword=$("#confirm_password").val();
        let state=$("#state").val();
        let city=$("#city").val();
        let address1=$("#address1").val().trim();
        let pincode=$("#pincode").val().trim();

        if(firstName=="")
        {
            $("#first_name_error").html( "First name is required");
            $("#fname").addClass( "input-error" );

            valid=false;
        }

        if(lastName=="")
        {
            $("#last_name_error").html( "Last name is required" );
            $("#lname").addClass("input-error");

            valid=false;
        }

        if(email=="")
        {
            $("#email_error").html("Email is required");
            $("#email").addClass("input-error" );

            valid=false;
        }
        else
        {
            let emailPattern= /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if(!emailPattern.test(email))
            {
                $("#email_error").html( "Invalid email address" );
                $("#email").addClass("input-error" );

                valid=false;
            }
        }

        if(mobile=="")
        {
            $("#phone_error").html( "Mobile number is required" );
            $("#phone").addClass( "input-error");

            valid=false;
        }
        else if(!/^[0-9]{10}$/.test(mobile))
        {
            $("#phone_error").html("Enter valid 10 digit mobile number" );
            $("#phone").addClass( "input-error"  );

            valid=false;
        }

        if(password=="")
        {
            $("#password_error").html("Password is required" );
            $("#password").addClass( "input-error");

            valid=false;
        }
        else if(password.length<8)
        {
            $("#password_error").html("Password must be at least 8 characters");
            $("#password").addClass( "input-error");

            valid=false;
        }

        if(confirmPassword=="")
        {
            $("#confirm_password_error").html( "Confirm your password");
            $("#confirm_password").addClass("input-error");

            valid=false;
        }
        else if(password!=confirmPassword)
        {
            $("#confirm_password_error").html( "Passwords do not match" );
            $("#confirm_password").addClass( "input-error");

            valid=false;
        }

        if(state=="")
        {
            $("#state_error").html( "Select a state" );
            $("#state").addClass( "input-error");

            valid=false;
        }

        if(city=="")
        {
            $("#city_error").html( "Select a city" );
            $("#city").addClass( "input-error");

            valid=false;
        }

        if(address1=="")
        {
            $("#address_1_error").html( "Address is required" );
            $("#address1").addClass("input-error" );

            valid=false;
        }
        

        if(pincode=="")
        {
            $("#pincode_error").html("Pincode is required");
            $("#pincode").addClass("input-error");

            valid=false;
        }
        else if(!/^[0-9]{6}$/.test(pincode))
        {
            $("#pincode_error").html("Invalid pincode");
            $("#pincode").addClass( "input-error");

            valid=false;
        }
        
        if(!$("#terms").is(":checked"))
        {
            $("#terms_error").html("Please accept Terms & Conditions");

            valid=false;
        }
        return valid;
    }
    
    //on click create
    $("#createAccountBtn").click(function(e){

        e.preventDefault();


        if(!validateRegisterForm())
        {
            return;
        }

        showLoader();

        $.post( "<?= base_url('verify'); ?>",
            {
                email  : $("#email").val()
            }, function(html){
                hideLoader();
                $("#verifyModalContainer") .html(html);
                $("#verifyModal") .fadeIn(200);
            }
        ) .fail(function(){
            hideLoader();
            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to send verification OTP. Please try again."
            );
        });
        
    });
    
    //to register user 
    function registerUser(){

        let firstName = $("#fname").val().trim();
        let lastName = $("#lname").val().trim();
        let email = $("#email").val().trim();
        let phone = $("#phone").val().trim();
        let password = $("#password").val();
        let address1 = $("#address1").val().trim();
        let address2 = $("#address2").val().trim();
        let city = $("#city").val();
        let state = $("#state").val();
        let country = $("#country").val();
        let pincode = $("#pincode").val().trim();
        let companyName = $("#company_name").val().trim();
        let companyGst = $("#gst_no").val().trim();


        $("#siteLoader").fadeIn(200);


        $.post(
            "<?= base_url('registerUser') ?>",
            {
                fname: firstName,
                lname: lastName,
                email: email,
                phone: phone,
                password: password,
                address1: address1,
                address2 : address2,
                city: city,
                state: state,
                country: country,
                pincode: pincode,
                company_name: companyName,
                company_gst: companyGst
            },
            function(response)
            {
                $("#siteLoader").fadeOut(200);

                if(response.status)
                {
                    showAlert(
                        "success","Account Created",
                        response.message
                    );
            
                    //redirect 
                    setTimeout(function()
                    {
                        window.location.href = "<?= base_url() ?>";
                    },1500);
                }
                else
                {
                    showAlert(
                        "error", "Registration Failed",
                        response.message
                    );
                }
            },
            "json"
        )
        .fail(function(xhr)
        {
            $("#siteLoader").fadeOut(200);
            console.log(xhr.responseText);
            showAlert(
                "error",
                "Something Went Wrong",
                "Unable to create your account. Please try again."
            );
        });
    }
    


</script>


<?= $this->endSection(); ?>