document.addEventListener('DOMContentLoaded', function () {
    const selectInstructor = document.getElementById('id_instructor');
    const selectClase = document.getElementById('selectClase');
    const editInstructor = document.getElementById('editInstructor');

    function cargarInstructores() {
        fetch('/backend/instructores.php')
            .then(response => response.json())
            .then(data => {
                selectInstructor.innerHTML = '<option value="">Seleccione un instructor</option>';
                editInstructor.innerHTML = '<option value="">Seleccione un instructor</option>';
                data.forEach(instructor => {
                    const option = document.createElement('option');
                    option.value = instructor.id_instructor;
                    option.textContent = instructor.nombre;
                    selectInstructor.appendChild(option);

                    const editOption = option.cloneNode(true);
                    editInstructor.appendChild(editOption);
                });
            })
            .catch(error => console.error('Error cargando instructores:', error));
    }

    function cargarClases() {
        fetch('/backend/clases.php')
            .then(response => response.json())
            .then(data => {
                selectClase.innerHTML = '<option value="">Seleccione una clase</option>';
                data.forEach(clase => {
                    const option = document.createElement('option');
                    option.value = clase.id_clase;
                    option.textContent = clase.nombre_clase;
                    selectClase.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando clases:', error));
    }

    cargarInstructores();
    cargarClases();

    // Registrar nueva clase
    document.getElementById('registroClaseForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const nombreClase = document.getElementById('nombre_clase').value;
        const descripcion = document.getElementById('descripcion').value;
        const idInstructor = selectInstructor.value;

        fetch('/backend/clases.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombreClase, descripcion, idInstructor })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarClases();
        })
        .catch(error => console.error('Error:', error));
    });

    // Eliminar clase
    document.getElementById('eliminarClase').addEventListener('click', function () {
        const idClase = selectClase.value;
        if (!confirm('¿Seguro que deseas eliminar esta clase?')) return;

        fetch('/backend/clases.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idClase })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarClases();
        })
        .catch(error => console.error('Error eliminando:', error));
    });
});
