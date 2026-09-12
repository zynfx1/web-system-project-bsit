<?php
require_once __DIR__ . '/../config/session.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Sign in — Sienna Retail';
$activeNav = 'login';
include __DIR__ . '/partials/header.php';
?>

<div class="page">
  <div class="center-shell">
    <div class="glass-card">
      <p class="glass-card__eyebrow">Store Portal</p>
      <h1>Sign in</h1>
      <p class="lede">Use your store staff credentials.</p>

      <div id="login-alert" class="alert"></div>

      <form id="login-form" novalidate>
        <div class="field">
          <label for="identifier">Username or email</label>
          <input type="text" id="identifier" name="identifier" autocomplete="username" required>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" id="login-submit" class="btn-primary">Sign in</button>
      </form>

      <p class="form-footer">New staff member? <a href="register.php">Create account</a></p>
    </div>
  </div>
</div>

<?php
$pageScript = 'assets/js/login.js';
include __DIR__ . '/partials/footer.php';
?>