document.addEventListener('DOMContentLoaded', function () {
    const consultarBtn = document.getElementById('consultarBtn');
    const GetIDInput = document.getElementById('Getid_producto');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeleteIDInput = document.getElementById('DeleteIDInput');
    
    document.getElementById('registroForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/backend/productos_tienda.php', {
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
            cargarProductos();
        })
        .catch(error => {
            alert('Ocurrió un error al procesar la solicitud');
        });
    });


    function cargarProductos() {
        fetch('/backend/productos_tienda.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                const tablaProductos = document.getElementById('tablaProductos');
                tablaProductos.innerHTML = '';

                if (Array.isArray(data)) {

                    data.forEach(producto => {
                        const fila = document.createElement('tr');
                        fila.classList.add('hover:bg-gray-100', 'transition');
    
                        const celdaId = document.createElement('td');
                        celdaId.textContent = producto.ID_PRODUCTO;
                        celdaId.classList.add('border', 'p-4');
                        fila.appendChild(celdaId);
    
                        const celdaNombre = document.createElement('td');
                        celdaNombre.textContent = producto.NOMBRE_PRODUCTO;
                        celdaNombre.classList.add('border', 'p-4');
                        fila.appendChild(celdaNombre);
    
                        const celdaPrecio = document.createElement('td');
                        celdaPrecio.textContent = `₡${parseFloat(producto.PRECIO).toFixed(2)}`;
                        celdaPrecio.classList.add('border', 'p-4');
                        fila.appendChild(celdaPrecio);
    
                        const celdaStock = document.createElement('td');
                        celdaStock.textContent = producto.STOCK;
                        celdaStock.classList.add('border', 'p-4');
                        fila.appendChild(celdaStock);
    
                        const celdaTipo = document.createElement('td');
                        celdaTipo.textContent = producto.TIPO_PRODUCTO;
                        celdaTipo.classList.add('border', 'p-4');
                        fila.appendChild(celdaTipo);
    
                        const celdaAcciones = document.createElement('td');
                        celdaAcciones.classList.add('border', 'p-4', 'text-center');
    
                        fila.appendChild(celdaAcciones);
    
                        tablaProductos.appendChild(fila);
                    });
                } else {
                    console.error('La respuesta no es un array:', data);
                    tablaProductos.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error: Datos no válidos</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error cargando productos:', error);
                const tablaProductos = document.getElementById('tablaProductos');
                tablaProductos.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error al cargar los productos</td></tr>';
            });
    }
    
    cargarProductos();



    if (consultarBtn && GetIDInput) {
        consultarBtn.addEventListener('click', function () {
            const id_producto = GetIDInput.value;

            if (id_producto) {
                console.log('Realizando consulta para ID del producto:', id_producto);

                fetch(`/backend/productos_tienda.php?id_producto=${id_producto}`)
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
                        } else{
                            
                            document.getElementById('Getnombre_producto').value = data.NOMBRE_PRODUCTO || '';
                            document.getElementById('Getprecio').value = data.PRECIO || '';
                            document.getElementById('Getstock').value = data.STOCK || '';
                            document.getElementById('Gettipo_producto').value = data.TIPO_PRODUCTO || '';

                            document.getElementById('Getnombre_producto').removeAttribute('disabled');
                            document.getElementById('Getprecio').removeAttribute('disabled');
                            document.getElementById('Getstock').removeAttribute('disabled');
                            document.getElementById('Gettipo_producto').removeAttribute('disabled');

                            document.getElementById('Getid_producto').setAttribute('disabled', true);

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


    editarBtn.addEventListener('click', function () {
        const id_producto = document.getElementById('Getid_producto').value;
        const nombre_producto = document.getElementById('Getnombre_producto').value;
        const precio = document.getElementById('Getprecio').value;
        const stock = document.getElementById('Getstock').value;
        const tipo_producto = document.getElementById('Gettipo_producto').value;

        const datosActualizados = {
            id_producto: id_producto,
            nombre_producto: nombre_producto,
            precio: precio,
            stock: stock,
            tipo_producto: tipo_producto
        };

        fetch('/backend/productos_tienda.php', {
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
                cargarProductos();
            })
            .catch(error => {
                console.error('Error al actualizar los datos:', error);
                alert('Ocurrió un error al actualizar los datos');
            });
    });




    limpiarBtn.addEventListener('click', function () {
        
        document.getElementById('Getid_producto').value = '';
        document.getElementById('Getnombre_producto').value = '';
        document.getElementById('Getprecio').value = '';
        document.getElementById('Getstock').value = '';
        document.getElementById('Gettipo_producto').value = '';    
        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');
        document.getElementById('Getnombre_producto').setAttribute('disabled', true);
        document.getElementById('Getprecio').setAttribute('disabled', true);
        document.getElementById('Getstock').setAttribute('disabled', true);
        document.getElementById('Gettipo_producto').setAttribute('disabled', true);
        document.getElementById('Getid_producto').removeAttribute('disabled');


        console.log('Formulario limpiado');
    });



    if (eliminarBtn && DeleteIDInput) {
        eliminarBtn.addEventListener('click', function () {
            const id_producto = DeleteIDInput.value;

            if (id_producto) {
                if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                    fetch('/backend/productos_tienda.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id_producto: id_producto })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                DeleteIDInput.value = '';
                            } else {
                                alert(data.error);
                            }
                            cargarProductos();
                        })
                        .catch(error => {
                            console.error('Error al eliminar el producto:', error);
                            alert('Ocurrió un error al eliminar el producto');
                        });
                }
            } else {
                alert('Por favor, ingrese un producto');
            }
        });
    } else {
        console.error('No se encontró el campo id del producto');
    }

});
