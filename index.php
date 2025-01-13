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
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    if ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o contraseña inválidos.";
    }
}

$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Function to retrieve images from a folder
function getImages($folder) {
    $images = [];
    if (is_dir($folder)) {
        $files = scandir($folder);
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                $images[] = $file;
            }
        }
    }
    return $images;
}

// Define chart folders
$chartFolders = [
    'hourly' => 'img/hourly',
    'minute' => 'img/minute',
    'second' => 'img/second',
];

// Get selected chart type
$selectedType = isset($_GET['type']) && isset($chartFolders[$_GET['type']]) ? $_GET['type'] : 'hourly';
$images = $loggedIn ? getImages($chartFolders[$selectedType]) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jocarsa | Tomato</title>
    <link rel="icon" href="https://jocarsa.com/static/logo/jocarsa%20%7c%20Tomato.svg" type="image/svg+xml">
    <style>
        /* General Styles */
        body {
            font-family: Ubuntu, Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
        }
        .header {
            background-color: tomato;
            width: 100%;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
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
        .sidebar {
            width: 250px;
            background: #f4f4f4;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin: 10px 0;
            color: tomato;
            text-decoration: none;
            border-radius: 5px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: tomato;
            color: white;
        }
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            margin-top: 70px;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .image-card {
            text-align: center;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .image-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php if ($loggedIn): ?>
        <!-- Header -->
        <div class="header">
            <div class="app-name">jocarsa | tomato</div>
            <a href="index.php?action=logout" class="logout-button">Cerrar Sesión</a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Gráficas</h3>
            <a href="index.php?type=hourly" class="<?php echo $selectedType === 'hourly' ? 'active' : ''; ?>">Última Hora</a>
            <a href="index.php?type=minute" class="<?php echo $selectedType === 'minute' ? 'active' : ''; ?>">Últimos Minutos</a>
            <a href="index.php?type=second" class="<?php echo $selectedType === 'second' ? 'active' : ''; ?>">Últimos Segundos</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="dashboard">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <img src="<?php echo $chartFolders[$selectedType] . '/' . htmlspecialchars($image); ?>" alt="Chart">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay gráficas disponibles en la carpeta seleccionada.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Entrar</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>

