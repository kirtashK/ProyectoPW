<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de consulta de Vuelos</title>
    <link rel="stylesheet" href="estilos.css">
    <script>
        function validarFechas()
        {
            var fecha = document.getElementById('fecha').value;
            /*var fechaRegreso = document.getElementById('fechaRegreso').value;

            if (fechaRegreso !== '' && fechaRegreso < fecha)
            {
                alert('La fecha de regreso no puede ser anterior a la fecha de salida.');
                return false;
            }*/
            const fechaActual = new Date().toISOString().slice(0, 10);
            if(fecha < fechaActual)
            {
                alert('Debes seleccionar una fecha posterior a la actual');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <header>
        <h1>TUSMEJORESVUELOS.COM</h1>
    </header>
    <div class="container">
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
        <?php
        if (!isset($_COOKIE["usuario_id"]))
        {
            echo '<form method="post" action="InicioSesion.php">
                    <input type="submit" value="Iniciar sesión">
                  </form>';
        }
        ?>

        <!-- Botones de administrador o ver perfil -->
        <?php
        if (isset($_COOKIE["usuario_id"]))
        {
            if ($_COOKIE["usuario_rol"] == "admin")
            {
                echo '<form method="post" action="admin.php">
                        <input type="submit" value="Administrador">
                      </form>';
            }
            else
            {
                echo '<form method="post" action="perfilUsuario.php">
                        <input type="submit" value="Ver perfil">
                      </form>';
            }
            ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="submit" name="cerrar_sesion" value="Cerrar sesión">
            </form>
        <?php
        }
        ?>
        
    </div>
</body>
</html>



    <!-- Este php recibe el formulario de consulta de vuelos de index.php
    y lo procesa, tendrá que conectarse a la base de datos, 
    comprobar que los vuelos a mostrar no estan llenos y etc
    y entonces mostrar los vuelos que cumplan las condiciones al usuario.-->

    <?php
    session_start();
    function cerrarSesion()
    {
        // Establecer la fecha de caducidad en el pasado para eliminar la cookie
        setcookie("usuario_id", "", time() - 3600, "/");
    }
    
    // Verificar si se ha enviado una solicitud POST
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Verificar si se hizo clic en el botón "Cerrar sesión"
        if(isset($_POST["cerrar_sesion"]))
        {
            // Llamar a la función para eliminar la cookie
            cerrarSesion();
            // Redirigir a una página de confirmación o a la página de inicio
            header("Location: index.php");
            exit();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        require_once "config.php";

        // Si vuelo_id esta vacio, todavia no se ha dado a reservar un vuelo:
        if (!isset($_POST['vuelo_id']))
        {
            // Recuperar los datos del formulario:
            $origen = $_POST['origen'];
            $destino = $_POST['destino'];
            $fecha = $_POST['fecha'];

            // Consulta SQL para seleccionar los vuelos:
            $sql = "SELECT * FROM vuelos WHERE origen = '$origen' AND destino = '$destino' AND fecha = '$fecha' AND capacidad > 0";

            // Ejecutar la consulta:
            $result = $conn->query($sql);

            if ($result->num_rows > 0)
            {
                // Mostrar los resultados de la consulta:
                echo "<h3>Vuelos encontrados:</h3>";
                echo "<ul>";

                while ($row = $result->fetch_assoc())
                {
                    echo "<li>Origen: " . $row["origen"] . ", Destino: " . $row["destino"] . "\nFecha de Salida: " . $row["fecha"] . ", Hora de Salida: " . $row["hora_salida"] . ", Hora de Llegada: " . $row["hora_llegada"] . "\precio: " . $row["precio"];

                    // Botón para reservar los vuelos mostrados:
                    echo "<form action='index.php' method='post'>";
                    echo "<input type='hidden' name='vuelo_id' value='" . $row['id'] . "'>";
                    echo "<input type='submit' value='Reservar'>";
                    echo "</form>";

                    echo "</li>";
                }

                echo "</ul>";
            }
            else
            {
                echo "No se encontraron vuelos para los criterios de búsqueda especificados.";
            }
        }
        else
        {
            if(isset($_POST['vuelo_id']))
            {
                if(!isset($_COOKIE["usuario_id"]))
                {
                    // Enviar al usuario a InicioSesion.php:
                    header("Location: InicioSesion.php");
                    exit();
                }
                else
                {
                    // Obtener el id del vuelo a reservar:
                    $vuelo_id = $_POST['vuelo_id'];

                    // Obtener id usuario de la cookie:
                    $usuario_id = $_COOKIE["usuario_id"];

                    // Comprobar si tiene el vuelo reservado:
                    $consulta_reserva = "SELECT idReserva FROM reservas WHERE idUsuario = $usuario_id AND idVuelo = $vuelo_id";
                    $resultado_reserva = $conn->query($consulta_reserva);
                    
                    // Si el usuario ya ha reservado un vuelo, no puede volver a reservarlo:
                    if($resultado_reserva->num_rows == 0)
                    {
                        // Obtener la capacidad del vuelo:
                        $consulta_capacidad = "SELECT precio, capacidad FROM vuelos WHERE id = $vuelo_id";
                        $resultado_capacidad = $conn->query($consulta_capacidad);

                        if ($resultado_capacidad->num_rows > 0)
                        {
                            // Obtener la capacidad y precio del vuelo a reservar y comprobar si hay suficiente capacidad:
                            $row = $resultado_capacidad->fetch_assoc();
                            $capacidad_actual = $row['capacidad'];
                            $precio = $row['precio'];
                            
                            if ($capacidad_actual >= 1)
                            {
                                // Comprobar si el usuario tiene saldo:
                                $consulta_saldo = "SELECT saldo FROM usuarios WHERE dni = $usuario_id";
                                $resultado_saldo = $conn->query($consulta_saldo);

                                if ($resultado_saldo->num_rows > 0)
                                {
                                    $row = $resultado_saldo->fetch_assoc();
                                    $saldo = $row["saldo"];

                                    if($saldo >= $precio)
                                    {
                                        // Restar precio al saldo del usuario:
                                        $nuevoSaldo = $saldo - $precio;
                                        $actualizar_saldo = "UPDATE usuarios SET Saldo = $nuevoSaldo WHERE dni = $usuario_id";
                                        $resultado_saldo = $conn->query($actualizar_saldo);
                                    
                                        // Restar capacidad al vuelo:
                                        $nueva_capacidad = $capacidad_actual - 1;
                                        $actualizar_capacidad = "UPDATE vuelos SET capacidad = $nueva_capacidad WHERE id = $vuelo_id";
                                        $resultado_capacidad = $conn->query($actualizar_capacidad);

                                        // Obtener $idReserva:
                                        $consulta_reserva = "SELECT MAX(idReserva) AS maxId FROM reservas";
                                        $resultado_reserva = $conn->query($consulta_reserva);

                                        if ($resultado_reserva->num_rows > 0)
                                        {
                                            $row = $resultado_reserva->fetch_assoc();
                                            $reserva_id = $row["maxId"];
                                            $reserva_id += 1;
                                        }
                                        else
                                        {
                                            $reserva_id = 1;
                                        }

                                        // Obtener la fecha actual:
                                        $fecha_reserva = date("Y-m-d");

                                        //* Insertar la reserva:
                                        $insertar_reserva =    "INSERT INTO reservas (idReserva, idVuelo, idUsuario, fechaReserva, precio) 
                                                                VALUES ('$reserva_id', '$vuelo_id', '$usuario_id', '$fecha_reserva', '$precio')";

                                        if ($conn->query($insertar_reserva) === TRUE)
                                        {
                                            echo "¡Reserva realizada con éxito!";
                                        }
                                        else
                                        {
                                            echo "Error al insertar la reserva: " . $conn->error;
                                        }
                                    }
                                } 
                                else
                                {
                                    // Enviar al usuario a InicioSesion.php:
                                    header("Location: InicioSesion.php");
                                    exit();
                                }
                            } 
                            else
                            {
                                echo "El vuelo seleccionado no tiene capacidad para el número de personas reservandos.";
                            }
                        }
                    }
                    else
                    {
                        echo "Ya has reservado ese vuelo.";
                    }
                }
            }
        } 
        // Cerrar la conexión:
        $conn->close();
    }
    ?>
</body>
</html>