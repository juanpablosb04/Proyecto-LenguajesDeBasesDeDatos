document.addEventListener('DOMContentLoaded', function () {
    const registrarClaseForm = document.getElementById('registrarClaseForm');
    const selectClase = document.getElementById('selectClase');
    const formEditarClase = document.getElementById('formEditarClase');

    function cargarClases(){
    fetch('/backend/clases.php')
        .then(response => response.json())
        .then(data => {
            selectClase.innerHTML = '<option value="">Seleccione una clase...</option>';
            data.forEach(clase => {
                const option = document.createElement('option');
                option.value = clase.ID_CLASE;
                option.textContent = clase.NOMBRE_CLASE;
                selectClase.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar clases:', error);
            selectInstructor.innerHTML = '<option value="">Error al cargar clases</option>';
        });
    }


        selectClase.addEventListener('change', function () {
            const claseId = this.value;
    
            if (claseId) {
                fetch(`/backend/clases.php?id=${claseId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('id_clase').value = data.ID_CLASE;
                            document.getElementById('Getnombre_clase').value = data.NOMBRE_CLASE;
                            document.getElementById('Getdescripcion').value = data.DESCRIPCION;
                            document.getElementById('Getid_instructor').value = data.ID_INSTRUCTOR;
    
                            limpiarBtn.classList.remove('hidden');
                            eliminarBtn.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener detalles del instructor:', error);
                    });
            } else {
                formEditarInstructor.reset();
            }
        });


        editarBtn.addEventListener('click', function () {
            const id_clase = document.getElementById('id_clase').value;
            const nombre_clase = document.getElementById('Getnombre_clase').value;
            const descripcion = document.getElementById('Getdescripcion').value;
            const id_instructor = document.getElementById('Getid_instructor').value;
        
            const datosActualizados = {
                id_clase: id_clase,
                nombre_clase: nombre_clase,
                descripcion: descripcion,
                id_instructor: id_instructor
            };
        
            fetch('/backend/clases.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosActualizados)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    cargarClases();
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error al actualizar los datos:', error);
                alert('Ocurrió un error al actualizar los datos');
            });
        });


        limpiarBtn.addEventListener('click', function () {
        
            document.getElementById('id_clase').value = '';
            document.getElementById('Getnombre_clase').value = '';
            document.getElementById('Getdescripcion').value = '';
            document.getElementById('Getid_instructor').value = '';

    
            limpiarBtn.classList.add('hidden');
            selectClase.selectedIndex = 0;
    
        });


        eliminarBtn.addEventListener('click', function () {
            const id_clase = document.getElementById('id_clase').value;

            if (id_clase) {
                if (confirm('¿Estás seguro de que deseas eliminar esta clase?')) {
                    fetch('/backend/clases.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_clase: id_clase })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                limpiarBtn.classList.add('hidden');
                                eliminarBtn.classList.add('hidden');
                                document.getElementById('id_clase').value = '';
                                document.getElementById('Getnombre_clase').value = '';
                                document.getElementById('Getdescripcion').value = '';
                                document.getElementById('Getid_instructor').value = '';
                                selectClase.selectedIndex = 0;
                                cargarClases();
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar la clase:', error);
                            alert('Ocurrió un error al eliminar la clase');
                        });
                }
            }
        });




    if (registrarClaseForm) {
        registrarClaseForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const nombreClase = document.getElementById('nombre_clase').value;
            const descripcion = document.getElementById('descripcion').value;
            const idInstructor = document.getElementById('id_instructor').value;

            if (nombreClase && descripcion && idInstructor) {
                const formData = new FormData();
                formData.append('nombre_clase', nombreClase);
                formData.append('descripcion', descripcion);
                formData.append('id_instructor', idInstructor);

                console.log('Enviando datos al backend:', { nombreClase, descripcion, idInstructor });

                fetch('/backend/clases.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Respuesta recibida:', response);
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    if (data.success) {
                        alert(data.success);
                        registrarClaseForm.reset();
                        cargarClases();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error registrando la clase:', error);
                    alert('Ocurrió un error al registrar la clase');
                });
            } else {
                alert('Por favor, complete todos los campos');
            }
        });
    } else {
        console.error('No se encontró el formulario de registrar clase');
    }
});