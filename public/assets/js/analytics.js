document.addEventListener('DOMContentLoaded', () => {
  const loadAnalytics = async () => {
    const { ok, data } = await getJSON('/api/analytics.php');
    if (!ok || !data.success) return;

    const { analytics } = data;

    document.getElementById('stat-total-revenue').textContent =
      `$${analytics.total_revenue.toFixed(2)}`;
    document.getElementById('stat-low-stock-count').textContent =
      analytics.low_stock_alerts.length;

    const topProduct = analytics.fast_moving[0];
    document.getElementById('stat-top-item').textContent = topProduct
      ? topProduct.product_name
      : 'N/A';

    const lowStockTbody = document.querySelector('#table-low-stock tbody');
    lowStockTbody.innerHTML = analytics.low_stock_alerts.length
      ? analytics.low_stock_alerts
          .map(
            (item) => `
          <tr>
            <td>${item.product_name}</td>
            <td><span class="badge-low">${item.stock_quantity}</span></td>
            <td>${item.reorder_level}</td>
          </tr>
        `,
          )
          .join('')
      : '<tr><td colspan="3" class="text-muted">Stock levels healthy.</td></tr>';

    const fastMovingTbody = document.querySelector('#table-fast-moving tbody');
    fastMovingTbody.innerHTML = analytics.fast_moving.length
      ? analytics.fast_moving
          .map(
            (item) => `
          <tr>
            <td>${item.product_name}</td>
            <td><strong>${item.total_units_sold}</strong></td>
          </tr>
        `,
          )
          .join('')
      : '<tr><td colspan="2" class="text-muted">No sales recorded yet.</td></tr>';
  };

  loadAnalytics();
});
