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

if (count($errors) > 0) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    echo "<br><a href='change_password.php'>Back</a>";
    exit();
}

try {

    $db = new database();

    $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Email not found.<br>";
        echo "<a href='change_password.php'>Back</a>";
        exit();
    }

    $stmt->close();

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $db->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        echo "Password successfully changed.<br>";
        echo "<a href='login.php'>Login Now</a>";
    } else {
        echo "Failed to update password.";
    }

    $stmt->close();

} catch (Exception $e) {
    echo "Database error.";
}
?>