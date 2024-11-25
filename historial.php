<?php
session_start();
include('php/conec.php');
$conn = $conexion;

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el usuario está logueado, si no, redirigir a la página de login
if (!isset($_SESSION['id_u'])) {
    header("Location: index.php");
    exit();
}

// Obtener el ID del usuario desde la sesión
$id_u = $_SESSION['id_u'];

// Obtener los datos del usuario
$consulta_usuario = "
    SELECT CONCAT(nom_u, ' ', ap_u, ' ', am_u) AS nombre, correo_u
    FROM usuario 
    WHERE id_u = $id_u
";
$result_usuario = $conn->query($consulta_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

// Obtener las compras del usuario y los videojuegos asociados
$consulta_compras = "
    SELECT v.nom_v, v.precio, v.imagen, c.subtotal, c.total
    FROM compra c
    JOIN vc vc ON c.id_c = vc.id_c
    JOIN videojuegos v ON vc.id_v = v.id_v
    WHERE c.id_u = $id_u
";
$result_compras = $conn->query($consulta_compras);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras - BLACK-GAMES</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Doto:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Doto:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Port+Lligat+Sans&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/num_pg.css">
    <link rel="stylesheet" href="css/catalogo.css">
    <link rel="stylesheet" href="css/hist.css">
</head>

<body>
    <header>
        <div class="tit">
            <a href="cliente.php" style="text-decoration: none;">
                <h1 style="cursor: pointer; font-family: 'Doto', sans-serif; font-size: xxx-large;">BLACK-GAMES</h1>
                  <a href="index.php">Cerrar Sesion</a> <br> <a href="estadisticas.php">Estadisticas</a> 
            </a>
        </div>
    </header>

    <div class="user-info">
        <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($usuario['correo_u']); ?></p>
    </div>

    <div class="purchase-history">
        <h2 style="color: azure;">Historial de Compras</h2>
        <div class="purchase-cards">
            <?php if ($result_compras->num_rows > 0): ?>
                <?php while ($compra = mysqli_fetch_assoc($result_compras)): ?>
                    <div class="purchase-card">
                        <img src="<?php echo htmlspecialchars($compra['imagen']); ?>" alt="<?php echo htmlspecialchars($compra['nom_v']); ?>">
                        <h3><?php echo htmlspecialchars($compra['nom_v']); ?></h3>
                        <p><strong>Precio:</strong> $<?php echo number_format($compra['precio'], 2); ?></p>
                        <p><strong>Subtotal:</strong> $<?php echo number_format($compra['subtotal'], 2); ?></p>
                        <p><strong>Total:</strong> $<?php echo number_format($compra['total'], 2); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No has realizado ninguna compra aún.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close(); // Cerrar la conexión con la base de datos
?>
