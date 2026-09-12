<?php
require_once __DIR__ . '/../config/session.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Register Staff — Sienna Retail';
$activeNav = 'register';
include __DIR__ . '/partials/header.php';
?>

<div class="page">
  <div class="center-shell">
    <div class="glass-card">
      <p class="glass-card__eyebrow">Get started</p>
      <h1>Create staff account</h1>
      <p class="lede">Takes about 20 seconds. Instant dashboard access.</p>

      <div id="register-alert" class="alert"></div>

      <form id="register-form" novalidate>
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" autocomplete="username" minlength="3" maxlength="50" required>
        </div>
        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" required>
          <small>At least 8 characters.</small>
        </div>
        <div class="field">
          <label for="confirm">Confirm password</label>
          <input type="password" id="confirm" name="confirm" autocomplete="new-password" minlength="8" required>
        </div>
        <button type="submit" id="register-submit" class="btn-primary">Create account</button>
      </form>

      <p class="form-footer">Already registered? <a href="login.php">Sign in</a></p>
    </div>
  </div>
</div>

<?php
$pageScript = 'assets/js/register.js';
include __DIR__ . '/partials/footer.php';
?>