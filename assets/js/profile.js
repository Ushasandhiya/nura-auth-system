$(document).ready(function () {

    // Get profile information
    $.ajax({
        url: "php/profile.php",
        method: "GET",
        dataType: "json",

        success: function (response) {

            if (response.success) {

                $("#profileUsername").text(response.user.username);
                $("#username").text(response.user.username);
                $("#email").text(response.user.email);

                $("#fullName").text(
                    response.profile.full_name || "Not provided"
                );

                $("#phone").text(
                    response.profile.phone || "Not provided"
                );

                $("#location").text(
                    response.profile.location || "Not provided"
                );

                $("#bio").text(
                    response.profile.bio || "Not provided"
                );

            } else {
                window.location.href = "login.html";
            }
        },

        error: function () {

            $("#profileUsername").text("Unable to load");
            $("#username").text("Unable to load");
            $("#email").text("Unable to load");
        }
    });


    // Logout
    $("#logoutBtn").on("click", function () {

        const button = $(this);

        button.prop("disabled", true);
        button.text("Logging out...");

        $.ajax({
            url: "php/logout.php",
            method: "POST",
            dataType: "json",

            success: function (response) {

                if (response.success) {
                    window.location.href = "login.html";
                } else {
                    button.prop("disabled", false);
                    button.text("Logout");
                    alert(response.message);
                }
            },

            error: function () {

                button.prop("disabled", false);
                button.text("Logout");

                alert("Unable to logout. Please try again.");
            }
        });

    });


    // Edit Profile
    $("#editProfileBtn").on("click", function () {

        $("#editFullName").val($("#fullName").text());
        $("#editPhone").val($("#phone").text());
        $("#editLocation").val($("#location").text());
        $("#editBio").val($("#bio").text());

        $("#profileDisplay").addClass("d-none");
        $("#editProfileForm").removeClass("d-none");
        $("#editProfileBtn").addClass("d-none");

    });


    // Cancel editing
    $("#cancelEditBtn").on("click", function () {

        $("#editProfileForm").addClass("d-none");
        $("#profileDisplay").removeClass("d-none");
        $("#editProfileBtn").removeClass("d-none");

        $("#profileMessage").html("");

    });


    // Save profile
    $("#editProfileForm").on("submit", function (e) {

        e.preventDefault();

        $.ajax({
            url: "php/update_profile.php",
            method: "POST",

            data: {
                full_name: $("#editFullName").val().trim(),
                phone: $("#editPhone").val().trim(),
                location: $("#editLocation").val().trim(),
                bio: $("#editBio").val().trim()
            },

            dataType: "json",

            success: function (response) {

                if (response.success) {

                    $("#profileMessage").html(
                        '<div class="alert alert-success">' +
                        response.message +
                        '</div>'
                    );

                    $("#fullName").text(
                        $("#editFullName").val().trim() || "Not provided"
                    );

                    $("#phone").text(
                        $("#editPhone").val().trim() || "Not provided"
                    );

                    $("#location").text(
                        $("#editLocation").val().trim() || "Not provided"
                    );

                    $("#bio").text(
                        $("#editBio").val().trim() || "Not provided"
                    );

                    setTimeout(function () {

                        $("#editProfileForm").addClass("d-none");
                        $("#profileDisplay").removeClass("d-none");
                        $("#editProfileBtn").removeClass("d-none");
                        $("#profileMessage").html("");

                    }, 1000);

                } else {

                    $("#profileMessage").html(
                        '<div class="alert alert-danger">' +
                        response.message +
                        '</div>'
                    );
                }
            },

            error: function () {

                $("#profileMessage").html(
                    '<div class="alert alert-danger">' +
                    'Unable to update profile.' +
                    '</div>'
                );
            }
        });

    });

});