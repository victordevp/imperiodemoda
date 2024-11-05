<?php
session_start();

$host="localhost";
$port=3306;
$socket="";
$user="root";
$password="123456";
$dbname="tienda_ropa";

$con = new mysqli($host, $user, $password, $dbname, $port, $socket)
    or die ('Could not connect to the database server' . mysqli_connect_error());

$nombre_usuario = $_POST['nombre_usuario'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$stmt = $con->prepare("SELECT correo FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "El correo ya está registrado. Por favor, intenta con otro.";
} else {
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_BCRYPT);

    $stmt = $con->prepare("INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre_usuario, $correo, $contrasena_encriptada);

    if ($stmt->execute()) {
        // Redirige a sesion.html después de un registro exitoso
        header("Location: sesion.html");
        exit(); // Asegúrate de salir después de la redirección
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }
}

$stmt->close();
$con->close();
?>
