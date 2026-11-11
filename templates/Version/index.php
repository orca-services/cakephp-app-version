<?php
/**
 * HTML representation of the app version.
 *
 * Override it in your application with `templates/plugin/AppVersion/Version/index.php`.
 *
 * @var \Cake\View\View $this
 * @var string $appVersion
 */

use function Cake\Core\h;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <title>App version</title>
</head>
<body>
<p>App version: <span class="app-version"><?= h($appVersion) ?></span></p>
</body>
</html>
