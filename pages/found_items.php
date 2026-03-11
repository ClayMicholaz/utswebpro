<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$items = [];

try {
    $db = new database();
    $sql = "SELECT fi.id, fi.item_name, fr.location_found, fr.date_found " .
        "FROM found_items fi " .
        "INNER JOIN found_reports fr ON fr.found_item_id = fi.id " .
        "ORDER BY fr.created_at DESC";
    $result = $db->conn->query($sql);
    if ($result) {
        $items = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    $items = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items</title>
    <link rel="stylesheet" href="../assets/items.css">
</head>

<body>
    <main class="page">
        <header class="header">
            <div>
                <h1>Found Items</h1>
                <p>All items reported found by users, listed with the latest report details.</p>
            </div>
            <a class="back-link" href="home.php">Back to home</a>
        </header>

        <?php if (count($items) === 0): ?>
            <div class="empty">No found items have been reported yet.</div>
        <?php else: ?>
            <ol class="list">
                <?php foreach ($items as $item): ?>
                    <li class="list__item">
                        <a class="list__link" href="item_detail.php?id=<?= (int) $item["id"]; ?>&type=found">
                            <div class="list__name"><?= htmlspecialchars($item["item_name"], ENT_QUOTES, "UTF-8"); ?></div>
                            <div class="list__meta">
                                <span>Location:</span>
                                <?= htmlspecialchars($item["location_found"], ENT_QUOTES, "UTF-8"); ?>
                            </div>
                            <div class="list__meta">
                                <span>Date:</span>
                                <?= htmlspecialchars($item["date_found"], ENT_QUOTES, "UTF-8"); ?>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </main>
</body>

</html>