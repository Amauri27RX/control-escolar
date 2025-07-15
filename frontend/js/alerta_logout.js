let alertaMostrada = false;

window.addEventListener('DOMContentLoaded', () => {
  history.pushState(null, '', location.href);
});

window.addEventListener('popstate', () => {
  if (!alertaMostrada) {
    alertaMostrada = true;

    const confirmar = confirm("¿Deseas cerrar sesión?");
    if (confirmar) {
      window.location.href = '../../backend/logout.php';
    } else {
      history.pushState(null, '', location.href);
      alertaMostrada = false;
    }
  }
});


