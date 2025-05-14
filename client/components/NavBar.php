<link rel="stylesheet" href="/FYP2025/SPAMS/client/css/Navbar.css" />

<?php
function renderBreadcrumb(array $crumbs)
{
    // Extract URLs for use in data-attribute
    $crumbUrls = array_map(fn($crumb) => $crumb['url'], $crumbs);
    $jsonUrls = htmlspecialchars(json_encode($crumbUrls), ENT_QUOTES, 'UTF-8');

    echo "<nav class=\"breadcrumb-nav\" data-crumbs='$jsonUrls'><ul class=\"breadcrumb\">";

    $lastIndex = count($crumbs) - 1;
    foreach ($crumbs as $index => $crumb) {
        if ($index !== $lastIndex) {
            echo "<li><a href=\"{$crumb['url']}\">{$crumb['label']}</a></li>";
            echo "<li>&raquo;</li>"; // separator
        } else {
            echo "<li class=\"active\">{$crumb['label']}</li>";
        }
    }

    echo '</ul></nav>';
}
?>