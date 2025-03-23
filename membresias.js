document.addEventListener('DOMContentLoaded', function () {
    const consultarBtn = document.getElementById('consultarBtn');
    const consultarCedulaInput = document.getElementById('consultarCedulaInput');
    const editarBtn = document.getElementById('editarBtn');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeletemembresiaInput = document.getElementById('DeletemembresiaInput');

    if (registrarMembresiaForm) {
        registrarMembresiaForm.addEventListener('submit', function (e) {
            e.preventDefault(); 

            const cedula = document.getElementById('cedula').value;
            const costoMensual = document.getElementById('costo_mensual').value;

            if (cedula && costoMensual) {
                const formData = new FormData();
                formData.append('cedula', cedula);
                formData.append('costo_mensual', costoMensual);

                console.log('Enviando datos al backend:', { cedula, costoMensual });

                fetch('/backend/membresias.php', {
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
                            registrarMembresiaForm.reset();
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error registrando la membresía:', error);
                        alert('Ocurrió un error al registrar la membresía');
                    });
            } else {
                alert('Por favor, complete todos los campos');
            }
        });
    } else {
        console.error('No se encontró el formulario de registrar membresía');
    }





    if (consultarBtn && consultarCedulaInput) {
        consultarBtn.addEventListener('click', function (event) {
            event.preventDefault();

            const cedula = consultarCedulaInput.value;

            if (cedula) {
                console.log('Realizando consulta para cédula:', cedula);

                fetch(`/backend/membresias.php?cedula=${cedula}`)
                    .then(response => {
                        console.log('Respuesta recibida:', response);
                        if (!response.ok) {
                            throw new Error('Error en la solicitud');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        if (data.error) {
                            alert(data.error);
                        } else {

                            document.getElementById('editarCostoMensual').value = data.COSTO_MENSUAL || '';
                            document.getElementById('editarEstado').value = data.ACTIVO || '';

                            editarBtn.classList.remove('hidden');
                            limpiarBtn.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error);
                        alert('Ocurrió un error al consultar los datos');
                    });
            } else {
                alert('Por favor, ingrese una cédula');
            }
        });
    } else {
        console.error('No se encontró el botón de consulta o el campo de cédula');
    }

    if (editarBtn) {
        editarBtn.addEventListener('click', function (event) {
            event.preventDefault();

            const cedula = consultarCedulaInput.value;
            const costoMensual = document.getElementById('editarCostoMensual').value;
            const estado = document.getElementById('editarEstado').value;

            const datosActualizados = {
                cedula: cedula,
                costo_mensual: costoMensual,
                estado: estado
            };

            fetch('/backend/membresias.php', {
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
    } else {
        console.error('No se encontró el botón de editar');
    }

    limpiarBtn.addEventListener('click', function () {
        
        document.getElementById('consultarCedulaInput').value = '';
        document.getElementById('editarCostoMensual').value = '';

        
        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');

        console.log('Formulario limpiado');
    });


    if (eliminarBtn && DeletemembresiaInput) {
        eliminarBtn.addEventListener('click', function () {
            const cedula = DeletemembresiaInput.value;

            if (cedula) {
                const datosMembresiaAEliminar = { cedula: cedula };

                fetch('/backend/membresias.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosMembresiaAEliminar)
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
                    console.error('Error al eliminar la membresía:', error);
                    alert('Ocurrió un error al eliminar la membresía');
                });
            } else {
                alert('Por favor, ingrese una cédula');
            }
        });
    } else {
        console.error('No se encontró el botón de eliminar o el campo de cédula');
    }
});