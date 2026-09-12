<?php
require_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Store Analytics — Sienna Retail';
$activeNav = 'dashboard';
include __DIR__ . '/partials/header.php';
?>

<div class="page">
  <div class="dashboard-shell">
    <div style="margin-bottom: 2rem;">
      <h1>Store Performance & Analytics</h1>
      <p>Automated metrics for store inventory health and revenue.</p>
    </div>

    <div class="grid-3">
      <div class="stat-card">
        <p class="glass-card__eyebrow">Total Revenue</p>
        <div id="stat-total-revenue" class="stat-card__val">$0.00</div>
      </div>
      <div class="stat-card">
        <p class="glass-card__eyebrow">Low Stock Items</p>
        <div id="stat-low-stock-count" class="stat-card__val">0</div>
      </div>
      <div class="stat-card">
        <p class="glass-card__eyebrow">Top Item Sold</p>
        <div id="stat-top-item" class="stat-card__val" style="font-size: 1.3rem;">-</div>
      </div>
    </div>

    <div class="grid-2">
      <div class="stat-card">
        <h3>Low Stock Alerts</h3>
        <p style="font-size: 0.85rem; margin-bottom: 0.8rem;">Items at or below reorder threshold.</p>
        <table class="table-clean" id="table-low-stock">
          <thead>
            <tr><th>Product</th><th>In Stock</th><th>Reorder Level</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="stat-card">
        <h3>Fast-Moving Products</h3>
        <p style="font-size: 0.85rem; margin-bottom: 0.8rem;">Top seller items by total units sold.</p>
        <table class="table-clean" id="table-fast-moving">
          <thead>
            <tr><th>Product</th><th>Units Sold</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$pageScript = 'assets/js/analytics.js';
include __DIR__ . '/partials/footer.php';
?>