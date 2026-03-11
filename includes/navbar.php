<?php
require_once __DIR__ . "/functions.php";

app_start_session();

$currentFile = basename($_SERVER["PHP_SELF"] ?? "");
$isLoggedIn = isset($_SESSION["email"]);
$isAdmin = isset($_SESSION["role"]) && $_SESSION["role"] === "admin";

$links = [];

if ($isLoggedIn) {
    $links = [
        ["label" => "Home", "href" => "/utswebpro/pages/home.php", "file" => "home.php"],
        ["label" => "Lost Items", "href" => "/utswebpro/pages/lost_items.php", "file" => "lost_items.php"],
        ["label" => "Found Items", "href" => "/utswebpro/pages/found_items.php", "file" => "found_items.php"],
        ["label" => "Report Lost", "href" => "/utswebpro/pages/report_lost.php", "file" => "report_lost.php"],
        ["label" => "Report Found", "href" => "/utswebpro/pages/report_found.php", "file" => "report_found.php"],
    ];

    if ($isAdmin) {
        $links[] = ["label" => "Admin", "href" => "/utswebpro/pages/admin_dashboard.php", "file" => "admin_dashboard.php"];
    }
} else {
    $links = [
        ["label" => "Login", "href" => "/utswebpro/auth/login.php", "file" => "login.php"],
        ["label" => "Register", "href" => "/utswebpro/auth/register.php", "file" => "register.php"],
        ["label" => "Change Password", "href" => "/utswebpro/auth/change_password.php", "file" => "change_password.php"],
    ];
}
?>

<header class="lf-nav">
    <div class="lf-nav__inner">
        <a class="lf-nav__brand" href="/utswebpro/pages/home.php">Lost &amp; Found</a>
        <nav class="lf-nav__links" aria-label="Main navigation">
            <?php foreach ($links as $link): ?>
                <a class="lf-nav__link <?php echo $currentFile === $link["file"] ? "is-active" : ""; ?>"
                    href="<?php echo htmlspecialchars($link["href"], ENT_QUOTES, "UTF-8"); ?>">
                    <?php echo htmlspecialchars($link["label"], ENT_QUOTES, "UTF-8"); ?>
                </a>
            <?php endforeach; ?>
            <?php if ($isLoggedIn): ?>
                <a class="lf-nav__link lf-nav__link--logout" href="/utswebpro/auth/logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</header>