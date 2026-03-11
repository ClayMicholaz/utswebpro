<?php
require_once "../core/auth.php";
require_once "../includes/functions.php";

app_require_post("login.php");

$auth = new auth();
if (!isset($_POST["login"])) {
    app_redirect("login.php");
}

$email = app_post_trim("email");
$password = app_post_trim("password");

if (!$auth->Login($email, $password)) {
    $_SESSION["login_error"] = "Incorrect email or password.";
    app_redirect("login.php");
}

if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
    app_redirect("/utswebpro/pages/admin_dashboard.php");
}

app_redirect("/utswebpro/pages/home.php");