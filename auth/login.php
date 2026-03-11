<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$login_error = $_SESSION["login_error"] ?? null;
unset($_SESSION["login_error"]);
?>
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
        <h2>Login System</h2>
        <?php if ($login_error): ?>
            <p style="color: #b00020; margin-bottom: 12px;">
                <?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>
        <form action="login_process.php" method="post">
            <input type="text" name="email" placeholder="Email" required>
            <div class="password-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" class="toggle password" data-target="password" aria-label="show password"><i
                        class="fa-solid fa-eye"></i></button>
            </div>
            <button type="submit" name="login">Sign In</button>
            <p class="auth-switch">Don't have an account? <a href="register.php">Sign up</a></p>
            <p><a href="change_password.php">Forgot Password?</a></p>
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