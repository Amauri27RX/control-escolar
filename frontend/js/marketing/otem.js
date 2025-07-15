document.addEventListener("DOMContentLoaded", function () {
  const checkbox = document.getElementById("usa_otem");
  const tituloGroup = document.getElementById("titulo");
  const cedulaGroup = document.getElementById("cedula");
  const constanciaGroup = document.getElementById("otem");

  checkbox.addEventListener("change", function () {
    if (this.checked) {
      tituloGroup.style.display = "none";
      cedulaGroup.style.display = "none";
      constanciaGroup.style.display = "block";
    } else {
      tituloGroup.style.display = "block";
      cedulaGroup.style.display = "block";
      constanciaGroup.style.display = "none";
    }
  });
});
