<?php
require_once __DIR__ . "/../core/auth.php";
auth::login_check();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item</title>
    <link rel="stylesheet" href="../assets/report.css">
    <link rel="stylesheet" href="../assets/layout.css" />
</head>

<body>
    <?php require_once __DIR__ . "/../includes/navbar.php"; ?>
    <main class="page">
        <header class="header">
            <div>
                <h1>Report a Found Item</h1>
                <p>Provide details so the owner can verify and claim the item.</p>
            </div>
            <a class="back-link" href="home.php">Back to home</a>
        </header>

        <section class="card">
            <form action="../controllers/reportFoundController.php" method="post" enctype="multipart/form-data">
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
                        <label for="location_found">Location found</label>
                        <input type="text" id="location_found" name="location_found"
                            placeholder="Library, parking lot, bus stop" required>
                    </div>
                    <div class="field">
                        <label for="date_found">Date found</label>
                        <input type="date" id="date_found" name="date_found" required>
                    </div>
                    <div class="field span-2">
                        <label for="report_description">Additional report notes</label>
                        <textarea id="report_description" name="report_description"
                            placeholder="Anything else that might help"></textarea>
                    </div>
                    <div class="field span-2">
                        <label for="image">Upload item photo (optional)</label>
                        <div class="file-actions">
                            <input type="file" id="image" name="image" accept="image/*">
                            <button type="button" data-clear-file="#image">Delete image</button>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit" name="submit_report">Submit report</button>
                </div>
                <p class="helper">Keep the details accurate so the owner can verify the claim quickly.</p>
            </form>
        </section>
    </main>
    <?php require_once __DIR__ . "/../includes/footer.php"; ?>
    <script>
        document.querySelectorAll("[data-clear-file]").forEach((button) => {
            button.addEventListener("click", () => {
                const target = document.querySelector(button.dataset.clearFile);
                if (target) {
                    target.value = "";
                }
            });
        });
    </script>
</body>

</html>