<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./login/sesion.html");
    exit();
}

$host = "localhost";
$port = 3306;
$user = "root";
$password = "123456";
$dbname = "tienda";

$con = new mysqli($host, $user, $password, $dbname, $port);

// Verifica la conexión
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Verifica si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAccion'])) {
    // Obtiene los datos del formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen'];

    // Prepara la consulta SQL
    $stmt = $con->prepare("INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $nombre, $precio, $descripcion, $imagen); // d para decimal, s para string

    // Ejecuta la consulta
    if ($stmt->execute()) {
        echo "<script>alert('Producto agregado correctamente.');</script>";
        

        header("Location: ./productos.html");
        exit();
    } else {
        echo "<script>alert('Error al agregar el producto: " . $stmt->error . "');</script>";
    }

    // Cierra la consulta
    $stmt->close();
}

// Cierra la conexión
$con->close();
?>
