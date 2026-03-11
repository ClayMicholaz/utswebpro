<?php

function app_start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function app_redirect(string $path): void
{
    header("Location: " . $path);
    exit();
}

function app_require_post(string $fallbackPath): void
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        app_redirect($fallbackPath);
    }
}

function app_post_trim(string $key): string
{
    return trim((string) ($_POST[$key] ?? ""));
}

function app_password_strength_errors(string $password): array
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    $hasUpper = preg_match("/[A-Z]/", $password);
    $hasLower = preg_match("/[a-z]/", $password);
    $hasDigit = preg_match("/[0-9]/", $password);
    $hasSymbol = preg_match("/[^A-Za-z0-9]/", $password);

    if (!$hasUpper || !$hasLower || !$hasDigit || !$hasSymbol) {
        $errors[] = "Password must include uppercase, lowercase, number, and symbol.";
    }

    return $errors;
}

function app_handle_image_upload(string $field, string $uploadDirAbs, string $uploadDirRel): ?string
{
    if (empty($_FILES[$field]["name"])) {
        return null;
    }

    $tmpName = $_FILES[$field]["tmp_name"] ?? "";
    $originalName = $_FILES[$field]["name"] ?? "";
    $error = $_FILES[$field]["error"] ?? UPLOAD_ERR_NO_FILE;

    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
        return null;
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if (getimagesize($tmpName) === false) {
        return null;
    }

    if (!is_dir($uploadDirAbs)) {
        mkdir($uploadDirAbs, 0777, true);
    }

    $safeName = bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
    $destination = rtrim($uploadDirAbs, "/\\") . DIRECTORY_SEPARATOR . $safeName;

    if (!move_uploaded_file($tmpName, $destination)) {
        return null;
    }

    return rtrim($uploadDirRel, "/") . "/" . $safeName;
}

function app_find_user_id_by_email(mysqli $conn, string $email): ?int
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ? (int) $row["id"] : null;
}
