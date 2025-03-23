document.addEventListener('DOMContentLoaded', function () {
    const tablaProductos = document.getElementById('tablaProductos');
    const registroForm = document.getElementById('registroProductoForm');

    // Cargar lista de productos
    function cargarProductos() {
        console.log('Cargando productos...'); // Verificar que la función se ejecuta
        fetch('/backend/productos_tienda.php')
        .then(response => {
            console.log(response); // Verifica la respuesta cruda
            return response.json();
        })
        .then(data => {
                console.log(data); // Verificar los datos recibidos
                tablaProductos.innerHTML = '';
                data.forEach(producto => {
                    tablaProductos.innerHTML += `
                        <tr>
                            <td class="border p-2">${producto.ID_PRODUCTO}</td>
                            <td class="border p-2">${producto.NOMBRE_PRODUCTO}</td>
                            <td class="border p-2">$${producto.PRECIO.toFixed(2)}</td>
                            <td class="border p-2">${producto.STOCK}</td>
                            <td class="border p-2">${producto.TIPO_PRODUCTO}</td>
                            <td class="border p-2 text-center">
                                <button onclick="editarProducto(${producto.id_producto})" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600">✏️</button>
                                <button onclick="eliminarProducto(${producto.id_producto})" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600">❌</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error('Error cargando productos:', error));
    }

    cargarProductos();

    // Registrar un nuevo producto
    registroForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const producto = {
            nombre: document.getElementById('nombre_producto').value,
            precio: parseFloat(document.getElementById('precio').value),
            stock: parseInt(document.getElementById('stock').value),
            tipo: document.getElementById('tipo_producto').value
        };

        fetch('/backend/productos_tienda.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(producto)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarProductos();
            registroForm.reset();
        })
        .catch(error => console.error('Error registrando producto:', error));
    });

    // Eliminar un producto
    window.eliminarProducto = function (id_producto) {
        if (!confirm('¿Seguro que deseas eliminar este producto?')) return;

        fetch('/backend/productos_tienda.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_producto })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarProductos();
        })
        .catch(error => console.error('Error eliminando producto:', error));
    };

    // Editar un producto (a futuro podemos hacer un modal)
    window.editarProducto = function (id_producto) {
        const producto = prompt("Ingrese el nuevo nombre del producto:");
        if (!producto) return;

        const precio = prompt("Ingrese el nuevo precio:");
        if (!precio || isNaN(precio)) return;

        const stock = prompt("Ingrese el nuevo stock:");
        if (!stock || isNaN(stock)) return;

        const tipo = prompt("Ingrese el nuevo tipo:");
        if (!tipo) return;

        const datosActualizados = { id_producto, nombre: producto, precio: parseFloat(precio), stock: parseInt(stock), tipo };

        fetch('/backend/productos_tienda.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosActualizados)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            cargarProductos();
        })
        .catch(error => console.error('Error actualizando producto:', error));
    };
});
