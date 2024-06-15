document.addEventListener('DOMContentLoaded', () => {
  const navLinks = document.querySelectorAll('.sidebar a.nav-link');
  const link = [...navLinks].find(x => x.href.endsWith('history'));
  link.classList.add('active');
});

