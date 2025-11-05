
function toggleMenu() {
  const menu = document.getElementById('menu-list');
  if (menu.classList.contains('expanded')) {
    menu.classList.remove('expanded');
  } else {
    menu.classList.add('expanded');
  }
}