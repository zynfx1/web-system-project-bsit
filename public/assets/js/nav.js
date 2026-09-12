// Shared nav behaviour included on every page:
// 1) adds a shadow once the page scrolls
// 2) swaps "Sign in / Create account" for "Dashboard / Sign out" once we
//    know whether the visitor has a live session.

document.addEventListener('DOMContentLoaded', () => {
  const nav = document.querySelector('.nav');
  if (nav) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('is-scrolled', window.scrollY > 8);
    }, { passive: true });
  }

  const guestEls = document.querySelectorAll('[data-auth="guest"]');
  const userEls = document.querySelectorAll('[data-auth="user"]');

  getJSON('/api/me.php').then(({ ok }) => {
    guestEls.forEach((el) => el.classList.toggle('nav__hidden', ok));
    userEls.forEach((el) => el.classList.toggle('nav__hidden', !ok));
  });

  document.querySelectorAll('[data-action="logout"]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      await postJSON('/api/logout.php', {});
      window.location.href = 'index.php';
    });
  });
});
