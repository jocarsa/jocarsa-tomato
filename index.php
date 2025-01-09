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

// Function to retrieve images from the 'img' directory
function getImages($dir = 'img/') {
    $images = [];
    if (is_dir($dir)) {
        // Scan the directory for image files
        $files = scandir($dir);
        foreach ($files as $file) {
            // Check for valid image extensions
            if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                $images[] = $file;
            }
        }
    }
    return $images;
}

// Retrieve images
$images = $loggedIn ? getImages() : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jocarsa | Tomato</title>
    <link rel="icon" href="https://jocarsa.com/static/logo/jocarsa%20%7c%20Tomato.svg" type="image/svg+xml">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family:Ubuntu, Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Header Styles */
        .header {
            background-color: tomato;
            width: 100%;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header .app-name {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .header .logout-button {
            background-color: tomato;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }

        .header .logout-button:hover {
            background-color: darkred;
        }

        /* Adjust body padding to prevent content being hidden behind the fixed header */
        .content {
            padding-top: 70px; /* Height of the header + some spacing */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Login Form Styles */
        .login-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin:auto;
            margin-top: 50px; /* Adjusted for header */
            text-align:center;
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
            margin-bottom: 40px;
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
            text-transform: capitalize; /* Capitalize titles */
        }

        .image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        .app-name{
        	display: flex;
			flex-direction: row;
			flex-wrap: nowrap;
			justify-content: center;
			align-items: center;
			align-content: stretch;
        }
        .app-name img{
        	width:50px;
        }
    </style>
</head>
<body>
    <?php if ($loggedIn): ?>
        <!-- Header -->
        <div class="header">
            <div class="app-name"><img src="https://jocarsa.com/static/logo/jocarsa | White.svg"> jocarsa | tomato</div>
            <a href="index.php?action=logout" class="logout-button">Cerrar Sesión</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Dashboard Content -->
            <h1 class="dashboard-title"></h1>
            <div class="dashboard">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                        <?php
                            // Extract the title from the image filename
                            $title = pathinfo($image, PATHINFO_FILENAME);
                            // Replace underscores or dashes with spaces and capitalize words
                            $title = ucwords(str_replace(['_', '-'], ' ', $title));
                        ?>
                        <div class="panel" data-image="<?php echo 'img/' . htmlspecialchars($image); ?>">
                            <h1 class="title"><?php echo htmlspecialchars($title); ?></h1>
                            <img src="<?php echo 'img/' . htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="image">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay imágenes disponibles en la carpeta 'img'.</p>
                <?php endif; ?>
            </div>

            <!-- Modal -->
            <div id="imageModal" class="modal">
                <div class="modal-content">
                    <button class="close">&times;</button>
                    <img src="" alt="Imagen Ampliada" id="modalImage">
                </div>
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
        		<img src="https://jocarsa.com/static/logo/jocarsa | Tomato.svg">
        		<h1>jocarsa | tomato</h1>
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

