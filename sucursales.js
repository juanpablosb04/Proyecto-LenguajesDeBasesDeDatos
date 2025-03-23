document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('registroForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Convertir FormData a un objeto para depuración
        const data = Object.fromEntries(formData.entries());
        console.log('Datos enviados:', data);

        fetch('/backend/sucursales.php', {
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
                this.reset(); // Limpiar el formulario
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
});