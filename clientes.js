document.addEventListener('DOMContentLoaded', function () {
    const consultarBtn = document.getElementById('consultarBtn');
    const GetcedulaInput = document.getElementById('Getcedula');
    const eliminarBtn = document.getElementById('eliminarBtn');
    const DeletecedulaInput = document.getElementById('DeletecedulaInput');

    if (consultarBtn && GetcedulaInput) {
        consultarBtn.addEventListener('click', function () {
            const cedula = GetcedulaInput.value;

            if (cedula) {
                console.log('Realizando consulta para cédula:', cedula);

                fetch(`/backend/clientes.php?cedula=${cedula}`)
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
                            document.getElementById('Getapellido').value = data.APELLIDO || '';
                            document.getElementById('Getcorreo').value = data.CORREO || '';
                            document.getElementById('Gettelefono').value = data.TELEFONO || '';
                            document.getElementById('Getdireccion').value = data.DIRECCION || '';
                            document.getElementById('Getfecha_registro').value = data.FECHA_REGISTRO || '';

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

    document.getElementById('registroForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/backend/clientes.php', {
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

    editarBtn.addEventListener('click', function () {
        const cedula = GetcedulaInput.value;
        const nombre = document.getElementById('Getnombre').value;
        const apellido = document.getElementById('Getapellido').value;
        const correo = document.getElementById('Getcorreo').value;
        const telefono = document.getElementById('Gettelefono').value;
        const direccion = document.getElementById('Getdireccion').value;
        const fecha_registro = document.getElementById('Getfecha_registro').value;

        const datosActualizados = {
            cedula: cedula,
            nombre: nombre,
            apellido: apellido,
            correo: correo,
            telefono: telefono,
            direccion: direccion,
            fecha_registro: fecha_registro
        };

        fetch('/backend/clientes.php', {
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
        
        document.getElementById('Getcedula').value = '';
        document.getElementById('Getnombre').value = '';
        document.getElementById('Getapellido').value = '';
        document.getElementById('Getcorreo').value = '';
        document.getElementById('Gettelefono').value = '';
        document.getElementById('Getdireccion').value = '';
        document.getElementById('Getfecha_registro').value = '';

        
        editarBtn.classList.add('hidden');
        limpiarBtn.classList.add('hidden');

        console.log('Formulario limpiado');
    });


    if (eliminarBtn && DeletecedulaInput) {
        eliminarBtn.addEventListener('click', function () {
            const cedula = DeletecedulaInput.value;

            if (cedula) {
                if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                    fetch('/backend/clientes.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ cedula: cedula })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                                
                                DeletecedulaInput.value = '';
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar el usuario:', error);
                            alert('Ocurrió un error al eliminar el usuario');
                        });
                }
            } else {
                alert('Por favor, ingrese una cédula');
            }
        });
    } else {
        console.error('No se encontró el campo de cédula');
    }
});