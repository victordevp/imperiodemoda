<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./login/sesion.html");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ./home.html");
    exit();
}

// Si se está procesando la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén los datos del carrito
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['cartItems'])) {
        $cartItems = $data['cartItems'];
        $cartItems = json_decode($cartItems, true); // Decodifica el JSON

        $to = $_SESSION['email']; // Asegúrate de que el email se almacene en la sesión
        $subject = "Confirmación de Compra";

        // Formato del cuerpo del correo en HTML
        $body = "
        <html>
        <head>
        <title>Confirmación de Compra</title>
        </head>
        <body>
        <h1>Gracias por tu compra en Imperio de la Moda</h1>
        <p>A continuación, tu resumen:</p>
        <table border='1' cellpadding='10' cellspacing='0'>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>";

        $total = 0;

        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $body .= "
            <tr>
                <td>{$item['name']}</td>
                <td>\${$item['price']}</td>
                <td>{$item['quantity']}</td>
                <td>\${$itemTotal}</td>
            </tr>";
            $total += $itemTotal;
        }

        $body .= "
            </tbody>
        </table>
        <p><strong>Total: \$${total}</strong></p>
        </body>
        </html>";

        // Cabeceras del correo
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@imperiomoda.com" . "\r\n";

        // Intentar enviar el correo
        if (mail($to, $subject, $body, $headers)) {
            // Limpia el carrito
            $_SESSION['cart'] = []; // Asegúrate de que el carrito esté en la sesión
            echo "<script>alert('Compra procesada. Revisa tu correo.');</script>";
        } else {
            echo "<script>alert('Hubo un error al enviar el correo. Intenta nuevamente.');</script>";
        }
    } else {
        echo "<script>alert('No se encontraron artículos en el carrito.');</script>";
    }
}

// Cierra la conexión (si hay alguna)

// Aquí puedes definir un array para simular el carrito en la sesión
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin-top: 80px;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #224b73 !important;
        }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .cart-item:hover {
            background-color: #e9ecef; /* Cambiar el color de fondo al pasar el mouse */
        }
        .btn-danger {
            margin-left: 10px; /* Espacio entre el precio y el botón de eliminar */
        }
        .summary {
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">IMPERIO DE LA MODA</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="./index.html">Inicio</a></li>
                    <li class="nav-item active"><a class="nav-link" href="./productos.html">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="./carrito.php">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/shopping-cart.png" alt="Carrito" style="width: 24px;">
                        <span class="cart-counter" id="cart-counter">0</span>
                    </a></li>
                    <li class="nav-item">
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="logout" class="btn btn-link nav-link">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Carrito de Compras</h2>
        <br>
        <div id="cart-items"></div>
        <br>
        <h3>Subtotal: $<span id="subtotal">0.00</span></h3>
        <h3>Total: $<span id="total">0.00</span></h3>
        <br>
        <button id="checkout" class="btn btn-success">Proceder a la compra</button>
    </div>

    <script src="./script.js"></script> <!-- Ajusta la ruta si es necesario -->
    <script>
        // Simulación de carga de artículos en el carrito
        let cartItems = [
            { name: "Bolso Louis Vuitton", price: 20000, quantity: 1 },
            { name: "Chamarra de lana", price: 20000, quantity: 1 }
            // Agrega más productos según sea necesario
        ];

        function updateCartDisplay() {
            let cartItemsHtml = '';
            let subtotal = 0;

            cartItems.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                cartItemsHtml += `
                    <div class="cart-item">
                        <span>${item.name}</span>
                        <span>$${item.price}</span>
                        <span>${item.quantity}</span>
                        <span>$${itemTotal}</span>
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Eliminar</button>
                    </div>`;
            });

            document.getElementById('cart-items').innerHTML = cartItemsHtml;
            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            document.getElementById('total').innerText = subtotal.toFixed(2);
        }

        function removeFromCart(index) {
            cartItems.splice(index, 1); // Elimina el producto del carrito
            updateCartDisplay(); // Actualiza la vista del carrito
        }

        document.getElementById('checkout').addEventListener('click', function() {
            fetch('./carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cartItems: JSON.stringify(cartItems) })
            }).then(response => response.text())
              .then(data => {
                  console.log(data); // Maneja la respuesta del servidor si es necesario
                  location.reload(); // Recargar la página o manejar de otra manera
              });
        });

        // Carga inicial de artículos
        updateCartDisplay();
    </script>
</body>
</html>
