document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('register-form');
  const alertBox = document.getElementById('register-alert');
  const submitBtn = document.getElementById('register-submit');

  const showAlert = (message, kind) => {
    alertBox.textContent = message;
    alertBox.className = `alert is-visible alert--${kind}`;
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm').value;

    if (password !== confirm) {
      showAlert("Passwords don't match.", 'error');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating account…';

    const { ok, data } = await postJSON('/api/register.php', { username, email, password });

    if (ok && data.success) {
      showAlert('Account created. Redirecting to sign in…', 'ok');
      setTimeout(() => { window.location.href = 'login.php'; }, 900);
      return;
    }

    showAlert(data.message || 'Registration failed.', 'error');
    submitBtn.disabled = false;
    submitBtn.textContent = 'Create account';
  });
});