document.addEventListener('DOMContentLoaded', function () {
    const selectInstructor = document.getElementById('selectInstructor');
    


    document.getElementById('registroInstructorForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/backend/instructores.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.reset();
                alert(data.success);
            } else {
                alert(data.error);
            }
        })
        .catch(error => {
            alert('Ocurrió un error al procesar la solicitud');
        });
    });



    // Cargar instructores en el dropdown
    function cargarInstructores() {
        fetch('/backend/instructores.php')
            .then(response => response.json())
            .then(data => {
                selectInstructor.innerHTML = '<option value="">Seleccione un instructor</option>';
                data.forEach(instructor => {
                    const option = document.createElement('option');
                    option.value = instructor.id_instructor;
                    option.textContent = instructor.nombre;
                    selectInstructor.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando instructores:', error));
    }

    cargarInstructores();

    // Actualizar instructor
    document.getElementById('actualizarInstructorForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const idInstructor = selectInstructor.value;
        const nombre = document.getElementById('editNombre').value;
        const especialidad = document.getElementById('editEspecialidad').value;
        const telefono = document.getElementById('editTelefono').value;
        const correo = document.getElementById('editCorreo').value;
        const salario = document.getElementById('editSalario').value;

        fetch('/backend/instructores.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idInstructor, nombre, especialidad, telefono, correo, salario })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarInstructores();
        })
        .catch(error => console.error('Error actualizando:', error));
    });

    // Eliminar instructor
    document.getElementById('eliminarInstructor').addEventListener('click', function () {
        const idInstructor = selectInstructor.value;
        if (!confirm('¿Seguro que deseas eliminar este instructor?')) return;

        fetch('/backend/instructores.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idInstructor })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarInstructores();
        })
        .catch(error => console.error('Error eliminando:', error));
    });
});
