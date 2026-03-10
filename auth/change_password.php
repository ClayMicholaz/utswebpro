<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>

<link rel="stylesheet" href="../assets/css/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

</head>

<body>

<div class="login-card">

<h2>Change Password</h2>

<form action="change_password_process.php" method="post">

<input type="email" name="email" placeholder="Email" required>

<div class="password-field">
<input type="password" id="new_password" name="new_password" placeholder="New Password" required>

<button type="button" class="toggle password" data-target="new_password">
<i class="fa-solid fa-eye"></i>
</button>
</div>

<div class="password-field">
<input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

<button type="button" class="toggle password" data-target="confirm_password">
<i class="fa-solid fa-eye"></i>
</button>
</div>

<button type="submit" name="change">Change Password</button>

</form>

</div>

<script>

document.querySelectorAll(".toggle.password").forEach(function (button) {

button.addEventListener("click", function(){

let targetid = button.getAttribute("data-target");
let input = document.getElementById(targetid);
let icon = button.querySelector("i");

let ispassword = input.type === "password";

input.type = ispassword ? "text" : "password";

icon.classList.toggle("fa-eye");
icon.classList.toggle("fa-eye-slash");

});

});

</script>

</body>
</html>