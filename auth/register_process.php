<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /utswebpro/auth/register.php");
    exit();
}

$errors = [];
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$confirm_password = $_POST["confirm_password"] ?? "";

if ($name === "") {
    $errors[] = "Name is required.";
}
if ($email === "") {
    $errors[] = "Email is required.";
}
if ($password === "") {
    $errors[] = "Password is required.";
}
if ($phone=== "") {
    $errors[] = "Phone number is required.";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (count($errors) === 0) {
    try {
        $db = new database();

        $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = ($result && $result->num_rows > 0);
        $stmt->close();

        if ($exists) {
            $errors[] = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->conn->prepare(
                "INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);

            if ($stmt->execute()) {
                $_SESSION["register_success"] = "Registration successful. Please login.";
                header("Location: /utswebpro/auth/login.php");
                exit;
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    } catch (Exception $e) {
    $errors[] = $e->getMessage();
}
}

if (count($errors) > 0) {
    $_SESSION["register_errors"] = $errors;
    $_SESSION["register_name"] = $name;
    $_SESSION["register_email"] = $email;
    $_SESSION["register_phone"] = $phone;
    header("Location: /utswebpro/auth/register.php");
    exit;
}
?>