$(document).ready(function () {

    $("#registerForm").on("submit", function (e) {
        e.preventDefault();

        const username = $("#username").val().trim();
        const email = $("#email").val().trim();
        const password = $("#password").val();
        const confirmPassword = $("#confirmPassword").val();

        $("#registerMessage").html("");

        // Basic validation
        if (username.length < 3) {
            $("#registerMessage").html(
                '<div class="alert alert-danger">Username must contain at least 3 characters.</div>'
            );
            return;
        }

        if (password.length < 8) {
            $("#registerMessage").html(
                '<div class="alert alert-danger">Password must contain at least 8 characters.</div>'
            );
            return;
        }

        if (password !== confirmPassword) {
            $("#registerMessage").html(
                '<div class="alert alert-danger">Passwords do not match.</div>'
            );
            return;
        }

        const button = $("#registerBtn");

        button.prop("disabled", true);
        button.text("Creating account...");

        $.ajax({
            url: "php/register.php",
            method: "POST",
            data: {
                username: username,
                email: email,
                password: password
            },

            dataType: "json",

            success: function (response) {

                if (response.success) {

                    $("#registerMessage").html(
                        '<div class="alert alert-success">' +
                        response.message +
                        '</div>'
                    );

                    $("#registerForm")[0].reset();

                    setTimeout(function () {
                        window.location.href = "login.html";
                    }, 1500);

                } else {

                    $("#registerMessage").html(
                        '<div class="alert alert-danger">' +
                        response.message +
                        '</div>'
                    );
                }
            },

            error: function () {

                $("#registerMessage").html(
                    '<div class="alert alert-danger">' +
                    'Something went wrong. Please try again.' +
                    '</div>'
                );
            },

            complete: function () {
                button.prop("disabled", false);
                button.text("Create Account");
            }
        });
    });

});