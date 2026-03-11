<?php
require_once __DIR__ . "/../core/auth.php";
auth::login_check();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item</title>
    <link rel="stylesheet" href="../assets/report.css">
    <link rel="stylesheet" href="../assets/layout.css" />
</head>

<body>
    <?php require_once __DIR__ . "/../includes/navbar.php"; ?>
    <main class="page">
        <header class="header">
            <div>
                <h1>Report a Lost Item</h1>
                <p>Share details about the missing item so the community can help find it.</p>
            </div>
            <a class="back-link" href="home.php">Back to home</a>
        </header>

        <section class="card">
            <form action="../controllers/reportLostController.php" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="field">
                        <label for="item_name">Item name</label>
                        <input type="text" id="item_name" name="item_name" placeholder="Wallet, phone, keys" required>
                    </div>
                    <div class="field">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" placeholder="Electronics, accessories">
                    </div>
                    <div class="field">
                        <label for="color">Color</label>
                        <input type="text" id="color" name="color" placeholder="Black, silver, red">
                    </div>
                    <div class="field">
                        <label for="brand">Brand</label>
                        <input type="text" id="brand" name="brand" placeholder="Apple, Samsung, Nike">
                    </div>
                    <div class="field span-2">
                        <label for="item_description">Item description</label>
                        <textarea id="item_description" name="item_description"
                            placeholder="Distinctive marks, accessories, or identifiers"></textarea>
                    </div>
                    <div class="field">
                        <label for="location_lost">Location lost</label>
                        <input type="text" id="location_lost" name="location_lost"
                            placeholder="Library, parking lot, bus stop" required>
                    </div>
                    <div class="field">
                        <label for="date_lost">Date lost</label>
                        <input type="date" id="date_lost" name="date_lost" required>
                    </div>
                    <div class="field span-2">
                        <label for="report_description">Additional report notes</label>
                        <textarea id="report_description" name="report_description"
                            placeholder="Anything else that might help"></textarea>
                    </div>
                    <div class="field span-2">
                        <label for="image">Upload item photo (optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="actions">
                    <button type="submit" name="submit_report">Submit report</button>
                </div>
                <p class="helper">Make sure the details are accurate so matches can be verified quickly.</p>
            </form>
        </section>
    </main>
    <?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>

</html>