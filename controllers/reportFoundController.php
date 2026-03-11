<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../includes/functions.php";

auth::login_check();
app_require_post("/utswebpro/pages/report_found.php");

$email = $_SESSION["email"] ?? "";
if ($email === "") {
    app_redirect("/utswebpro/auth/login.php");
}

$item_name = app_post_trim("item_name");
$category = app_post_trim("category");
$color = app_post_trim("color");
$brand = app_post_trim("brand");
$item_description = app_post_trim("item_description");
$location_found = app_post_trim("location_found");
$date_found = app_post_trim("date_found");
$report_description = app_post_trim("report_description");

if ($item_name === "" || $location_found === "" || $date_found === "") {
    app_redirect("/utswebpro/pages/report_found.php");
}

$image_path = app_handle_image_upload(
    "image",
    __DIR__ . "/../uploads/found",
    "uploads/found"
);

try {
    $db = new database();
    $db->conn->begin_transaction();

    $userId = app_find_user_id_by_email($db->conn, $email);
    if ($userId === null) {
        $db->conn->rollback();
        app_redirect("/utswebpro/auth/login.php");
    }

    $stmt = $db->conn->prepare(
        "INSERT INTO found_items (item_name, description, category, color, brand, image) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssss",
        $item_name,
        $item_description,
        $category,
        $color,
        $brand,
        $image_path
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to save found item.");
    }

    $found_item_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $db->conn->prepare(
        "INSERT INTO found_reports (user_id, found_item_id, location_found, date_found, description) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iisss",
        $userId,
        $found_item_id,
        $location_found,
        $date_found,
        $report_description
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to save found report.");
    }

    $stmt->close();
    $db->conn->commit();
    app_redirect("/utswebpro/pages/home.php");
} catch (Exception $e) {
    if (isset($db) && $db->conn) {
        $db->conn->rollback();
    }
    app_redirect("/utswebpro/pages/report_found.php");
}
