<?php
require_once "../config/database.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: change_password.php");
    exit();
}
$email = trim($_POST["email"] ?? "");
$new_password = trim($_POST["new_password"] ?? "");
$confirm_password = trim($_POST["confirm_password"] ?? "");
$errors = [];
if ($email === "") {
    $errors[] = "Email is required.";
}
if ($new_password === "") {
    $errors[] = "New password is required.";
}
if ($confirm_password === "") {
    $errors[] = "Confirm password is required.";
}
if ($new_password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if ($new_password !== "") {
    if (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    $has_upper = preg_match("/[A-Z]/", $new_password);
    $has_lower = preg_match("/[a-z]/", $new_password);
    $has_digit = preg_match("/[0-9]/", $new_password);
    $has_symbol = preg_match("/[^A-Za-z0-9]/", $new_password);

    if (!$has_upper || !$has_lower || !$has_digit || !$has_symbol) {
        $errors[] = "Password must include uppercase, lowercase, number, and symbol.";
    }
}
if (count($errors) > 0) {
    $_SESSION["change_password_errors"] = $errors;
    header("Location: change_password.php");
    exit();
}
try {

    $db = new database();

    $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION["change_password_errors"] = ["Email not found."];
        header("Location: change_password.php");
        exit();
    }

    $stmt->close();

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $db->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        $_SESSION["change_password_success"] = "Password successfully changed.";
        header("Location: change_password.php");
        exit();
    }

    $_SESSION["change_password_errors"] = ["Failed to update password."];
    header("Location: change_password.php");
    exit();

    $stmt->close();

} catch (Exception $e) {
    $_SESSION["change_password_errors"] = ["Database error."];
    header("Location: change_password.php");
    exit();
}
?>