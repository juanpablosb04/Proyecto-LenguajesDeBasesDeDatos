document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('loginForm');
    const loginError = document.getElementById('login-error');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!email || !password) {
            loginError.style.display = 'block';
            loginError.textContent = 'Por favor, llena todos los campos.';
            return;
        }

        try {
            
            const response = await fetch('backend/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ email, password }),
            });

            const result = await response.json();

            if (response.ok) {
                
                localStorage.setItem('userLoggedIn', 'true');
                window.location.href ='inicio.html';
            } else {
                loginError.style.display = 'block';
                loginError.textContent = result.error || 'Usuario o contraseña incorrectos.';
            }
        } catch (error) {
            console.error(error);
            loginError.style.display = 'block';
            loginError.textContent = 'Hubo un error al procesar tu solicitud.';
        }
    });




    const logoutButtons = document.getElementById('logout-buttons');
    const authButtons = document.getElementById('auth-buttons');

    if (logoutButtons && authButtons) {
       
        if (localStorage.getItem('userLoggedIn') === 'true') {
            logoutButtons.style.display = 'block';
            authButtons.querySelectorAll('.btn-outline-light, .btn-register').forEach(button => {
                button.style.display = 'none';
            });
        } else {
            logoutButtons.style.display = 'none';
            authButtons.querySelectorAll('.btn-outline-light, .btn-register').forEach(button => {
                button.style.display = 'inline-block';
            });
        }
    }

});