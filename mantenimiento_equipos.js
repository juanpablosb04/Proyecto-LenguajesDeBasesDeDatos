document.addEventListener('DOMContentLoaded', function () {

    const consultarBtn = document.getElementById('consultarBtn');
    const GetIDInput = document.getElementById('Getid_mantenimiento');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeleteIDInput = document.getElementById('DeleteIDInput');
    const editarBtn = document.getElementById('editarBtn');
    const limpiarBtn = document.getElementById('limpiarBtn');

    document.getElementById('registroForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        const data = Object.fromEntries(formData.entries());
        console.log('Datos enviados:', data);

        fetch('/backend/mantenimiento_equipos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.reset();
                alert(data.success);
            } else {
                alert(data.error || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
        });
    });


    if (consultarBtn && GetIDInput) {
        consultarBtn.addEventListener('click', function () {
            const id_mantenimiento = GetIDInput.value;

            if (id_mantenimiento) {
                console.log('Realizando consulta para ID del mantenimiento:', id_mantenimiento);

                fetch(`/backend/mantenimiento_equipos.php?id_mantenimiento=${id_mantenimiento}`)
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
                            document.getElementById('Getid_equipo').value = data.ID_EQUIPO || '';
                            document.getElementById('Getfecha_mantenimiento').value = data.FECHA_MANTENIMIENTO || '';
                            document.getElementById('Getdescripcion').value = data.DESCRIPCION || '';
                            document.getElementById('Getestado').value = data.ESTADO || '';

                            document.getElementById('Getid_equipo').removeAttribute('disabled');
                            document.getElementById('Getfecha_mantenimiento').removeAttribute('disabled');
                            document.getElementById('Getdescripcion').removeAttribute('disabled');
                            document.getElementById('Getestado').removeAttribute('disabled');

                            document.getElementById('Getid_mantenimiento').setAttribute('disabled', true);

                            editarBtn.classList.remove('hidden');
                            limpiarBtn.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error);
                        alert('Ocurrió un error al consultar los datos');
                    });
            } else {
                alert('Por favor, ingrese un ID de un mantenimiento realizado');
            }
        });
    } else {
        console.error('No se encontró el botón de consulta o el campo de ID');
    }

    editarBtn.addEventListener('click', function () {
        const id_mantenimiento = document.getElementById('Getid_mantenimiento').value;
        const id_equipo = document.getElementById('Getid_equipo').value;
        const fecha_mantenimiento = document.getElementById('Getfecha_mantenimiento').value;
        const descripcion = document.getElementById('Getdescripcion').value;
        const estado = document.getElementById('Getestado').value;
        

        const datosActualizados = {
            id_mantenimiento: id_mantenimiento,
            id_equipo: id_equipo,
            fecha_mantenimiento: fecha_mantenimiento,
            descripcion: descripcion,
            estado: estado
        };

        fetch('/backend/mantenimiento_equipos.php', {
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
        document.getElementById('Getid_mantenimiento').value = '';
        document.getElementById('Getid_equipo').value = '';
        document.getElementById('Getfecha_mantenimiento').value = '';
        document.getElementById('Getdescripcion').value = '';
        document.getElementById('Getestado').value = '';

        document.getElementById('Getid_equipo').setAttribute('disabled', true);
        document.getElementById('Getfecha_mantenimiento').setAttribute('disabled', true);
        document.getElementById('Getdescripcion').setAttribute('disabled', true);
        document.getElementById('Getestado').setAttribute('disabled', true);
        

        document.getElementById('Getid_mantenimiento').removeAttribute('disabled');

        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');

        console.log('Formulario limpiado');
    });

    if (eliminarBtn && DeleteIDInput) {
        eliminarBtn.addEventListener('click', function () {
            const id_mantenimiento = DeleteIDInput.value;

            if (id_mantenimiento) {
                if (confirm('¿Estás seguro de que deseas eliminar este mantenimiento?')) {
                    fetch('/backend/mantenimiento_equipos.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_mantenimiento: id_mantenimiento })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                DeleteIDInput.value = '';
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar el mantenimiento:', error);
                            alert('Ocurrió un error al eliminar el mantenimiento');
                        });
                }
            } else {
                alert('Por favor, ingrese un ID de mantenimiento');
            }
        });
    } else {
        console.error('No se encontró el campo ID del mantenimiento');
    }
    
});