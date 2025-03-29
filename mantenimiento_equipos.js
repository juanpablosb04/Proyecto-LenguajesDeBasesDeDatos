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
    
});