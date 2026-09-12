<?php
$pageTitle = 'Sienna Retail — Store operations made simple';
$activeNav = 'home';
include __DIR__ . '/partials/header.php';
?>

<div class="page">
  <section class="hero">
    <div class="hero__copy">
      <h1>Artisanal retail, precisely managed.</h1>
      <p>
        Sienna Retail brings clear inventory management, real-time stock thresholds, 
        and point of sale operations into one lightweight, self-contained system.
      </p>
      <div class="hero__actions">
        <a class="btn-pill btn-pill--solid" href="register.php" data-auth="guest">Create account</a>
        <a class="btn-pill btn-pill--outline" href="login.php" data-auth="guest">Sign in</a>
        <a class="btn-pill btn-pill--solid nav__hidden" href="pos.php" data-auth="user">Go to Point of Sale</a>
        <a class="btn-pill btn-pill--outline nav__hidden" href="dashboard.php" data-auth="user">View Analytics</a>
      </div>
    </div>

    <div class="hero__art" aria-hidden="true">
      <span class="hero__blob hero__blob--a"></span>
      <span class="hero__blob hero__blob--b"></span>
      <div class="hero__panel">
 
        <h3>Real-time POS</h3>
        <p>Instant inventory updates, revenue tracking, and order receipts.</p>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>