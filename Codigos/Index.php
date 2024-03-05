<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de consulta de Vuelos</title>

    <script>
        function validarFechas()
        {
            var fecha = document.getElementById('fecha').value;
            var fechaRegreso = document.getElementById('fechaRegreso').value;

            if (fechaRegreso !== '' && fechaRegreso < fecha)
            {
                alert('La fecha de regreso no puede ser anterior a la fecha de salida.');
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <h2>Formulario de consulta de Vuelos</h2>
    <form action="index.php" method="post" onsubmit="return validarFechas()">
        
        <label for="origen">Origen:</label><br>
        <input type="text" id="origen" name="origen" required><br><br>
        
        <label for="destino">Destino:</label><br>
        <input type="text" id="destino" name="destino" required><br><br>
        
        <label for="fecha">Fecha de Salida:</label><br>
        <input type="date" id="fecha" name="fecha" required><br><br>
        
        <input type="submit" value="Consultar Vuelos">
    </form>

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
        if ($conn->connect_error)
        {
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

        if ($result->num_rows > 0)
        {
            // Mostrar los resultados de la consulta:
            echo "<h3>Vuelos encontrados:</h3>";
            echo "<ul>";

            while ($row = $result->fetch_assoc())
            {
                echo "<li>Origen: " . $row["origen"] . ", Destino: " . $row["destino"] . ", Fecha de Salida: " . $row["fecha"] . ", Hora de Salida: " . $row["hora_salida"] . ", Hora de Llegada: " . $row["hora_llegada"];

                // Botón para reservar los vuelos mostrados:
                echo "<form action='index.php' method='post'>";
                echo "<input type='hidden' name='vuelo_id' value='" . $row['id'] . "'>";
                echo "<input type='submit' value='Reservar'>";
                echo "</form>";

                echo "</li>";
            }

            echo "</ul>";

            // Insertar el vuelo reservado a reservas:
            // Obtener la capacidad
            $capacidad = $row["capacidad"];

            //! TODO (cuando podamos iniciar sesion) Obtener el saldo del usuario:
            // Hacer select del usuario
            //$saldo = $row;

            // Comprobar si el vuelo tiene capacidad y el usuario tiene saldo:
            if ($capacidad > 1 && $saldo >= $row["precio"])
            {
                $result = "INSERT INTO reservas VALUES()";
                // Ejecutar la consulta:
                $result = $conn->query($sql);

                // Si el insert se hace con exito, reducir capacidad en 1:
                $capacidad = $capacidad - 1;

                // Actualizar capacidad del vuelo:
            }
        } 
        else
        {
            echo "No se encontraron vuelos para los criterios de búsqueda especificados.";
        }

        // Cerrar la conexión:
        $conn->close();
    }
    ?>
</body>
</html>