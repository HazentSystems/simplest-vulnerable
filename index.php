<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

/*
  Arreglo de seguridad:
  1) Se corrige la vulnerabilidad de SQL Injection evitando concatenar entrada del usuario en la consulta.
  2) Se valida que 'id' contenga solo dígitos y se usa una consulta preparada (prepared statement) con enlace de parámetros.
*/
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    // Validación de entrada: aceptar solo dígitos para un ID numérico
    if (!ctype_digit($id)) {
        echo "ID inválido";
    } else {
        $id = (int)$id;
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Si la preparación falla, evitar exponer detalles internos al usuario
            echo "Error en la consulta";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "id: " . $row["id"]. " - Nombre: " . $row["nombre"]. "<br>";
                }
            } else {
                echo "0 resultados";
            }
            $stmt->close();
        }
    }
}


// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
