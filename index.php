<?php
// Start the session at the beginning of the script
session_start();

// Define valid credentials
$VALID_USERNAME = 'jocarsa';
$VALID_PASSWORD = 'jocarsa'; 

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_unset();
    session_destroy();
    // Redirect to the login form
    header("Location: index.php");
    exit;
}

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Retrieve and sanitize input
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate credentials
    if ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
        // Credentials are valid, set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Redirect to the dashboard
        header("Location: index.php");
        exit;
    } else {
        // Invalid credentials, set error message
        $error = "Usuario o contraseña inválidos.";
    }
}

// Check if the user is logged in
$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jocarsa | tomato</title>
    <link rel="icon" href="https://jocarsa.com/static/logo/jocarsa%20%7c%20Tomato.svg" type="image/svg+xml">
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
        }

        /* Login Form Styles */
        .login-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: tomato;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0 16px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            background-color: tomato;
            color: white;
            padding: 14px 20px;
            margin: 8px 0 0 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: darkred;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Dashboard Styles */
        .dashboard-title {
            font-size: 2rem;
            font-weight: bold;
            color: tomato;
            margin-bottom: 20px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
        }

        .panel {
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid tomato;
            cursor: pointer; /* Indicates clickable */
            transition: transform 0.2s; /* Hover effect */
        }

        .panel:hover {
            transform: scale(1.05); /* Hover effect */
        }

        .title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: tomato;
        }

        .image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Logout Button */
        .logout-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: tomato;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }

        .logout-button:hover {
            background-color: darkred;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8); /* Semi-transparent background */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }

        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .close {
            position: absolute;
            top: -10px;
            right: -10px;
            color: white;
            background-color: tomato;
            border: none;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <?php if ($loggedIn): ?>
        <!-- Logout Button -->
        <a href="index.php?action=logout" class="logout-button">Cerrar Sesión</a>

        <!-- Dashboard Content -->
        <h1 class="dashboard-title">Panel de Control del Servidor</h1>
        <div class="dashboard">
            <div class="panel" data-image="carga_cpu.jpg">
                <h1 class="title">CPU</h1>
                <img src="carga_cpu.jpg" alt="Gráfica de CPU" class="image">
            </div>
            <div class="panel" data-image="carga_ram.jpg">
                <h1 class="title">RAM</h1>
                <img src="carga_ram.jpg" alt="Gráfica de RAM" class="image">
            </div>
            <div class="panel" data-image="carga_disco.jpg">
                <h1 class="title">Disco</h1>
                <img src="carga_disco.jpg" alt="Gráfica de Disco" class="image">
            </div>
            <div class="panel" data-image="carga_red.jpg">
                <h1 class="title">Red</h1>
                <img src="carga_red.jpg" alt="Gráfica de Red" class="image">
            </div>
            <div class="panel" data-image="carga_conexiones.jpg">
                <h1 class="title">Conexiones</h1>
                <img src="carga_conexiones.jpg" alt="Gráfica de Conexiones" class="image">
            </div>
            <div class="panel" data-image="carga_temperatura.jpg">
                <h1 class="title">Temperatura</h1>
                <img src="carga_temperatura.jpg" alt="Gráfica de Temperatura" class="image">
            </div>
        </div>

        <!-- Modal -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <button class="close">&times;</button>
                <img src="" alt="Imagen Ampliada" id="modalImage">
            </div>
        </div>

        <script>
            // Get DOM elements
            const panels = document.querySelectorAll('.panel');
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const closeButton = document.querySelector('.close');

            // Function to open the modal with the clicked image
            panels.forEach(panel => {
                panel.addEventListener('click', () => {
                    const imgSrc = panel.getAttribute('data-image');
                    modalImage.src = imgSrc;
                    modal.style.display = 'flex';
                });
            });

            // Function to close the modal
            const closeModal = () => {
                modal.style.display = 'none';
                modalImage.src = '';
            };

            // Close when clicking the close button
            closeButton.addEventListener('click', closeModal);

            // Close when clicking outside the image
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Close when pressing the Esc key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        </script>
    <?php else: ?>
        <!-- Login Form -->
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <button type="submit" name="login">Entrar</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>

