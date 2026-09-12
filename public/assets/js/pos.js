document.addEventListener('DOMContentLoaded', () => {
  let catalog = [];
  const cart = new Map();

  const productListEl = document.getElementById('product-list');
  const cartTableBody = document.querySelector('#cart-table tbody');
  const cartTotalEl = document.getElementById('cart-total');
  const checkoutBtn = document.getElementById('btn-checkout');
  const alertEl = document.getElementById('pos-alert');

  const showAlert = (msg, type = 'error') => {
    alertEl.textContent = msg;
    alertEl.className = `alert is-visible alert--${type}`;
  };

  const renderCart = () => {
    cartTableBody.innerHTML = '';
    let total = 0;

    cart.forEach((item) => {
      const subtotal = item.selling_price * item.quantity;
      total += subtotal;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.product_name}</td>
        <td>${item.quantity}</td>
        <td>$${Number(item.selling_price).toFixed(2)}</td>
        <td>$${subtotal.toFixed(2)}</td>
      `;
      cartTableBody.appendChild(tr);
    });

    cartTotalEl.textContent = total.toFixed(2);
  };

  const loadProducts = async () => {
    const { ok, data } = await getJSON('/api/products.php');
    if (!ok || !data.success) return;

    catalog = data.products;
    productListEl.innerHTML = catalog
      .map(
        (p) => `
      <div class="item-row">
        <div class="item-row__info">
          <h4>${p.product_name}</h4>
          <p>$${Number(p.selling_price).toFixed(2)} | In Stock: ${p.stock_quantity}</p>
        </div>
        <button class="nav__cta" data-add="${p.product_id}" ${p.stock_quantity < 1 ? 'disabled style="opacity:0.5;"' : ''}>
          ${p.stock_quantity < 1 ? 'Out of Stock' : 'Add to Cart'}
        </button>
      </div>
    `,
      )
      .join('');

    document.querySelectorAll('[data-add]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        const id = Number(e.target.getAttribute('data-add'));
        const product = catalog.find((p) => p.product_id === id);
        if (!product) return;

        const currentQty = cart.has(id) ? cart.get(id).quantity : 0;
        if (currentQty + 1 > product.stock_quantity) {
          showAlert(
            `Cannot exceed available stock limit (${product.stock_quantity}).`,
          );
          return;
        }

        cart.set(id, { ...product, quantity: currentQty + 1 });
        renderCart();
      });
    });
  };

  checkoutBtn.addEventListener('click', async () => {
    if (cart.size === 0) {
      showAlert('Cart is currently empty.');
      return;
    }

    checkoutBtn.disabled = true;
    const payload = {
      cart: Array.from(cart.values()).map((item) => ({
        product_id: item.product_id,
        quantity: item.quantity,
      })),
    };

    const { ok, data } = await postJSON('/api/sales.php', payload);

    if (ok && data.success) {
      showAlert(
        `Receipt ${data.transaction_number} processed ($${data.total_amount.toFixed(2)}).`,
        'ok',
      );
      cart.clear();
      renderCart();
      await loadProducts();
    } else {
      showAlert(data.message || 'Transaction failed.');
    }

    checkoutBtn.disabled = false;
  });

  loadProducts();
});
