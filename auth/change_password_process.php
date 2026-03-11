<?php
require_once "../config/database.php";
require_once "../includes/functions.php";

app_start_session();
app_require_post("change_password.php");

$email = app_post_trim("email");
$new_password = app_post_trim("new_password");
$confirm_password = app_post_trim("confirm_password");
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
    $errors = array_merge($errors, app_password_strength_errors($new_password));
}
if (count($errors) > 0) {
    $_SESSION["change_password_errors"] = $errors;
    app_redirect("change_password.php");
}
try {

    $db = new database();

    $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION["change_password_errors"] = ["Email not found."];
        app_redirect("change_password.php");
    }

    $stmt->close();

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $db->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        $_SESSION["change_password_success"] = "Password successfully changed.";
        app_redirect("change_password.php");
    }

    $_SESSION["change_password_errors"] = ["Failed to update password."];
    app_redirect("change_password.php");

    $stmt->close();

} catch (Exception $e) {
    $_SESSION["change_password_errors"] = ["Database error."];
    app_redirect("change_password.php");
}
?>