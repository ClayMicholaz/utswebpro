<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$lost_total = 0;
$found_total = 0;

try {
    $db = new database();

    $result = $db->conn->query("SELECT COUNT(*) AS total FROM lost_items");
    $lost_total = $result ? (int) $result->fetch_assoc()["total"] : 0;

    $result = $db->conn->query("SELECT COUNT(*) AS total FROM found_items");
    $found_total = $result ? (int) $result->fetch_assoc()["total"] : 0;
} catch (Exception $e) {
    $lost_total = 0;
    $found_total = 0;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lost and Found</title>
    <link rel="stylesheet" href="../assets/home.css" />
    <link rel="stylesheet" href="../assets/layout.css" />
</head>

<body>
    <?php require_once __DIR__ . "/../includes/navbar.php"; ?>
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
                <a class="stat" href="lost_items.php">
                    <span class="stat__value"><?= number_format($lost_total); ?></span>
                    <span class="stat__label">Lost items</span>
                </a>
                <a class="stat" href="found_items.php">
                    <span class="stat__value"><?= number_format($found_total); ?></span>
                    <span class="stat__label">Found items</span>
                </a>
            </div>
        </div>

        <div class="hero__visual" aria-hidden="true">
            <div id="three-stage" class="hero__canvas"></div>
            <div class="hero__glow"></div>
        </div>
    </main>

    <?php require_once __DIR__ . "/../includes/footer.php"; ?>
    <script type="module" src="../assets/home.js"></script>
</body>

</html>