<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";

auth::login_check();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /utswebpro/pages/report_lost.php");
    exit();
}

$email = $_SESSION["email"] ?? "";
if ($email === "") {
    header("Location: /utswebpro/auth/login.php");
    exit();
}

$item_name = trim($_POST["item_name"] ?? "");
$category = trim($_POST["category"] ?? "");
$color = trim($_POST["color"] ?? "");
$brand = trim($_POST["brand"] ?? "");
$item_description = trim($_POST["item_description"] ?? "");
$location_lost = trim($_POST["location_lost"] ?? "");
$date_lost = trim($_POST["date_lost"] ?? "");
$report_description = trim($_POST["report_description"] ?? "");

if ($item_name === "" || $location_lost === "" || $date_lost === "") {
    header("Location: /utswebpro/pages/report_lost.php");
    exit();
}

$image_path = null;
if (!empty($_FILES["image"]["name"])) {
    $upload_dir = __DIR__ . "/../uploads/lost";
    $tmp_name = $_FILES["image"]["tmp_name"] ?? "";
    $original_name = $_FILES["image"]["name"] ?? "";
    $error = $_FILES["image"]["error"] ?? UPLOAD_ERR_NO_FILE;

    if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmp_name)) {
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
        if (in_array($ext, $allowed, true)) {
            $image_info = getimagesize($tmp_name);
            if ($image_info !== false) {
                $safe_name = bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
                $destination = $upload_dir . "/" . $safe_name;
                if (move_uploaded_file($tmp_name, $destination)) {
                    $image_path = "uploads/lost/" . $safe_name;
                }
            }
        }
    }
}

try {
    $db = new database();
    $db->conn->begin_transaction();

    $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) {
        $db->conn->rollback();
        header("Location: /utswebpro/auth/login.php");
        exit();
    }

    $stmt = $db->conn->prepare(
        "INSERT INTO lost_items (item_name, description, category, color, brand, image) VALUES (?, ?, ?, ?, ?, ?)"
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
        throw new Exception("Failed to save lost item.");
    }

    $lost_item_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $db->conn->prepare(
        "INSERT INTO lost_reports (user_id, lost_item_id, location_lost, date_lost, description) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iisss",
        $user["id"],
        $lost_item_id,
        $location_lost,
        $date_lost,
        $report_description
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to save lost report.");
    }

    $stmt->close();
    $db->conn->commit();
    header("Location: /utswebpro/pages/home.php");
    exit();
} catch (Exception $e) {
    if (isset($db) && $db->conn) {
        $db->conn->rollback();
    }
    header("Location: /utswebpro/pages/report_lost.php");
    exit();
}
