<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$port = 3306;
$user = "root";
$password = "123456";
$dbname = "tienda_ropa";

$con = new mysqli($host, $user, $password, $dbname, $port);

// Verifica si la conexión ha fallado
if ($con->connect_error) {
    die('Error de conexión: ' . $con->connect_error);
}

// Verifica si las variables POST están definidas para evitar advertencias
if (isset($_POST['correo']) && isset($_POST['contrasena'])) {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Prepara y ejecuta la consulta SQL
    $stmt = $con->prepare("SELECT id, contrasena FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    // Verifica si el correo existe en la base de datos
    if ($stmt->num_rows > 0) {
        // Obtiene los datos del usuario
        $stmt->bind_result($id_usuario, $hash_contrasena);
        $stmt->fetch();

        // Verifica la contraseña
        if (password_verify($contrasena, $hash_contrasena)) {
            // Inicia la sesión y redirige al carrito
            $_SESSION['id_usuario'] = $id_usuario;
            header("Location: ../carrito.php");
            exit();
        } else {
            // Si la contraseña es incorrecta
            echo "Contraseña incorrecta.";
        }
    } else {
        // Si el correo no está registrado, redirige a la página de registro
        header("Location: ./registro.html");
        exit();
    }

    // Cierra la declaración y la conexión
    $stmt->close();
    $con->close();
} else {
    // Si los datos no se enviaron correctamente
    echo "Por favor, ingrese su correo y contraseña.";
}
?>
