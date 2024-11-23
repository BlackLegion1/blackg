<?php 

$host = 'localhost'; 
$user = 'root';      
$password = '';      
$database = 'gb'; 


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nom_v = $_POST['nom_v'];
    $desc_v = $_POST['desc_v'];
    $fecha_lanz = $_POST['fecha_lanz'];
    $clasif_v = $_POST['clasif_v'];
    $genero_v = $_POST['genero_v'];
    $precio = $_POST['precio'];

    // Procesar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $targetDir = "imagenes/juegos/"; // Carpeta de destino para las imágenes
        $targetFilePath = $targetDir . basename($fileName); // Ruta completa del archivo

        // Validar el tipo de archivo (solo imágenes)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            die("Solo se permiten archivos de imagen (JPEG, PNG, GIF).");
        }

        // Validar el tamaño del archivo (por ejemplo, máximo 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            die("El archivo es demasiado grande. El tamaño máximo permitido es 5MB.");
        }

        // Subir el archivo
        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            // Guardar la ruta de la imagen en la base de datos
            $imagen = "imagenes/juegos/" . basename($fileName); 

            
            $sql = "INSERT INTO videojuegos (nom_v, desc_v, fecha_lanz, clasif_v, genero_v, precio, imagen)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssis", $nom_v, $desc_v, $fecha_lanz, $clasif_v, $genero_v, $precio, $imagen);

            if ($stmt->execute()) {
                echo "<script>alert('El videojuego ha sido ingresado con éxito.'); window.location.href='prodconsultar.php';</script>";
            } else {
                echo "Error al insertar los datos: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Ha ocurrido un error al subir la imagen.";
        }
    } else {
        echo "No se ha seleccionado ninguna imagen o ha ocurrido un error con el archivo.";
    }
}

// Cerra
$conn->close();
?> 
