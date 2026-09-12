document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('login-form');
  const alertBox = document.getElementById('login-alert');
  const submitBtn = document.getElementById('login-submit');

  const showAlert = (message, kind) => {
    alertBox.textContent = message;
    alertBox.className = `alert is-visible alert--${kind}`;
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    submitBtn.disabled = true;
    submitBtn.textContent = 'Signing in…';

    const identifier = document.getElementById('identifier').value.trim();
    const password = document.getElementById('password').value;

    const { ok, data } = await postJSON('api/login.php', {
      identifier,
      password,
    });

    if (ok && data.success) {
      window.location.href = 'dashboard.php';
      return;
    }

    showAlert(
      data.message || 'Login failed. Please check credentials.',
      'error',
    );
    submitBtn.disabled = false;
    submitBtn.textContent = 'Sign in';
  });
});
