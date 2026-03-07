<?php
session_start();

$errors = $_SESSION["register_errors"] ?? [];
$success = $_SESSION["register_success"] ?? "";
$name = $_SESSION["register_name"] ?? "";
$email = $_SESSION["register_email"] ?? "";
$date_of_birth = $_SESSION["register_date_of_birth"] ?? "";
$username = $_SESSION["register_username"] ?? "";

unset(
    $_SESSION["register_errors"],
    $_SESSION["register_success"],
    $_SESSION["register_name"],
    $_SESSION["register_email"],
    $_SESSION["register_date_of_birth"],
    $_SESSION["register_username"]
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <link rel="stylesheet" href="" />
</head>

<body>
    <div class="login-card">
        <div>
            <img src="../assets/logo.png" alt="Logo">
        </div>
        <h2> Register System</h2>

        <?php if (count($errors) > 0): ?>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?=  htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success !== ""): ?>
            <div>
                <p><?=  htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>

        <form action="register_process.php" method="post">
            <input type="text" name="name" placeholder="Name" value="<?=  htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?=  htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            <input type="date" name="date_of_birth" placeholder="Date of Birth" value="<?=  htmlspecialchars($date_of_birth, ENT_QUOTES, 'UTF-8'); ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?=  htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class = "password-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" class="toggle password" data-target="password" aria-label="show password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <div class = "password-field">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="button" class="toggle password" data-target="confirm_password" aria-label="show password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <button type="submit" name="register">Sign Up</button>
            <button type="button" id="login" name="login" onclick="window.location.href = 'login.php'">Login</button>
        </form>
    </div>
    <script>
        document.querySelectorAll(".toggle.password").forEach(function (button) {
            button.addEventListener("click", function() {
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
</body>
</html>