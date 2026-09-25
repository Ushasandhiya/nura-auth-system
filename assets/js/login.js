$(document).ready(function () {

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        const email = $("#email").val().trim();
        const password = $("#password").val();

        $("#loginMessage").html("");

        if (email === "" || password === "") {
            $("#loginMessage").html(
                '<div class="alert alert-danger">Please enter your email and password.</div>'
            );
            return;
        }

        const button = $("#loginBtn");

        button.prop("disabled", true);
        button.text("Logging in...");

        $.ajax({
            url: "php/login.php",
            method: "POST",
            data: {
                email: email,
                password: password
            },
            dataType: "json",

            success: function (response) {

                if (response.success) {

    // Save the Redis session token


    $("#loginMessage").html(
        '<div class="alert alert-success">' +
        response.message +
        '</div>'
    );

    setTimeout(function () {
        window.location.href = "profile.html";
    }, 1000);
                } else {

                    $("#loginMessage").html(
                        '<div class="alert alert-danger">' +
                        response.message +
                        '</div>'
                    );
                }
            },

            error: function () {

                $("#loginMessage").html(
                    '<div class="alert alert-danger">' +
                    'Unable to connect to the server.' +
                    '</div>'
                );
            },

            complete: function () {
                button.prop("disabled", false);
                button.text("Login");
            }
        });
    });

});