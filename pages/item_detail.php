<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$type = $_GET["type"] ?? "lost";
$type = in_array($type, ["lost", "found"], true) ? $type : "lost";

$item = null;
$error_message = "";

if ($id <= 0) {
    $error_message = "Item not found.";
} else {
    try {
        $db = new database();
        if ($type === "lost") {
            $stmt = $db->conn->prepare(
                "SELECT li.item_name, li.description, li.category, li.color, li.brand, li.image, " .
                "lr.location_lost AS location, lr.date_lost AS date_value, lr.status " .
                "FROM lost_items li " .
                "INNER JOIN lost_reports lr ON lr.lost_item_id = li.id " .
                "WHERE li.id = ? " .
                "ORDER BY lr.created_at DESC LIMIT 1"
            );
        } else {
            $stmt = $db->conn->prepare(
                "SELECT fi.item_name, fi.description, fi.category, fi.color, fi.brand, fi.image, " .
                "fr.location_found AS location, fr.date_found AS date_value, fr.status " .
                "FROM found_items fi " .
                "INNER JOIN found_reports fr ON fr.found_item_id = fi.id " .
                "WHERE fi.id = ? " .
                "ORDER BY fr.created_at DESC LIMIT 1"
            );
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$item) {
            $error_message = "Item not found.";
        }
    } catch (Exception $e) {
        $error_message = "Unable to load item details.";
    }
}

$title = $type === "lost" ? "Lost Item Detail" : "Found Item Detail";
$back_link = $type === "lost" ? "lost_items.php" : "found_items.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, "UTF-8"); ?></title>
    <link rel="stylesheet" href="../assets/item_detail.css">
    <link rel="stylesheet" href="../assets/layout.css" />
</head>

<body>
    <?php require_once __DIR__ . "/../includes/navbar.php"; ?>
    <main class="page">
        <header class="header">
            <div>
                <h1><?= htmlspecialchars($title, ENT_QUOTES, "UTF-8"); ?></h1>
                <p>Complete report details for this item.</p>
            </div>
            <a class="back-link" href="<?= htmlspecialchars($back_link, ENT_QUOTES, "UTF-8"); ?>">Back to list</a>
        </header>

        <?php if ($error_message !== ""): ?>
            <div class="empty"><?= htmlspecialchars($error_message, ENT_QUOTES, "UTF-8"); ?></div>
        <?php else: ?>
            <section class="card">
                <div class="card__image">
                    <?php if (!empty($item["image"])): ?>
                        <img src="../<?= htmlspecialchars($item["image"], ENT_QUOTES, "UTF-8"); ?>"
                            alt="<?= htmlspecialchars($item["item_name"], ENT_QUOTES, "UTF-8"); ?>">
                    <?php else: ?>
                        <span>No image</span>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="detail__title"><?= htmlspecialchars($item["item_name"], ENT_QUOTES, "UTF-8"); ?></h2>
                    <div class="detail__meta">
                        <div><span>Location:</span><?= htmlspecialchars($item["location"], ENT_QUOTES, "UTF-8"); ?></div>
                        <div><span>Date:</span><?= htmlspecialchars($item["date_value"], ENT_QUOTES, "UTF-8"); ?></div>
                        <?php if (!empty($item["category"])): ?>
                            <div><span>Category:</span><?= htmlspecialchars($item["category"], ENT_QUOTES, "UTF-8"); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($item["color"])): ?>
                            <div><span>Color:</span><?= htmlspecialchars($item["color"], ENT_QUOTES, "UTF-8"); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($item["brand"])): ?>
                            <div><span>Brand:</span><?= htmlspecialchars($item["brand"], ENT_QUOTES, "UTF-8"); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($item["status"])): ?>
                            <div><span>Status:</span><?= htmlspecialchars($item["status"], ENT_QUOTES, "UTF-8"); ?></div>
                        <?php endif; ?>
                    </div>
                    <p class="detail__description">
                        <?= $item["description"] !== "" ? htmlspecialchars($item["description"], ENT_QUOTES, "UTF-8") : "No description provided."; ?>
                    </p>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>

</html>