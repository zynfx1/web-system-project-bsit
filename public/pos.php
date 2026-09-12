<?php
require_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Point of Sale — Sienna Retail';
$activeNav = 'pos';
include __DIR__ . '/partials/header.php';
?>

<div class="page">
  <div class="dashboard-shell">
    <div class="grid-2">
      <div>
        <h2>Store Catalog</h2>
        <p style="font-size: 0.88rem; margin-bottom: 1.2rem;">Select items to add to checkout cart.</p>
        <div id="product-list"></div>
      </div>

      <div>
        <div class="stat-card">
          <h3>Current Checkout</h3>
          <div id="pos-alert" class="alert"></div>

          <table class="table-clean" id="cart-table">
            <thead>
              <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
            </thead>
            <tbody></tbody>
          </table>

          <div style="margin-top: 1.5rem; text-align: right;">
            <p style="font-size: 1.25rem; font-weight: 700; color: var(--ink);">
              Total: $<span id="cart-total">0.00</span>
            </p>
            <button id="btn-checkout" class="btn-primary" style="margin-top: 1rem;">Process Transaction</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$pageScript = 'assets/js/pos.js';
include __DIR__ . '/partials/footer.php';
?>