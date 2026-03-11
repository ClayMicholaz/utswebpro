<?php
require_once "../core/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$auth = new auth();
if (isset($_POST["login"])) {

    $names = $_POST["email"];
    $password = $_POST["password"];

    if ($auth->Login($names, $password)) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
            header("Location: /utswebpro/pages/admin_dashboard.php");
            exit();
        }

        header("Location: /utswebpro/pages/home.php");
        exit();
    } else {
        $_SESSION["login_error"] = "Incorrect email or password.";
        header("Location: login.php");
        exit();
    }
}

header("Location: login.php");
exit();