<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";
auth::login_check();

$items = [];
$rawSearch = isset($_GET["q"]) ? $_GET["q"] : "";
$rawDate = isset($_GET["date"]) ? $_GET["date"] : "";

$search = trim((string) $rawSearch);
$filterDate = trim((string) $rawDate);
$searchId = ctype_digit($search) ? (int) $search : 0;

if ($filterDate !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
    $filterDate = "";
}

try {
    $db = new database();
    $sql = "SELECT li.id, li.item_name, lr.id AS report_id, lr.location_lost, lr.date_lost " .
        "FROM lost_items li " .
        "INNER JOIN lost_reports lr ON lr.lost_item_id = li.id " .
        "WHERE ((? = '' OR li.item_name LIKE ?) OR (? > 0 AND lr.id = ?)) " .
        "AND (? = '' OR lr.date_lost = ?) " .
        "ORDER BY lr.created_at DESC";
    $stmt = $db->conn->prepare($sql);
    $searchLike = "%" . $search . "%";
    $stmt->bind_param("ssiiss", $search, $searchLike, $searchId, $searchId, $filterDate, $filterDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
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
    <link rel="stylesheet" href="../assets/layout.css" />
</head>

<body>
    <?php require_once __DIR__ . "/../includes/navbar.php"; ?>
    <main class="page">
        <header class="header">
            <div>
                <h1>Lost Items</h1>
                <p>All items reported lost by users, listed with the latest report details.</p>
            </div>
            <a class="back-link" href="home.php">Back to home</a>
        </header>

        <form class="filter-form" method="get" action="">
            <input type="text" name="q" placeholder="Search item name or report ID"
                value="<?= htmlspecialchars($search, ENT_QUOTES, "UTF-8"); ?>">
            <input type="date" name="date" value="<?= htmlspecialchars($filterDate, ENT_QUOTES, "UTF-8"); ?>">
            <button type="submit">Apply</button>
        </form>

        <?php if (count($items) === 0): ?>
            <div class="empty">No lost items have been reported yet.</div>
        <?php else: ?>
            <ol class="list">
                <?php foreach ($items as $item): ?>
                    <li class="list__item">
                        <a class="list__link" href="item_detail.php?id=<?= (int) $item["id"]; ?>&type=lost">
                            <div class="list__name">#<?= (int) $item["report_id"]; ?> -
                                <?= htmlspecialchars($item["item_name"], ENT_QUOTES, "UTF-8"); ?>
                            </div>
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
    <?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>

</html>