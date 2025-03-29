document.addEventListener('DOMContentLoaded', function () {

    const consultarBtn = document.getElementById('consultarBtn');
    const GetIDInput = document.getElementById('Getid_pago');
    const editarBtn = document.getElementById('editarBtn');
    const limpiarBtn = document.getElementById('limpiarBtn');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeleteIDInput = document.getElementById('DeleteIDInput');

    document.getElementById('registroForm').addEventListener('submit', function (e) {
        e.preventDefault();
    
        const formData = new FormData(this);
    
        const data = Object.fromEntries(formData.entries());
        console.log('Datos enviados:', data);
    
        fetch('/backend/pagos.php', {
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
            const id_pago = GetIDInput.value;
    
            if (id_pago) {
                console.log('Realizando consulta para ID de la venta:', id_pago);
    
                fetch(`/backend/pagos.php?id_pago=${id_pago}`)
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
                            document.getElementById('Getid_miembro').value = data.ID_MIEMBRO || '';
                            document.getElementById('Getfecha_pago').value = data.FECHA_PAGO || '';
                            document.getElementById('Getmonto').value = data.MONTO || '';
                            document.getElementById('Getmetodo_pago').value = data.METODO_PAGO || '';
    
                            document.getElementById('Getid_miembro').removeAttribute('disabled');
                            document.getElementById('Getmonto').removeAttribute('disabled');
                            document.getElementById('Getmetodo_pago').removeAttribute('disabled');
    
                            document.getElementById('Getid_pago').setAttribute('disabled', true);
    
                            editarBtn.classList.remove('hidden');
                            limpiarBtn.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error);
                        alert('Ocurrió un error al consultar los datos');
                    });
            } else {
                alert('Por favor, ingrese un ID de venta');
            }
        });
    } else {
        console.error('No se encontró el botón de consulta o el campo de ID');
    }
    
    editarBtn.addEventListener('click', function () {
        const id_pago = document.getElementById('Getid_pago').value;
        const id_miembro = document.getElementById('Getid_miembro').value;
        const fecha_pago = document.getElementById('Getfecha_pago').value;
        const monto = document.getElementById('Getmonto').value;
        const metodo_pago = document.getElementById('Getmetodo_pago').value;
    
        const datosActualizados = {
            id_pago: id_pago,
            id_miembro: id_miembro,
            fecha_pago: fecha_pago,
            monto: monto,
            metodo_pago: metodo_pago
        };
    
        fetch('/backend/pagos.php', {
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
        document.getElementById('Getid_pago').value = '';
        document.getElementById('Getid_miembro').value = '';
        document.getElementById('Getfecha_pago').value = '';
        document.getElementById('Getmonto').value = '';
        document.getElementById('Getmetodo_pago').value = '';
    
        document.getElementById('Getid_miembro').setAttribute('disabled', true);
        document.getElementById('Getmonto').setAttribute('disabled', true);
        document.getElementById('Getmetodo_pago').setAttribute('disabled', true);
    
        document.getElementById('Getid_pago').removeAttribute('disabled');
    
        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');
    
        console.log('Formulario limpiado');
    });

    if (eliminarBtn && DeleteIDInput) {
        eliminarBtn.addEventListener('click', function() {
            const id_pago = DeleteIDInput.value;
    
            if (id_pago) {
                if (confirm('¿Estás seguro de que deseas eliminar esta venta?')) {
                    fetch('/backend/pagos.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_pago: id_pago })
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
                        console.error('Error al eliminar el pago:', error);
                        alert('Ocurrió un error al eliminar el pago');
                    });
                }
            } else {
                alert('Por favor, ingrese un ID de pago válido');
            }
        });
    } else {
        console.error('No se encontró el campo de ID de pago o el botón de eliminar');
    }

});