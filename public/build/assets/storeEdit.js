document.addEventListener('DOMContentLoaded', () => {
  const navLinks = document.querySelectorAll('.sidebar a.nav-link');
  const link = [...navLinks].find(x => x.href.endsWith('store'));
  link.classList.add('active')
});