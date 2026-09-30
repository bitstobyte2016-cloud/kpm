<div
    id="editProfileModal"
    class="account-modal"
>

    <div class="account-modal-overlay"></div>

    <div class="account-modal-box">

        <div class="account-modal-header">

            <div>
                <i class="fa-solid fa-user-pen"></i>

                <h3>
                    Edit Profile
                </h3>
            </div>

            <button
                type="button"
                class="account-modal-close"
                onclick="closeEditProfile()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <div class="account-modal-body">

            <p class="account-modal-description">
                Update your first and last name below.
            </p>


            <div class="account-edit-profile-form">

                <div class="account-edit-profile-group">

                    <label>
                        First Name
                    </label>

                    <input
                        type="text"
                        id="editFirstName"
                        value="<?= esc($first_name); ?>"
                        placeholder="First Name"
                    >

                </div>


                <div class="account-edit-profile-group">

                    <label>
                        Last Name
                    </label>

                    <input
                        type="text"
                        id="editLastName"
                        value="<?= esc($last_name); ?>"
                        placeholder="Last Name"
                    >

                </div>

            </div>

        </div>


        <div class="account-modal-footer">

            <button
                type="button"
                class="account-modal-cancel"
                onclick="closeEditProfile()"
            >
                CANCEL
            </button>

            <button
                type="button"
                class="account-modal-save"
                onclick="saveProfile()"
            >
                <i class="fa-solid fa-check"></i>
                SAVE
            </button>

        </div>

    </div>

</div>


<script>

function closeEditProfile()
{
    $("#editProfileModal").fadeOut(200, function(){

        $("#editProfileModalContainer").empty();

    });
}


function saveProfile()
{
    let firstName = $("#editFirstName").val().trim();
    let lastName = $("#editLastName").val().trim();

    if(firstName === ""){

        showAlert(
            "error",
            "Missing First Name",
            "Please enter your first name."
        );

        return;
    }

    if(lastName === ""){

        showAlert(
            "error",
            "Missing Last Name",
            "Please enter your last name."
        );

        return;
    }


    $("#siteLoader").fadeIn(200);

    $.post(
        "<?= base_url('updateProfile'); ?>",
        {
            first_name: firstName,
            last_name: lastName
        },
        function(response){

            $("#siteLoader").fadeOut(200);

            if(response.status){

                showAlert(
                    "success",
                    "Profile Updated",
                    response.message
                );

                closeEditProfile();

                setTimeout(function(){
                    location.reload();
                }, 1000);

            } else {

                showAlert(
                    "error",
                    "Update Failed",
                    response.message
                );

            }

        },
        "json"

    ).fail(function(xhr){

        $("#siteLoader").fadeOut(200);

        console.log(xhr.responseText);

        showAlert(
            "error",
            "Something Went Wrong",
            "Unable to update your profile. Please try again."
        );

    });
}

</script>