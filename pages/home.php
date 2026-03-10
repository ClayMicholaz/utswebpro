<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$items_listed = 0;
$items_returned = 0;

try {
    $db = new database();

    $result = $db->conn->query("SELECT COUNT(*) AS total FROM lost_items");
    $lost_total = $result ? (int) $result->fetch_assoc()["total"] : 0;

    $result = $db->conn->query("SELECT COUNT(*) AS total FROM found_items");
    $found_total = $result ? (int) $result->fetch_assoc()["total"] : 0;

    $items_listed = $lost_total + $found_total;

    $result = $db->conn->query("SELECT COUNT(*) AS total FROM found_reports WHERE status = 'returned'");
    $items_returned = $result ? (int) $result->fetch_assoc()["total"] : 0;
} catch (Exception $e) {
    $items_listed = 0;
    $items_returned = 0;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lost and Found</title>
    <link rel="stylesheet" href="../assets/home.css" />
</head>

<body>
    <main class="hero">
        <div class="hero__content">
            <p class="hero__eyebrow">Lost it. Found it. Fixed it.</p>
            <h1>Reconnect people with what matters.</h1>
            <p class="hero__lead">
                Report lost items, list found items, and match them fast. A simple
                hub for your community.
            </p>
            <div class="hero__actions">
                <a class="btn btn--primary" href="report_lost.php">Report Lost</a>
                <a class="btn btn--ghost" href="report_found.php">Report Found</a>
            </div>
            <div class="hero__stats">
                <div>
                    <span class="stat__value"><?= number_format($items_listed); ?></span>
                    <span class="stat__label">Items listed</span>
                </div>
                <div>
                    <span class="stat__value"><?= number_format($items_returned); ?></span>
                    <span class="stat__label">Items returned</span>
                </div>
            </div>
        </div>

        <div class="hero__visual" aria-hidden="true">
            <div id="three-stage" class="hero__canvas"></div>
            <div class="hero__glow"></div>
        </div>
    </main>

    <script type="module" src="../assets/home.js"></script>
</body>

</html>