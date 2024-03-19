<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de consulta de Vuelos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>TUSMEJORESVUELOS.COM</h1>
    </header>

    <div class="container">
        <h2>Perfil</h2>
        
        <!-- Este php muestra las reservas del usuario y su saldo
    ademas de un boton para introducir saldo.-->

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
        else
        {
            if(!isset($_COOKIE["usuario_id"]))
            {
                // Enviar al usuario a InicioSesion.php:
                header("Location: InicioSesion.php");
                exit();
            }
            else
            {
                // Obtener id usuario de la cookie:
                $usuario_id = $_COOKIE["usuario_id"];

                // Obtener vuelos reservados:
                //! LEER Mostrar solo vuelos posteriores a la fecha actual? O mostrar todos?
                $consulta_reserva = "SELECT * FROM reservas WHERE idUsuario = $usuario_id";
                $resultado_reserva = $conn->query($consulta_reserva);
                    
                // Comprobar si tiene al menos un vuelo reservado:
                if($resultado_reserva->num_rows != 0)
                {
                    echo "<h2>Reservas realizadas:</h2>";
                    echo "<ul>";
                    // Iterar sobre el resultado y mostrar la información de cada reserva:
                    while ($row = $resultado_reserva->fetch_assoc())
                    {
                        // Obtener la información del vuelo asociado a la reserva
                        $idVuelo = $row["idVuelo"];
                        $consulta_vuelo = "SELECT origen, destino, precio, hora_salida, hora_llegada, aerolinea FROM vuelos WHERE id = $idVuelo";
                        $resultado_vuelo = $conn->query($consulta_vuelo);
                        $row_vuelo = $resultado_vuelo->fetch_assoc();

                        echo "<li>";
                        echo "Origen: " . $row_vuelo["origen"] . "<br>";
                        echo "Destino: " . $row_vuelo["destino"] . "<br>";
                        echo "Fecha de Reserva: " . $row["fechaReserva"] . "<br>";
                        echo "Hora de Salida: " . $row_vuelo["hora_salida"] . "<br>";
                        echo "Hora de Llegada: " . $row_vuelo["hora_llegada"] . "<br>";
                        echo "Aerolínea: " . $row_vuelo["aerolinea"] . "<br>";
                        echo "Precio: " . $row_vuelo["precio"] . "<br>";
                        
                        // Puedes agregar más detalles si los deseas
                        echo "</li>";
                    }
                        echo "</ul>";   
                }
                else
                {
                    echo "No tienes ningún vuelo reservado";
                }

                //* Introducir saldo:
                ?>
                <form method="post" action="">
                    <label for="cantidad">Introducir cantidad a agregar:</label>
                    <input type="number" id="cantidad" name="cantidad" required>
                    <input type="submit" value="Añadir saldo">
                </form>

                <?php

                // Verificar si el formulario ha sido enviado:
                if ($_SERVER["REQUEST_METHOD"] == "POST")
                {
                    if(isset($_POST['cantidad']))
                    {
                        if (!is_numeric($_POST['cantidad']) || $_POST['cantidad'] < 0)
                        {
                            echo "Por favor, introduzca un valor numérico válido." . "<br>";
                        }
                        else
                        {
                            // Obtener la cantidad a agregar del formulario:
                            $cantidadExtra = $_POST['cantidad'];

                            // Actualizar el saldo del usuario:
                            $consulta_actualizar_saldo = "UPDATE usuarios SET saldo = saldo + $cantidadExtra WHERE dni = $usuario_id";
                            $resultado_actualizar_saldo = $conn->query($consulta_actualizar_saldo);
                            
                            if ($resultado_actualizar_saldo)
                            {
                                echo "¡Saldo actualizado correctamente!" . "<br>";
                            } 
                            else
                            {
                                echo "Error al actualizar el saldo: " . $conn->error . "<br>";
                            }
                        }
                    }
                }

                //* Ver saldo:
                $consulta_usuario = "SELECT * FROM usuarios WHERE dni = $usuario_id";
                $resultado_usuario = $conn->query($consulta_usuario);
                $row = $resultado_usuario->fetch_assoc();
                $saldo = $row["saldo"];
                echo "Saldo actual: " . $saldo . "<br>";
            }
        } 
        // Cerrar la conexión:
        $conn->close();
    }

            if (!isset($_COOKIE["usuario_id"]))
            {
                echo '<form method="post" action="InicioSesion.php">
                        <input type="submit" value="Iniciar sesión">
                    </form>';
            }
            echo '<form method="post" action="Index.php">
                <input type="submit" value="Volver atras">
                </form>';
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="submit" name="cerrar_sesion" value="Cerrar sesión">
        </form>
    </div>
</body>
</html>

    <?php
    if (!isset($_COOKIE["usuario_id"]))
    {
        echo '<form method="post" action="InicioSesion.php">
                <input type="submit" value="Iniciar sesión">
              </form>';
    }
    ?>
</body>
</html>