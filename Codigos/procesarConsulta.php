<!-- Este php recibe el formulario de consulta de vuelos de index.php
y lo procesa, tendrá que conectarse a la base de datos, 
comprobar que los vuelos a mostrar no estan llenos y etc
y entonces mostrar los vuelos que cumplan las condiciones al usuario.-->

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Establecer la conexión con la base de datos:
    $servername = "localhost";
    $username = "pw";
    $password = "pw";
    $database = "pw_vuelo";

    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión:
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Recuperar los datos del formulario:
    $origen = $_POST['origen'];
    $destino = $_POST['destino'];
    $fecha = $_POST['fecha'];

    // Consulta SQL para seleccionar los vuelos:
    $sql = "SELECT * FROM vuelos WHERE origen = '$origen' AND destino = '$destino' AND fecha = '$fecha'";

    // Ejecutar la consulta:
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Mostrar los resultados de la consulta:
        echo "<h3>Vuelos encontrados:</h3>";
        echo "<ul>";

        while ($row = $result->fetch_assoc())
        {
            echo "<li>Origen: " . $row["origen"] . ", Destino: " . $row["destino"] . ", Fecha de Salida: " . $row["fecha"] . "</li>";
        }
        echo "</ul>";
    }
    else
    {
        echo "No se encontraron vuelos para los criterios de búsqueda especificados.";
    }

    // Cerrar la conexión:
    $conn->close();
}
?>