document.addEventListener('DOMContentLoaded', function () {

    const consultarBtn = document.getElementById('consultarBtn');
    const GetIDInput = document.getElementById('Getid_equipo');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeleteIDInput = document.getElementById('DeleteIDInput');
    const editarBtn = document.getElementById('editarBtn');
    const limpiarBtn = document.getElementById('limpiarBtn');

    function cargarEquipos() {
        fetch('/backend/equipos_S2.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Equipos recibidos:', data);
                const tablaEquipos = document.getElementById('tablaEquipos');
                tablaEquipos.innerHTML = '';

                if (Array.isArray(data)) {
                    data.forEach(equipo => {
                        const fila = document.createElement('tr');
                        fila.classList.add('hover:bg-gray-100', 'transition');

                        const celdaId = document.createElement('td');
                        celdaId.textContent = equipo.ID_EQUIPO;
                        celdaId.classList.add('border', 'p-4');
                        fila.appendChild(celdaId);

                        const celdaNombre = document.createElement('td');
                        celdaNombre.textContent = equipo.NOMBRE;
                        celdaNombre.classList.add('border', 'p-4');
                        fila.appendChild(celdaNombre);

                        const celdaTipo = document.createElement('td');
                        celdaTipo.textContent = equipo.TIPO;
                        celdaTipo.classList.add('border', 'p-4');
                        fila.appendChild(celdaTipo);

                        const celdaEstado = document.createElement('td');
                        celdaEstado.textContent = equipo.ESTADO;
                        celdaEstado.classList.add('border', 'p-4');
                        fila.appendChild(celdaEstado);

                        const celdaFechaCompra = document.createElement('td');
                        celdaFechaCompra.textContent = equipo.FECHA_COMPRA;
                        celdaFechaCompra.classList.add('border', 'p-4');
                        fila.appendChild(celdaFechaCompra);

                        const celdaIdGimnasio = document.createElement('td');
                        celdaIdGimnasio.textContent = equipo.ID_GIMNASIO;
                        celdaIdGimnasio.classList.add('border', 'p-4');
                        fila.appendChild(celdaIdGimnasio);

                        tablaEquipos.appendChild(fila);
                    });
                } else {
                    console.error('La respuesta no es un array:', data);
                    tablaEquipos.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error: Datos no válidos</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error cargando equipos:', error);
                const tablaEquipos = document.getElementById('tablaEquipos');
                tablaEquipos.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error al cargar los equipos</td></tr>';
            });
    }

    cargarEquipos();

    if (consultarBtn && GetIDInput) {
        consultarBtn.addEventListener('click', function () {
            const id_equipo = GetIDInput.value;

            if (id_equipo) {
                console.log('Realizando consulta para ID del equipo:', id_equipo);

                fetch(`/backend/equipos_S2.php?id_equipo=${id_equipo}`)
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
                            document.getElementById('Getnombre').value = data.NOMBRE || '';
                            document.getElementById('Gettipo').value = data.TIPO || '';
                            document.getElementById('Getestado').value = data.ESTADO || '';
                            document.getElementById('Getfecha_compra').value = data.FECHA_COMPRA || '';
                            document.getElementById('Getid_gimnasio').value = data.ID_GIMNASIO || '';

                            document.getElementById('Getnombre').removeAttribute('disabled');
                            document.getElementById('Gettipo').removeAttribute('disabled');
                            document.getElementById('Getestado').removeAttribute('disabled');
                            document.getElementById('Getid_gimnasio').removeAttribute('disabled');

                            document.getElementById('Getid_equipo').setAttribute('disabled', true);

                            editarBtn.classList.remove('hidden');
                            limpiarBtn.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error);
                        alert('Ocurrió un error al consultar los datos');
                    });
            } else {
                alert('Por favor, ingrese un ID de equipo');
            }
        });
    } else {
        console.error('No se encontró el botón de consulta o el campo de ID');
    }

    editarBtn.addEventListener('click', function () {
        const id_equipo = document.getElementById('Getid_equipo').value;
        const nombre = document.getElementById('Getnombre').value;
        const tipo = document.getElementById('Gettipo').value;
        const estado = document.getElementById('Getestado').value;
        const fecha_compra = document.getElementById('Getfecha_compra').value;
        const id_gimnasio = document.getElementById('Getid_gimnasio').value;

        const datosActualizados = {
            id_equipo: id_equipo,
            nombre: nombre,
            tipo: tipo,
            estado: estado,
            fecha_compra: fecha_compra,
            id_gimnasio: id_gimnasio
        };

        fetch('/backend/equipos_S2.php', {
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
                cargarEquipos();
            })
            .catch(error => {
                console.error('Error al actualizar los datos:', error);
                alert('Ocurrió un error al actualizar los datos');
            });
    });

    limpiarBtn.addEventListener('click', function () {
        document.getElementById('Getid_equipo').value = '';
        document.getElementById('Getnombre').value = '';
        document.getElementById('Gettipo').value = '';
        document.getElementById('Getestado').value = '';
        document.getElementById('Getfecha_compra').value = '';
        document.getElementById('Getid_gimnasio').value = '';

        document.getElementById('Getnombre').setAttribute('disabled', true);
        document.getElementById('Gettipo').setAttribute('disabled', true);
        document.getElementById('Getestado').setAttribute('disabled', true);
        document.getElementById('Getfecha_compra').setAttribute('disabled', true);
        document.getElementById('Getid_gimnasio').setAttribute('disabled', true);

        document.getElementById('Getid_equipo').removeAttribute('disabled');

        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');

        console.log('Formulario limpiado');
    });

    if (eliminarBtn && DeleteIDInput) {
        eliminarBtn.addEventListener('click', function () {
            const id_equipo = DeleteIDInput.value;

            if (id_equipo) {
                if (confirm('¿Estás seguro de que deseas eliminar este equipo?')) {
                    fetch('/backend/equipos_S2.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_equipo: id_equipo })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                DeleteIDInput.value = '';
                            } else {
                                alert(data.error);
                            }
                            cargarEquipos();
                        })
                        .catch(error => {
                            console.error('Error al eliminar el equipo:', error);
                            alert('Ocurrió un error al eliminar el equipo');
                        });
                }
            } else {
                alert('Por favor, ingrese un ID de equipo');
            }
        });
    } else {
        console.error('No se encontró el campo ID del equipo');
    }
});