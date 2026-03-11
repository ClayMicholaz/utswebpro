<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$items = [];

try {
    $db = new database();
    $sql = "SELECT li.id, li.item_name, lr.location_lost, lr.date_lost " .
        "FROM lost_items li " .
        "INNER JOIN lost_reports lr ON lr.lost_item_id = li.id " .
        "ORDER BY lr.created_at DESC";
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
    <title>Lost Items</title>
    <link rel="stylesheet" href="../assets/items.css">
</head>

<body>
    <main class="page">
        <header class="header">
            <div>
                <h1>Lost Items</h1>
                <p>All items reported lost by users, listed with the latest report details.</p>
            </div>
            <a class="back-link" href="home.php">Back to home</a>
        </header>

        <?php if (count($items) === 0): ?>
            <div class="empty">No lost items have been reported yet.</div>
        <?php else: ?>
            <ol class="list">
                <?php foreach ($items as $item): ?>
                    <li class="list__item">
                        <a class="list__link" href="item_detail.php?id=<?= (int) $item["id"]; ?>&type=lost">
                            <div class="list__name"><?= htmlspecialchars($item["item_name"], ENT_QUOTES, "UTF-8"); ?></div>
                            <div class="list__meta">
                                <span>Location:</span>
                                <?= htmlspecialchars($item["location_lost"], ENT_QUOTES, "UTF-8"); ?>
                            </div>
                            <div class="list__meta">
                                <span>Date:</span>
                                <?= htmlspecialchars($item["date_lost"], ENT_QUOTES, "UTF-8"); ?>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </main>
</body>

</html>