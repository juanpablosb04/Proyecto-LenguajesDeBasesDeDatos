document.addEventListener('DOMContentLoaded', function () {
    const consultarBtn = document.getElementById('consultarBtn');
    const GetIDInput = document.getElementById('Getid_venta');
    const editarBtn = document.getElementById('editarBtn');
    const limpiarBtn = document.getElementById('limpiarBtn');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeleteIDInput = document.getElementById('DeleteIDInput');
    const calcularBtn = document.getElementById('calcularBtn');
    


document.getElementById('registroForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    const data = Object.fromEntries(formData.entries());
    console.log('Datos enviados:', data);

    fetch('/backend/ventas_tienda.php', {
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
        const id_venta = GetIDInput.value;

        if (id_venta) {
            console.log('Realizando consulta para ID de la venta:', id_venta);

            fetch(`/backend/ventas_tienda.php?id_venta=${id_venta}`)
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
                        document.getElementById('Getcedula').value = data.CEDULA || '';
                        document.getElementById('Getid_producto').value = data.ID_PRODUCTO || '';
                        document.getElementById('Getcantidad').value = data.CANTIDAD || '';
                        document.getElementById('Gettotal').value = data.TOTAL || '';
                        document.getElementById('Getfecha_venta').value = data.FECHA_VENTA || '';


                        document.getElementById('Getcedula').removeAttribute('disabled');
                        document.getElementById('Getcantidad').removeAttribute('disabled');
                        document.getElementById('Gettotal').removeAttribute('disabled');

                        document.getElementById('Getid_venta').setAttribute('disabled', true);

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
    const id_venta = document.getElementById('Getid_venta').value;
    const cedula = document.getElementById('Getcedula').value;
    const id_producto = document.getElementById('Getid_producto').value;
    const cantidad = document.getElementById('Getcantidad').value;
    const total = document.getElementById('Gettotal').value;
    const fecha_venta = document.getElementById('Getfecha_venta').value;

    const datosActualizados = {
        id_venta: id_venta,
        cedula: cedula,
        id_producto: id_producto,
        cantidad: cantidad,
        total: total,
        fecha_venta: fecha_venta
    };

    fetch('/backend/ventas_tienda.php', {
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
    document.getElementById('Getid_venta').value = '';
    document.getElementById('Getcedula').value = '';
    document.getElementById('Getid_producto').value = '';
    document.getElementById('Getcantidad').value = '';
    document.getElementById('Gettotal').value = '';
    document.getElementById('Getfecha_venta').value = '';

    document.getElementById('Getcedula').setAttribute('disabled', true);
    document.getElementById('Getid_producto').setAttribute('disabled', true);
    document.getElementById('Getcantidad').setAttribute('disabled', true);
    document.getElementById('Gettotal').setAttribute('disabled', true);
    document.getElementById('Getfecha_venta').setAttribute('disabled', true);

    document.getElementById('Getid_venta').removeAttribute('disabled');

    editarBtn.classList.add('hidden');
    limpiarBtn.classList.add('hidden');

    console.log('Formulario limpiado');
});

if (eliminarBtn && DeleteIDInput) {
    eliminarBtn.addEventListener('click', function() {
        const idVenta = DeleteIDInput.value;

        if (idVenta) {
            if (confirm('¿Estás seguro de que deseas eliminar esta venta?')) {
                fetch('/backend/ventas_tienda.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_venta: idVenta })
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
                    console.error('Error al eliminar la venta:', error);
                    alert('Ocurrió un error al eliminar la venta');
                });
            }
        } else {
            alert('Por favor, ingrese un ID de venta válido');
        }
    });
} else {
    console.error('No se encontró el campo de ID de venta o el botón de eliminar');
}




if (calcularBtn) {
    calcularBtn.addEventListener('click', function () {
        const idProductoInput = document.getElementById('id_producto');
        const cantidadInput = document.getElementById('cantidad');

        const id_producto = idProductoInput.value;
        const cantidad = cantidadInput.value;

        if (id_producto && cantidad) {
            fetch(`/backend/ventas_tienda.php?id_producto=${id_producto}&cantidad=${cantidad}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Total a pagar:', data);
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.getElementById('total').value = parseFloat(data.total);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud fetch:', error);
                    alert('Ocurrió un error al calcular el total');
                });

        } else {
            alert('Por favor, ingrese el ID del producto y la cantidad');
        }
    });
} else {
    console.error('No se encontró el botón calcular');
}

});