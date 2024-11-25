<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gb"; // Base de datos "gb"

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 1. Cantidad de juegos por clasificación
$sql_clasificacion = "SELECT clasif_v, COUNT(*) AS total FROM videojuegos GROUP BY clasif_v";
$result_clasificacion = $conn->query($sql_clasificacion);

// 2. Por género
$sql_genero = "SELECT genero_v, COUNT(*) AS total FROM videojuegos GROUP BY genero_v";
$result_genero = $conn->query($sql_genero);

// 3. Precio promedio de videojuegos por género
$sql_precio_promedio = "SELECT genero_v, AVG(precio) AS precio_promedio FROM videojuegos GROUP BY genero_v";
$result_precio_promedio = $conn->query($sql_precio_promedio);

// 4. Cantidad de juegos por década
$sql_decada = "SELECT CONCAT(FLOOR(YEAR(fecha_lanz) / 10) * 10, 's') AS decada, COUNT(*) AS total
               FROM videojuegos GROUP BY decada ORDER BY decada";
$result_decada = $conn->query($sql_decada);

// 5. Juegos estrenados por año
$sql_anio = "SELECT YEAR(fecha_lanz) AS anio, COUNT(*) AS total FROM videojuegos GROUP BY anio ORDER BY anio";
$result_anio = $conn->query($sql_anio);

// 6. Porcentaje de juegos por clasificación
$sql_porcentaje_clasif = "SELECT clasif_v, COUNT(*) * 100.0 / (SELECT COUNT(*) FROM videojuegos) AS porcentaje 
                          FROM videojuegos GROUP BY clasif_v ORDER BY porcentaje DESC";
$result_porcentaje_clasif = $conn->query($sql_porcentaje_clasif);

// 7. Juegos más caros
$sql_juegos_caros = "SELECT nom_v, precio FROM videojuegos ORDER BY precio DESC LIMIT 5";
$result_juegos_caros = $conn->query($sql_juegos_caros);

// 8. Juegos más antiguos
$sql_juegos_antiguos = "SELECT nom_v, fecha_lanz FROM videojuegos ORDER BY fecha_lanz ASC LIMIT 5";
$result_juegos_antiguos = $conn->query($sql_juegos_antiguos);

// 9. Promedio subtotal por usuario
$sql_gasto_promedio = "SELECT u.nom_u, u.ap_u, AVG(c.subtotal) AS gasto_promedio
                       FROM compra c JOIN usuario u ON c.id_u = u.id_u
                       GROUP BY u.id_u ORDER BY gasto_promedio DESC";
$result_gasto_promedio = $conn->query($sql_gasto_promedio);

// 10. Videojuegos comprados por usuario
$sql_videojuegos_comprados = "SELECT u.nom_u, u.ap_u, COUNT(vc.id_v) AS cantidad_videojuegos_comprados
                              FROM vc JOIN compra c ON vc.id_c = c.id_c
                              JOIN usuario u ON c.id_u = u.id_u
                              GROUP BY u.id_u ORDER BY cantidad_videojuegos_comprados DESC";
$result_videojuegos_comprados = $conn->query($sql_videojuegos_comprados);

// 11. Compras por tarjeta
$sql_compras_tarjeta = "SELECT t.tipo_tj, COUNT(c.id_c) AS total_compras
                        FROM compra c JOIN tarjeta t ON c.id_u = t.id_u
                        GROUP BY t.tipo_tj ORDER BY total_compras DESC";
$result_compras_tarjeta = $conn->query($sql_compras_tarjeta);

// Procesar los resultados de las consultas y almacenarlos en arrays
$clasificacion = [];
$votos_clasificacion = [];
while ($row = $result_clasificacion->fetch_assoc()) {
    $clasificacion[] = $row['clasif_v'];
    $votos_clasificacion[] = $row['total'];
}

$generos = [];
$votos_genero = [];
while ($row = $result_genero->fetch_assoc()) {
    $generos[] = $row['genero_v'];
    $votos_genero[] = $row['total'];
}

$precio_promedio_genero = [];
$precio_promedio = [];
while ($row = $result_precio_promedio->fetch_assoc()) {
    $precio_promedio_genero[] = $row['genero_v'];
    $precio_promedio[] = $row['precio_promedio'];
}

$decadas = [];
$votos_decada = [];
while ($row = $result_decada->fetch_assoc()) {
    $decadas[] = $row['decada'];
    $votos_decada[] = $row['total'];
}

$anios = [];
$votos_anio = [];
while ($row = $result_anio->fetch_assoc()) {
    $anios[] = $row['anio'];
    $votos_anio[] = $row['total'];
}

$porcentaje_clasif = [];
$porcentaje_votos_clasif = [];
while ($row = $result_porcentaje_clasif->fetch_assoc()) {
    $porcentaje_clasif[] = $row['clasif_v'];
    $porcentaje_votos_clasif[] = $row['porcentaje'];
}

$juegos_caros = [];
$precios_caros = [];
while ($row = $result_juegos_caros->fetch_assoc()) {
    $juegos_caros[] = $row['nom_v'];
    $precios_caros[] = $row['precio'];
}

$juegos_antiguos = [];
$fechas_antiguos = [];
while ($row = $result_juegos_antiguos->fetch_assoc()) {
    $juegos_antiguos[] = $row['nom_v'];
    $fechas_antiguos[] = $row['fecha_lanz'];
}

$usuarios_gasto = [];
$gasto_promedio = [];
while ($row = $result_gasto_promedio->fetch_assoc()) {
    $usuarios_gasto[] = $row['nom_u'] . " " . $row['ap_u'];
    $gasto_promedio[] = $row['gasto_promedio'];
}

$usuarios_compras = [];
$cantidad_compras = [];
while ($row = $result_videojuegos_comprados->fetch_assoc()) {
    $usuarios_compras[] = $row['nom_u'] . " " . $row['ap_u'];
    $cantidad_compras[] = $row['cantidad_videojuegos_comprados'];
}

$tarjetas = [];
$total_compras_tarjeta = [];
while ($row = $result_compras_tarjeta->fetch_assoc()) {
    $tarjetas[] = $row['tipo_tj'];
    $total_compras_tarjeta[] = $row['total_compras'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas de Videojuegos</title>
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
    <link rel="stylesheet" href="css/estadisticas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header>
    <h1>Estadisticas de BLACK-GAMES</h1>



    

                       
    <div class="menu">
    <select id="graficaSelect">
        <option value="clasificacion">Cantidad de Juegos por Clasificación</option>
        <option value="genero">Cantidad de Juegos por Género</option>
        <option value="precio_promedio">Precio Promedio por Género</option>
        <option value="decada">Cantidad de Juegos por Década</option>
        <option value="anio">Juegos Estrenados por Año</option>
        <option value="porcentaje_clasif">Porcentaje de Juegos por Clasificación</option>
        <option value="juegos_caros">Juegos Más Caros</option>
        <option value="juegos_antiguos">Juegos Más Antiguos</option>
        <option value="gasto_promedio">Promedio Subtotal por Usuario</option>
        <option value="compras_usuario">Videojuegos Comprados por Usuario</option>
        <option value="compras_tarjeta">Compras por Tipo de Tarjeta</option>
        <option value="todas">Ver todas las estadísticas</option> <!-- Opción para ver todas las gráficas -->
    </select>
</div>





    <div class="tit">
            <a href="cliente.php" style="text-decoration: none;">
                <h1 >BLACK-GAMES</h1>
                  <a style="text-decoration: underline; text-align: center; cursor: pointer;" href="historial.php">Historial de compras</a> </p>
            </a>
        </div>
</header>

<br><br><br><br>

<section id="clasificacion">
    <h2>Cantidad de Juegos por Clasificación</h2>
    <canvas id="clasificacionChart"></canvas>
</section>

<section id="genero">
    <h2>Cantidad de Juegos por Género</h2>
    <canvas id="generoChart"></canvas>
</section>

<section id="precio_promedio">
    <h2>Precio Promedio de Videojuegos por Género</h2>
    <canvas id="precioPromedioChart"></canvas>
</section>

<section id="decada">
    <h2>Cantidad de Juegos por Década</h2>
    <canvas id="decadaChart"></canvas>
</section>

<section id="anio">
    <h2>Juegos Estrenados por Año</h2>
    <canvas id="anioChart"></canvas>
</section>

<section id="porcentaje_clasif">
    <h2>Porcentaje de Juegos por Clasificación</h2>
    <canvas id="porcentajeClasifChart"></canvas>
</section>

<section id="juegos_caros">
    <h2>Juegos Más Caros</h2>
    <canvas id="juegosCarosChart"></canvas>
</section>

<section id="juegos_antiguos">
    <h2>Juegos Más Antiguos</h2>
    <canvas id="juegosAntiguosChart"></canvas>
</section>

<section id="gasto_promedio">
    <h2>Promedio Subtotal por Usuario</h2>
    <canvas id="gastoPromedioChart"></canvas>
</section>

<section id="compras_usuario">
    <h2>Videojuegos Comprados por Usuario</h2>
    <canvas id="comprasUsuarioChart"></canvas>
</section>

<section id="compras_tarjeta">
    <h2>Compras por Tipo de Tarjeta</h2>
    <canvas id="comprasTarjetaChart"></canvas>
</section>

<script>
// Gráficas
const ctx_clasificacion = document.getElementById('clasificacionChart').getContext('2d');
const ctx_genero = document.getElementById('generoChart').getContext('2d');
const ctx_precio_promedio = document.getElementById('precioPromedioChart').getContext('2d');
const ctx_decada = document.getElementById('decadaChart').getContext('2d');
const ctx_anio = document.getElementById('anioChart').getContext('2d');
const ctx_porcentaje_clasif = document.getElementById('porcentajeClasifChart').getContext('2d');
const ctx_juegos_caros = document.getElementById('juegosCarosChart').getContext('2d');
const ctx_juegos_antiguos = document.getElementById('juegosAntiguosChart').getContext('2d');
const ctx_gasto_promedio = document.getElementById('gastoPromedioChart').getContext('2d');
const ctx_compras_usuario = document.getElementById('comprasUsuarioChart').getContext('2d');
const ctx_compras_tarjeta = document.getElementById('comprasTarjetaChart').getContext('2d');

// Gráfica de Juegos por Clasificación
new Chart(ctx_clasificacion, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($clasificacion); ?>,
        datasets: [{
            label: 'Cantidad de Juegos por Clasificación',
            data: <?php echo json_encode($votos_clasificacion); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)'
            ]
        }]
    }
});

// Gráfica de Juegos por Género
new Chart(ctx_genero, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($generos); ?>,
        datasets: [{
            label: 'Cantidad de Juegos por Género',
            data: <?php echo json_encode($votos_genero); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)'
            ]
        }]
    }
});

// Gráfica de Precio Promedio por Género
new Chart(ctx_precio_promedio, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($precio_promedio_genero); ?>,
        datasets: [{
            label: 'Precio Promedio de Videojuegos',
            data: <?php echo json_encode($precio_promedio); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
        }]
    }
});

// Gráfica de Juegos por Década
new Chart(ctx_decada, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($decadas); ?>,
        datasets: [{
            label: 'Cantidad de Juegos por Década',
            data: <?php echo json_encode($votos_decada); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            fill: false
        }]
    }
});

// Gráfica de Juegos Estrenados por Año
new Chart(ctx_anio, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($anios); ?>,
        datasets: [{
            label: 'Juegos Estrenados por Año',
            data: <?php echo json_encode($votos_anio); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
        }]
    }
});

// Gráfica de Porcentaje de Juegos por Clasificación
new Chart(ctx_porcentaje_clasif, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($porcentaje_clasif); ?>,
        datasets: [{
            label: 'Porcentaje de Juegos por Clasificación',
            data: <?php echo json_encode($porcentaje_votos_clasif); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)'
            ]
        }]
    }
});

// Gráfica de Juegos Más Caros
new Chart(ctx_juegos_caros, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($juegos_caros); ?>,
        datasets: [{
            label: 'Juegos Más Caros',
            data: <?php echo json_encode($precios_caros); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
        }]
    }
});

// Gráfica de Juegos Más Antiguos
new Chart(ctx_juegos_antiguos, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($juegos_antiguos); ?>,
        datasets: [{
            label: 'Juegos Más Antiguos',
            data: <?php echo json_encode($fechas_antiguos); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
        }]
    }
});

// Gráfica de Gasto Promedio por Usuario
new Chart(ctx_gasto_promedio, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($usuarios_gasto); ?>,
        datasets: [{
            label: 'Gasto Promedio por Usuario',
            data: <?php echo json_encode($gasto_promedio); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
        }]
    }
});

// Gráfica de Videojuegos Comprados por Usuario
new Chart(ctx_compras_usuario, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($usuarios_compras); ?>,
        datasets: [{
            label: 'Videojuegos Comprados por Usuario',
            data: <?php echo json_encode($cantidad_compras); ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.6)',
        }]
    }
});

// Gráfica de Compras por Tarjeta
new Chart(ctx_compras_tarjeta, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($tarjetas); ?>,
        datasets: [{
            label: 'Compras por Tarjeta',
            data: <?php echo json_encode($total_compras_tarjeta); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)'
            ]
        }]
    }
});



// Seleccionar el menú desplegable y las secciones
const graficaSelect = document.getElementById('graficaSelect');
    
    // Event listener para cambiar a la sección seleccionada
    graficaSelect.addEventListener('change', function() {
        const selectedValue = graficaSelect.value;
        const sections = document.querySelectorAll('section');
        
        // Ocultar todas las secciones
        sections.forEach(section => {
            section.style.display = 'none';
        });
        
        // Mostrar la sección correspondiente a la opción seleccionada
        if (selectedValue === 'todas') {
            // Mostrar todas las secciones
            sections.forEach(section => {
                section.style.display = 'block';
            });
        } else {
            // Mostrar solo la sección seleccionada
            const selectedSection = document.getElementById(selectedValue);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        }
    });

    // Inicialmente mostrar la sección de "clasificacion"
    document.getElementById('clasificacion').style.display = 'block';

</script>


</body>
</html>
