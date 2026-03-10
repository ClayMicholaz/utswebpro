<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found</title>
    <link rel="stylesheet" href="../assets/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="login-card">
        <h2> Login System </h2>
        <form action="login_process.php" method="post">
            <input type="text" name="email" placeholder="Email" required>
            <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" class="toggle password" data-target="password" aria-label="show password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <button type="submit" name="login">Sign In</button>
            <p> <a href="change_password.php">Forgot Password?</a></p>
            <button type="button" id="register" name="register"
                onclick="window.location.href = 'register.php'">Register</button>
        </form>
    </div>
    <script>
        document.querySelectorAll(".toggle.password").forEach(function (button) {
            button.addEventListener("click", function () {
                let targetid = button.getAttribute("data-target");
                let input = document.getElementById(targetid);
                let icon = button.querySelector("i");
                if (!input || !icon) {
                    return;
                }
                let ispassword = input.type === "password";
                input.type = ispassword ? "text" : "password";
                icon.classList.toggle("fa-eye", !ispassword);
                icon.classList.toggle("fa-eye-slash", ispassword);
                button.setAttribute("aria-label", ispassword ? "hide password" : "show password");
            });
        });
    </script>
    <script type="module" src="assets/login.js"></script>
</body>

</html>