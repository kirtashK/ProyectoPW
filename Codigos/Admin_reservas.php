<?php
require_once "config.php";

session_start();

if (!isset($_COOKIE["usuario_rol"]) || $_COOKIE["usuario_rol"] !== "admin") {
    die("Acceso denegado. Debes iniciar sesión como administrador para acceder a esta página.");
}

// Función para crear una nueva reserva:
function crearReserva($pdo, $idVuelo, $idUsuario, $fechaReserva, $precio) {
    try {
        $sql = "INSERT INTO reservas (idVuelo, idUsuario, fechaReserva, precio) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idVuelo, $idUsuario, $fechaReserva, $precio]);
        echo "Nueva reserva creada con éxito.";
    } catch (PDOException $e) {
        echo "Error al crear la reserva: " . $e->getMessage();
    }
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

// Función para eliminar una reserva:
function eliminarReserva($pdo, $idReserva) {
    try {
        $sql = "DELETE FROM reservas WHERE idReserva=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idReserva]);
        echo "Reserva eliminada con éxito.";
    } catch (PDOException $e) {
        echo "Error al eliminar la reserva: " . $e->getMessage();
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
                    echo "<tr><td>" . $row["idReserva"] . "</td><td>" . $row["idVuelo"] . "</td><td>" . $row["idUsuario"] . "</td><td>" . $row["fechaReserva"] . "</td><td>" . $row["precio"] . "</td></tr>";
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


       
