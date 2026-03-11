<?php
require_once __DIR__ . "/../core/auth.php";
require_once __DIR__ . "/../config/database.php";

auth::login_check();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: /utswebpro/pages/home.php");
    exit();
}

$db = null;
$lostItems = [];
$foundItems = [];
$flashMessage = null;
$flashType = "success";
$rawFilter = isset($_GET["filter"]) ? $_GET["filter"] : (isset($_POST["filter"]) ? $_POST["filter"] : "all");
$rawSearch = isset($_GET["q"]) ? $_GET["q"] : (isset($_POST["q"]) ? $_POST["q"] : "");
$rawDate = isset($_GET["date"]) ? $_GET["date"] : (isset($_POST["date"]) ? $_POST["date"] : "");

$filterType = strtolower(trim((string) $rawFilter));
$searchName = trim((string) $rawSearch);
$filterDate = trim((string) $rawDate);
$searchId = ctype_digit($searchName) ? (int) $searchName : 0;

if ($filterDate !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
    $filterDate = "";
}

if (!in_array($filterType, ["all", "lost", "found"], true)) {
    $filterType = "all";
}

$showLostSection = $filterType === "all" || $filterType === "lost";
$showFoundSection = $filterType === "all" || $filterType === "found";

try {
    $db = new database();
} catch (Exception $e) {
    $flashMessage = "Database connection failed.";
    $flashType = "error";
}

if ($db && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve_return"])) {
    $foundReportId = isset($_POST["found_report_id"]) ? (int) $_POST["found_report_id"] : 0;
    $claimerEmail = isset($_POST["claimer_email"]) ? trim($_POST["claimer_email"]) : "";
    $claimDescription = isset($_POST["claim_description"]) ? trim($_POST["claim_description"]) : "";

    if ($foundReportId <= 0 || $claimerEmail === "") {
        $flashMessage = "Found report and claimer email are required.";
        $flashType = "error";
    } else {
        try {
            $db->conn->begin_transaction();

            $stmt = $db->conn->prepare("SELECT id, status FROM found_reports WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $foundReportId);
            $stmt->execute();
            $report = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$report) {
                throw new Exception("Found report not found.");
            }

            if ($report["status"] === "returned") {
                throw new Exception("This item has already been returned.");
            }

            $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $claimerEmail);
            $stmt->execute();
            $claimer = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$claimer) {
                throw new Exception("Claimer account not found.");
            }

            $adminEmail = $_SESSION["email"];
            $stmt = $db->conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $adminEmail);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$admin) {
                throw new Exception("Admin account not found.");
            }

            $stmt = $db->conn->prepare("SELECT id FROM item_claims WHERE found_report_id = ? AND status = 'approved' LIMIT 1");
            $stmt->bind_param("i", $foundReportId);
            $stmt->execute();
            $approvedClaim = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($approvedClaim) {
                throw new Exception("This found report already has an approved claim.");
            }

            $proofImage = null;
            $status = "approved";
            $claimerId = (int) $claimer["id"];
            $adminId = (int) $admin["id"];

            $stmt = $db->conn->prepare(
                "INSERT INTO item_claims (user_id, found_report_id, claim_description, proof_image, status, approved_by) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iisssi", $claimerId, $foundReportId, $claimDescription, $proofImage, $status, $adminId);
            $stmt->execute();
            $stmt->close();

            $newStatus = "returned";
            $stmt = $db->conn->prepare("UPDATE found_reports SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $foundReportId);
            $stmt->execute();
            $stmt->close();

            $db->conn->commit();
            $flashMessage = "Claim approved and item marked as returned.";
            $flashType = "success";
        } catch (Exception $e) {
            $db->conn->rollback();
            $flashMessage = $e->getMessage();
            $flashType = "error";
        }
    }
}

if ($db) {
    try {
        if ($showLostSection) {
            $lostSql = "SELECT li.id, li.item_name, li.category, li.color, li.brand, lr.id AS report_id, lr.location_lost, lr.date_lost, lr.status, u.name AS reporter_name, u.email AS reporter_email " .
                "FROM lost_reports lr " .
                "INNER JOIN lost_items li ON li.id = lr.lost_item_id " .
                "INNER JOIN users u ON u.id = lr.user_id " .
                "WHERE ((? = '' OR li.item_name LIKE ?) OR (? > 0 AND lr.id = ?)) " .
                "AND (? = '' OR lr.date_lost = ?) " .
                "ORDER BY lr.created_at DESC";
            $lostStmt = $db->conn->prepare($lostSql);
            $lostSearch = "%" . $searchName . "%";
            $lostStmt->bind_param("ssiiss", $searchName, $lostSearch, $searchId, $searchId, $filterDate, $filterDate);
            $lostStmt->execute();
            $lostResult = $lostStmt->get_result();
            $lostItems = $lostResult ? $lostResult->fetch_all(MYSQLI_ASSOC) : [];
            $lostStmt->close();
        }

        if ($showFoundSection) {
            $foundSql = "SELECT fi.id, fi.item_name, fi.category, fi.color, fi.brand, fr.id AS report_id, fr.location_found, fr.date_found, fr.status, " .
                "u.name AS finder_name, u.email AS finder_email, ic.id AS approved_claim_id, ic.claim_description, cu.name AS claimer_name, cu.email AS claimer_email " .
                "FROM found_reports fr " .
                "INNER JOIN found_items fi ON fi.id = fr.found_item_id " .
                "INNER JOIN users u ON u.id = fr.user_id " .
                "LEFT JOIN item_claims ic ON ic.found_report_id = fr.id AND ic.status = 'approved' " .
                "LEFT JOIN users cu ON cu.id = ic.user_id " .
                "WHERE ((? = '' OR fi.item_name LIKE ?) OR (? > 0 AND fr.id = ?)) " .
                "AND (? = '' OR fr.date_found = ?) " .
                "ORDER BY fr.created_at DESC";
            $foundStmt = $db->conn->prepare($foundSql);
            $foundSearch = "%" . $searchName . "%";
            $foundStmt->bind_param("ssiiss", $searchName, $foundSearch, $searchId, $searchId, $filterDate, $filterDate);
            $foundStmt->execute();
            $foundResult = $foundStmt->get_result();
            $foundItems = $foundResult ? $foundResult->fetch_all(MYSQLI_ASSOC) : [];
            $foundStmt->close();
        }
    } catch (Exception $e) {
        $flashMessage = "Failed to load dashboard data.";
        $flashType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin_dashboard.css">
</head>

<body>
    <div class="wrap">
        <header class="top">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="muted">Verify claimers and approve item returns.</p>
            </div>
            <div class="actions">
                <form class="filter-form" method="get" action="">
                    <select name="filter" aria-label="Filter item type">
                        <option value="all" <?php echo $filterType === "all" ? "selected" : ""; ?>>All Items</option>
                        <option value="lost" <?php echo $filterType === "lost" ? "selected" : ""; ?>>Lost Items</option>
                        <option value="found" <?php echo $filterType === "found" ? "selected" : ""; ?>>Found Items
                        </option>
                    </select>
                    <input type="text" name="q" placeholder="Search item name or report ID"
                        value="<?php echo htmlspecialchars($searchName, ENT_QUOTES, "UTF-8"); ?>">
                    <input type="date" name="date"
                        value="<?php echo htmlspecialchars($filterDate, ENT_QUOTES, "UTF-8"); ?>">
                    <button class="btn" type="submit">Apply</button>
                </form>
                <a class="btn primary" href="/utswebpro/auth/logout.php">Logout</a>
            </div>
        </header>

        <?php if ($flashMessage): ?>
            <div class="flash <?php echo $flashType === "error" ? "error" : "success"; ?>">
                <?php echo htmlspecialchars($flashMessage, ENT_QUOTES, "UTF-8"); ?>
            </div>
        <?php endif; ?>

        <section class="grid">
            <?php if ($showLostSection): ?>
                <article class="card">
                    <div class="card__head">
                        <h2>Lost Items</h2>
                    </div>
                    <?php if (count($lostItems) === 0): ?>
                        <div class="empty">No lost item reports found.</div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Color / Brand</th>
                                        <th>Lost At</th>
                                        <th>Date</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lostItems as $row): ?>
                                        <tr>
                                            <td>#<?php echo (int) $row["report_id"]; ?></td>
                                            <td><?php echo htmlspecialchars($row["item_name"], ENT_QUOTES, "UTF-8"); ?></td>
                                            <td><?php echo htmlspecialchars((string) $row["category"], ENT_QUOTES, "UTF-8"); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars((string) $row["color"], ENT_QUOTES, "UTF-8"); ?> /
                                                <?php echo htmlspecialchars((string) $row["brand"], ENT_QUOTES, "UTF-8"); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $row["location_lost"], ENT_QUOTES, "UTF-8"); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $row["date_lost"], ENT_QUOTES, "UTF-8"); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars((string) $row["reporter_name"], ENT_QUOTES, "UTF-8"); ?><br>
                                                <span
                                                    class="muted"><?php echo htmlspecialchars((string) $row["reporter_email"], ENT_QUOTES, "UTF-8"); ?></span>
                                            </td>
                                            <td>
                                                <span
                                                    class="status <?php echo htmlspecialchars((string) $row["status"], ENT_QUOTES, "UTF-8"); ?>">
                                                    <?php echo htmlspecialchars((string) $row["status"], ENT_QUOTES, "UTF-8"); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endif; ?>

            <?php if ($showFoundSection): ?>
                <article class="card">
                    <div class="card__head">
                        <h2>Found Items & Return Approval</h2>
                    </div>
                    <?php if (count($foundItems) === 0): ?>
                        <div class="empty">No found item reports found.</div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Finder</th>
                                        <th>Found At</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($foundItems as $row): ?>
                                        <tr>
                                            <td>#<?php echo (int) $row["report_id"]; ?></td>
                                            <td><?php echo htmlspecialchars($row["item_name"], ENT_QUOTES, "UTF-8"); ?></td>
                                            <td><?php echo htmlspecialchars((string) $row["category"], ENT_QUOTES, "UTF-8"); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars((string) $row["finder_name"], ENT_QUOTES, "UTF-8"); ?><br>
                                                <span
                                                    class="muted"><?php echo htmlspecialchars((string) $row["finder_email"], ENT_QUOTES, "UTF-8"); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $row["location_found"], ENT_QUOTES, "UTF-8"); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $row["date_found"], ENT_QUOTES, "UTF-8"); ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="status <?php echo htmlspecialchars((string) $row["status"], ENT_QUOTES, "UTF-8"); ?>">
                                                    <?php echo htmlspecialchars((string) $row["status"], ENT_QUOTES, "UTF-8"); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ((string) $row["status"] === "returned" || !empty($row["approved_claim_id"])): ?>
                                                    <strong>Approved</strong><br>
                                                    <span
                                                        class="muted"><?php echo htmlspecialchars((string) $row["claimer_name"], ENT_QUOTES, "UTF-8"); ?></span><br>
                                                    <span
                                                        class="muted"><?php echo htmlspecialchars((string) $row["claimer_email"], ENT_QUOTES, "UTF-8"); ?></span>
                                                <?php else: ?>
                                                    <form method="post" class="approve-form">
                                                        <input type="hidden" name="filter"
                                                            value="<?php echo htmlspecialchars($filterType, ENT_QUOTES, "UTF-8"); ?>">
                                                        <input type="hidden" name="q"
                                                            value="<?php echo htmlspecialchars($searchName, ENT_QUOTES, "UTF-8"); ?>">
                                                        <input type="hidden" name="date"
                                                            value="<?php echo htmlspecialchars($filterDate, ENT_QUOTES, "UTF-8"); ?>">
                                                        <input type="hidden" name="found_report_id"
                                                            value="<?php echo (int) $row["report_id"]; ?>">
                                                        <input type="email" name="claimer_email" placeholder="Claimer email" required>
                                                        <textarea name="claim_description"
                                                            placeholder="Verification notes (optional)"></textarea>
                                                        <button class="btn primary" type="submit" name="approve_return"
                                                            value="1">Approve Return</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>