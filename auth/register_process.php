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
$date_of_birth = trim($_POST["date_of_birth"] ?? "");
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

if ($name === "") {
    $errors[] = "Name is required.";
}
if ($email === "") {
    $errors[] = "Email is required.";
}
if ($date_of_birth === "") {
    $errors[] = "Date of Birth is required.";
}
if ($username === "") {
    $errors[] = "Username is required.";
}
if ($password === "") {
    $errors[] = "Password is required.";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (count($errors) === 0) {
    try {
        $db = new database();

        $stmt = $db->conn->prepare("SELECT id FROM tbl_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = ($result && $result->num_rows > 0);
        $stmt->close();

        if ($exists) {
            $errors[] = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->conn->prepare(
                "INSERT INTO tbl_users (name, email, date_of_birth, username, password) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $date_of_birth, $username, $hashed_password);

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
        $errors[] = "Database connection error.";
    }
}

if (count($errors) > 0) {
    $_SESSION["register_errors"] = $errors;
    $_SESSION["register_name"] = $name;
    $_SESSION["register_email"] = $email;
    $_SESSION["register_date_of_birth"] = $date_of_birth;
    $_SESSION["register_username"] = $username;
    header("Location: /utswebpro/auth/register.php");
    exit;
}
?>