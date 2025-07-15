document.querySelectorAll('.drop-zone').forEach(dropZone => {
    const inputId = dropZone.getAttribute('data-input');
    const input = document.getElementById(inputId);
    const fileInfo = dropZone.querySelector('.file-info');
    const fileNameSpan = dropZone.querySelector('.file-name');
    const deleteBtn = dropZone.querySelector('.delete-btn');

    // Abrir explorador al hacer clic
    dropZone.addEventListener('click', () => input.click());

    // Drag and drop
    dropZone.addEventListener('dragover', e => {
      e.preventDefault();
      dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
      dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', e => {
      e.preventDefault();
      dropZone.classList.remove('dragover');

      if (e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        if (file.type === "application/pdf") {
          input.files = e.dataTransfer.files;
          fileNameSpan.textContent = file.name;
          fileInfo.style.display = 'flex';
        } else {
          alert("Solo se permiten archivos PDF");
        }
      }
    });

    // Cuando se selecciona archivo con el input
    input.addEventListener('change', () => {
      if (input.files.length > 0) {
        const file = input.files[0];
        fileNameSpan.textContent = file.name;
        fileInfo.style.display = 'flex';
      }
    });

    // Botón para eliminar archivo
    deleteBtn.addEventListener('click', () => {
      input.value = '';
      fileNameSpan.textContent = '';
      fileInfo.style.display = 'none';
    });
  });
