document.addEventListener('DOMContentLoaded', function() {

    const selectInstructor = document.getElementById('selectInstructor');
    const formEditarInstructor = document.getElementById('formEditarInstructor');

    fetch('/backend/instructores.php')
        .then(response => response.json())
        .then(data => {
            selectInstructor.innerHTML = '<option value="">Seleccione un instructor...</option>';
            data.forEach(instructor => {
                const option = document.createElement('option');
                option.value = instructor.ID_INSTRUCTOR;
                option.textContent = instructor.NOMBRE;
                selectInstructor.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar instructores:', error);
            selectInstructor.innerHTML = '<option value="">Error al cargar instructores</option>';
        });

    document.getElementById('registroForm').addEventListener('submit', function(e) {
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






    selectInstructor.addEventListener('change', function () {
        const instructorId = this.value;

        if (instructorId) {
            fetch(`/backend/instructores.php?id=${instructorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('id_instructor').value = data.ID_INSTRUCTOR;
                        document.getElementById('Getnombre').value = data.NOMBRE;
                        document.getElementById('Getespecialidad').value = data.ESPECIALIDAD;
                        document.getElementById('Gettelefono').value = data.TELEFONO;
                        document.getElementById('Getcorreo').value = data.CORREO;
                        document.getElementById('Getsalario').value = data.SALARIO;

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

        const id_instructor = document.getElementById('id_instructor').value;
        const nombre = document.getElementById('Getnombre').value;
        const especialidad = document.getElementById('Getespecialidad').value;
        const telefono = document.getElementById('Gettelefono').value;
        const correo = document.getElementById('Getcorreo').value;
        const salario = document.getElementById('Getsalario').value;
    
        const datosActualizados = {
            id_instructor: id_instructor,
            nombre: nombre,
            especialidad: especialidad,
            telefono: telefono,
            correo: correo,
            salario: salario
        };
    
        fetch('/backend/instructores.php', {
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
        
        document.getElementById('id_instructor').value = '';
        document.getElementById('Getnombre').value = '';
        document.getElementById('Getespecialidad').value = '';
        document.getElementById('Gettelefono').value = '';
        document.getElementById('Getcorreo').value = '';
        document.getElementById('Getsalario').value = '';

        limpiarBtn.classList.add('hidden');
        selectInstructor.selectedIndex = 0;

    });






        eliminarBtn.addEventListener('click', function () {
            const id_instructor = document.getElementById('id_instructor').value;

            if (id_instructor) {
                if (confirm('¿Estás seguro de que deseas eliminar este instructor?')) {
                    fetch('/backend/instructores.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_instructor: id_instructor })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                limpiarBtn.classList.add('hidden');
                                eliminarBtn.classList.add('hidden');
                                document.getElementById('id_instructor').value = '';
                                document.getElementById('Getnombre').value = '';
                                document.getElementById('Getespecialidad').value = '';
                                document.getElementById('Gettelefono').value = '';
                                document.getElementById('Getcorreo').value = '';
                                document.getElementById('Getsalario').value = '';

                                selectInstructor.selectedIndex = 0;
                                
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar el instructor:', error);
                            alert('Ocurrió un error al eliminar el instructor');
                        });
                }
            }
        });
    



});