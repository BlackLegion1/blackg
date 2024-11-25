<?php

$host = 'localhost'; 
$user = 'root';     
$password = '';      
$database = 'gb'; 


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$sql = "SELECT * FROM videojuegos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Videojuegos</title>
    <link rel="stylesheet" href="css/tablass.css">
</head>
<body>


<header class="header">
        <h1 class="site-title" id="site-title">Bienvenido Administrador</h1>


</header>

<!-- Botón para abrir el sidebar -->
<button class="menu-btn" id="menu-toggle">&#9776;</button> 

<br><br><br>

<aside class="sidebar" id="sidebar">
    <button class="close-btn" id="close-sidebar">&times;</button>
    <br><br><br>
    <nav class="sidebar-nav">
        <a href="login.html">• Cerrar sesión</a>
        <a href="registroadmin.html">• Ingresar nuevo usuario</a>
        <a href="consultarusuario.php">• Modificar registros de usuarios</a>
        <a href="prodingresar.html">• Ingresar productos</a>
        <a href="prodconsultar.php">• Modificar productos</a>
    </nav>
</aside>

<script>
    // Mostrar/ocultar el sidebar
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('visible');
    });

    document.getElementById('close-sidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('visible');
    });

    // Efecto de cambio de tamaño en el título al deslizar
    window.addEventListener('scroll', function() {
        const title = document.getElementById('site-title');
        if (window.scrollY > 50) {
            title.classList.add('shrink');
        } else {
            title.classList.remove('shrink');
        }
    });
</script>

    

 <div class="table-container" >    
    <table border="1" >
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha de Lanzamiento</th>
                <th>Clasificación</th>
                <th>Género</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom_v']); ?></td>
                    <td><?php echo htmlspecialchars($row['desc_v']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_lanz']); ?></td>
                    <td><?php echo htmlspecialchars($row['clasif_v']); ?></td>
                    <td><?php echo htmlspecialchars($row['genero_v']); ?></td>
                    <td><?php echo htmlspecialchars($row['precio']); ?></td>
                    <td>
                        <?php if ($row['imagen']) { ?>
                            <img src="<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen del videojuego" width="100">
                        <?php } ?>
                    </td>
                    <td>
                        <!-- Botón de Modificar -->
                        <a href="prodmodificar.php?id=<?php echo $row['id_v']; ?>">Modificar</a> |
                        <!-- Botón de Eliminar -->
                        <a href="prodeliminar.php?id=<?php echo $row['id_v']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este videojuego?')">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    <?php
    // Cerrar
    $conn->close();
    ?>
</body>
</html>
