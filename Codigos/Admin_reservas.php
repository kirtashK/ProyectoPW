<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 100px 100px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 10px;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        input{
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>ADMINISTRACIÓN DE LA RESERVA</h1>
    <!-- Aquí va el resto del contenido -->
</body>
</html>

<?php
require_once "config.php";

session_start();

if (!isset($_COOKIE["usuario_rol"]) || $_COOKIE["usuario_rol"] !== "admin") {
    die("Acceso denegado. Debes iniciar sesión como administrador para acceder a esta página.");
}

// Función para mostrar todas las reservas:
function mostrarReservas($pdo) {
    try {
        $sql = "SELECT * FROM reservas";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() > 0) {
            echo "<h2>Lista de Reservas:</h2>";
            echo "<table border='1'><tr><th>ID Reserva</th><th>ID Vuelo</th><th>ID Usuario</th><th>Fecha de Reserva</th><th>Precio</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>" . $row["idReserva"] . "</td><td>" . $row["idVuelo"] . "</td><td>" . $row["idUsuario"] . "</td><td>" . $row["fechaReserva"] . "</td><td>" . $row["precio"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No se encontraron reservas.";
        }
    } catch (PDOException $e) {
        echo "Error al mostrar reservas: " . $e->getMessage();
    }
}

// Función para modificar una reserva:
function modificarReserva($pdo, $idReserva, $idVuelo, $idUsuario, $fechaReserva, $precio) {
    try {
        $sql = "UPDATE reservas 
                SET idVuelo=?, idUsuario=?, fechaReserva=?, precio=? 
                WHERE idReserva=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idVuelo, $idUsuario, $fechaReserva, $precio, $idReserva]);
        echo "Reserva modificada con éxito.";
    } catch (PDOException $e) {
        echo "Error al modificar la reserva: " . $e->getMessage();
    }
}

// Función para crear una nueva reserva y actualizar la capacidad del vuelo:
function crearReserva($pdo, $idVuelo, $idUsuario, $fechaReserva, $precio) {
    try {
        // Verificar la capacidad disponible del vuelo
        $sql_capacidad = "SELECT capacidad FROM vuelos WHERE id=?";
        $stmt_capacidad = $pdo->prepare($sql_capacidad);
        $stmt_capacidad->execute([$idVuelo]);
        $capacidad_vuelo = $stmt_capacidad->fetchColumn();
        
        // Verificar si hay suficiente capacidad disponible
        if ($capacidad_vuelo > 0) {
            $pdo->beginTransaction(); // Comenzar transacción
            
            // Insertar la reserva
            $sql_insertar_reserva = "INSERT INTO reservas (idVuelo, idUsuario, fechaReserva, precio) 
                                     VALUES (?, ?, ?, ?)";
            $stmt_insertar_reserva = $pdo->prepare($sql_insertar_reserva);
            $stmt_insertar_reserva->execute([$idVuelo, $idUsuario, $fechaReserva, $precio]);
            
            // Actualizar la capacidad del vuelo
            $sql_actualizar_capacidad = "UPDATE vuelos SET capacidad = capacidad - 1 WHERE id=?";
           
            $stmt_actualizar_capacidad = $pdo->prepare($sql_actualizar_capacidad);
            $stmt_actualizar_capacidad->execute([$idVuelo]);
            
            $pdo->commit(); // Confirmar transacción
            echo "Nueva reserva creada con éxito.";
        } else {
            echo "No hay suficiente capacidad disponible en el vuelo.";
        }
    } catch (PDOException $e) {
        echo "Error al crear la reserva: " . $e->getMessage();
    }
}

// Función para eliminar una reserva y devolver el dinero al usuario, y actualizar la capacidad del vuelo:
function eliminarReserva($pdo, $idReserva) {
    try {
        $pdo->beginTransaction(); // Comenzar transacción
        
        // Obtener el idVuelo de la reserva
        $sql_info_reserva = "SELECT idVuelo FROM reservas WHERE idReserva=?";
        $stmt_info_reserva = $pdo->prepare($sql_info_reserva);
        $stmt_info_reserva->execute([$idReserva]);
        $idVuelo = $stmt_info_reserva->fetchColumn();
        
        // Eliminar la reserva
        $sql_eliminar = "DELETE FROM reservas WHERE idReserva=?";
        $stmt_eliminar = $pdo->prepare($sql_eliminar);
        $stmt_eliminar->execute([$idReserva]);
        
        // Incrementar la capacidad del vuelo
        $sql_incrementar_capacidad = "UPDATE vuelos SET capacidad = capacidad + 1 WHERE id=?";
        $stmt_incrementar_capacidad = $pdo->prepare($sql_incrementar_capacidad);
        $stmt_incrementar_capacidad->execute([$idVuelo]);
        
        $pdo->commit(); // Confirmar transacción
        echo "Reserva eliminada y capacidad del vuelo actualizada.";
    } catch (PDOException $e) {
        $pdo->rollBack(); // Revertir transacción en caso de error
        echo "Error al eliminar la reserva y actualizar la capacidad del vuelo: " . $e->getMessage();
    }
}

// Lógica para procesar las operaciones CRUD:

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué acción se ha solicitado:
    if (isset($_POST["crear_reserva"])) {
        // Procesar la creación de una nueva reserva:
        crearReserva($pdo, $_POST["idVuelo"], $_POST["idUsuario"], $_POST["fechaReserva"], $_POST["precio"]);
    } elseif (isset($_POST["modificar_reserva"])) {
        // Procesar la modificación de una reserva:
        modificarReserva($pdo, $_POST["idReserva"], $_POST["idVuelo"], $_POST["idUsuario"], $_POST["fechaReserva"], $_POST["precio"]);
    } elseif (isset($_POST["eliminar_reserva"])) {
        // Procesar la eliminación de una reserva:
        eliminarReserva($pdo, $_POST["idReserva_eliminar"]);
    }
}

// Mostrar el formulario para crear una nueva reserva:
echo "<h2>Crear Nueva Reserva:</h2>";
echo "<form method='post'>";
echo "ID Vuelo: <input type='text' name='idVuelo' required><br>";
echo "ID Usuario: <input type='text' name='idUsuario' required><br>";
echo "Fecha de Reserva: <input type='date' name='fechaReserva' required><br>";
echo "Precio: <input type='number' name='precio' required><br>";
echo "<input type='submit' name='crear_reserva' value='Crear Reserva'>";
echo "</form>";

echo "<h2>Buscar Reserva por ID:</h2>";
echo "<form method='post'>";
echo "ID Reserva: <input type='text' name='idReserva_busqueda'>";
echo "<input type='submit' name='buscar_reserva' value='Buscar Reserva'>";
echo "</form>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscar_reserva"])) {
    $idReserva_busqueda = $_POST["idReserva_busqueda"];
    if (!empty($idReserva_busqueda)) {
        // Mostrar la reserva con el ID proporcionado:
        try {
            $sql = "SELECT * FROM reservas WHERE idReserva=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idReserva_busqueda]);
            if ($stmt->rowCount() > 0) {
                echo "<h2>Reserva Encontrada:</h2>";
                echo "<table border='1'><tr><th>ID Reserva</th><th>ID Vuelo</th><th>ID Usuario</th><th>Fecha de Reserva</th><th>Precio</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . $row["idReserva"] . "</td
                    ><td>" . $row["idVuelo"] . "</td><td>" . $row["idUsuario"] . "</td><td>" . $row["fechaReserva"] . "</td><td>" . $row["precio"] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No se encontró ninguna reserva con el ID proporcionado.";
            }
        } catch (PDOException $e) {
            echo "Error al mostrar reserva: " . $e->getMessage();
        }
    } else {
        // Mostrar todas las reservas si el campo ID Reserva está vacío:
        mostrarReservas($pdo);
    }
}

// Formulario para modificar una reserva por ID:
echo "<h2>Modificar Reserva por ID:</h2>";
echo "<form method='post'>";
echo "ID Reserva: <input type='text' name='idReserva_modificar'>";
echo "<input type='submit' name='buscar_modificar_reserva' value='Buscar y Modificar Reserva'>";
echo "</form>";

// Formulario para eliminar una reserva por ID:
echo "<h2>Eliminar Reserva por ID:</h2>";
echo "<form method='post'>";
echo "ID Reserva: <input type='text' name='idReserva_eliminar'>";
echo "<input type='submit' name='eliminar_reserva' value='Eliminar Reserva'>";
echo "</form>";

// Lógica para procesar la solicitud de modificar una reserva por ID:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscar_modificar_reserva"])) {
    $idReserva_modificar = $_POST["idReserva_modificar"];
    try {
        $sql = "SELECT * FROM reservas WHERE idReserva=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idReserva_modificar]);
        if ($stmt->rowCount() > 0) {
            echo "<h2>Modificar Reserva:</h2>";
            echo "<form method='post'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "ID Reserva: <input type='text' name='idReserva' value='" . $row["idReserva"] . "' readonly><br>";
                echo "ID Vuelo: <input type='text' name='idVuelo' value='" . $row["idVuelo"] . "' required><br>";
                echo "ID Usuario: <input type='text' name='idUsuario' value='" . $row["idUsuario"] . "' required><br>";
                echo "Fecha de Reserva: <input type='date' name='fechaReserva' value='" . $row["fechaReserva"] . "' required><br>";
                echo "Precio: <input type='number' name='precio' value='" . $row["precio"] . "' required><br>";
            }
            echo "<input type='submit' name='modificar_reserva' value='Modificar Reserva'>";
            echo "</form>";
        } else {
            echo "No se encontró ninguna reserva con el ID proporcionado.";
        }
    } catch (PDOException $e) {
        echo "Error al buscar y modificar reserva: " . $e->getMessage();
    }
}
?>
</body>
</html>
