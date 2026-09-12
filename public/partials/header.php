<?php
$pageTitle = $pageTitle ?? 'Sienna Retail';
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="nav">
  <a class="nav__brand" href="index.php">
    <span class="nav__brand-mark"></span>Sienna Retail
  </a>
  <ul class="nav__links">
    <li>
      <a class="nav__link <?= $activeNav === 'home' ? 'is-active' : '' ?>" href="index.php">Home</a>
    </li>
    <li data-auth="guest">
      <a class="nav__link <?= $activeNav === 'login' ? 'is-active' : '' ?>" href="login.php">Sign in</a>
    </li>
    <li data-auth="guest">
      <a class="nav__cta" href="register.php">Create account</a>
    </li>
    <li data-auth="user" class="nav__hidden">
      <a class="nav__link <?= $activeNav === 'dashboard' ? 'is-active' : '' ?>" href="dashboard.php">Analytics</a>
    </li>
    <li data-auth="user" class="nav__hidden">
      <a class="nav__link <?= $activeNav === 'pos' ? 'is-active' : '' ?>" href="pos.php">Point of Sale</a>
    </li>
    <li data-auth="user" class="nav__hidden">
      <button class="nav__cta" type="button" data-action="logout">Sign out</button>
    </li>
  </ul>
</nav>