<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class= "login-card">
        <div>
            <img src="../assets/logo.png" alt="Logo">
        </div>
        <h2> Login System </h2>
        <form action="login_process.php" method = "post">
            <input type = "text" name = "username" placeholder = "username" required>
            <div class = "password-field">
                <input type = "password" name = "password" placeholder = "password" required>
                <button type = "button" class = "toggle password" data-target = "password" aria-label = "show password">
                    <i class = "fa-solid fa-eye"></i>
                </button>
            </div>
            <button type = "login" name = "login">sign in</button>
            <p> <a href="change_password.php">Forget Password?</a></p>
            <button type = "button" id = "register" name = "register" onclick = "window.location.href = 'register.php'">Register</buttoon>"
        </form>
    </div>
    <script>
        document.querySelectorAll(".toggle.password").forEach(function (button) {
            button.addEventListener("click", function() {
                let targetid = button.getattribute("data-target");
                let input = document.getelementarybyid(targetid);
                let icon = button.querySelector("i");
                if (!input || !icon) {
                    return;
                }
                let ispassword = input.type === "password";
                input.type = ispassword ? "text" : "password";
                icon.classlist.toggle("fa-eye", !ispassword);
                icon.classlist.toggle("fa-eye-slash", ispassword);
                button.setattribute("aria-label", ispassword ? "hide password" : "show password");
            });
        });
    </script>
</body>
</html>