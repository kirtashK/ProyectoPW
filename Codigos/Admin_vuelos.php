<?php
require_once "config.php";

session_start();

if (!isset($_COOKIE["usuario_rol"]) || $_COOKIE["usuario_rol"] !== "admin") {
    die("Acceso denegado. Debes iniciar sesión como administrador para acceder a esta página.");
}

// Función para crear un nuevo vuelo:
function crearVuelo($pdo, $origen, $destino, $fecha, $hora_salida, $aerolinea, $capacidad, $precio) {
    try {
        $sql = "INSERT INTO vuelos (origen, destino, fecha, hora_salida, aerolinea, capacidad, precio) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$origen, $destino, $fecha, $hora_salida, $aerolinea, $capacidad, $precio]);
        echo "Nuevo vuelo creado con éxito.";
    } catch (PDOException $e) {
        echo "Error al crear el vuelo: " . $e->getMessage();
    }
}

// Función para mostrar todos los vuelos:
function mostrarVuelos($pdo) {
    try {
        $sql = "SELECT * FROM vuelos";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() > 0) {
            echo "<h2>Lista de Vuelos:</h2>";
            echo "<table border='1'><tr><th>ID</th><th>Origen</th><th>Destino</th><th>Fecha</th><th>Hora de Salida</th><th>Aerolínea</th><th>Capacidad</th><th>Precio</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["origen"] . "</td><td>" . $row["destino"] . "</td><td>" . $row["fecha"] . "</td><td>" . $row["hora_salida"] . "</td><td>" . $row["aerolinea"] . "</td><td>" . $row["capacidad"] . "</td><td>" . $row["precio"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No se encontraron vuelos.";
        }
    } catch (PDOException $e) {
        echo "Error al mostrar vuelos: " . $e->getMessage();
    }
}

// Función para modificar un vuelo:
function modificarVuelo($pdo, $id, $origen, $destino, $fecha, $hora_salida, $aerolinea, $capacidad, $precio) {
    try {
        $sql = "UPDATE vuelos 
                SET origen=?, destino=?, fecha=?, hora_salida=?, aerolinea=?, capacidad=?, precio=? 
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$origen, $destino, $fecha, $hora_salida, $aerolinea, $capacidad, $precio, $id]);
        echo "Vuelo modificado con éxito.";
    } catch (PDOException $e) {
        echo "Error al modificar el vuelo: " . $e->getMessage();
    }
}

// Función para eliminar un vuelo:
// Función para eliminar un vuelo y las reservas asociadas:
function eliminarVuelo($pdo, $id) {
    try {
        $pdo->beginTransaction(); // Comenzar transacción
        
        // Eliminar reservas asociadas al vuelo
        $sql_reservas = "DELETE FROM reservas WHERE idVuelo=?";
        $stmt_reservas = $pdo->prepare($sql_reservas);
        $stmt_reservas->execute([$id]);
        
        // Eliminar el vuelo
        $sql_vuelo = "DELETE FROM vuelos WHERE id=?";
        $stmt_vuelo = $pdo->prepare($sql_vuelo);
        $stmt_vuelo->execute([$id]);
        
        $pdo->commit(); // Confirmar transacción
        echo "Vuelo y reservas asociadas eliminados con éxito";
    } catch (PDOException $e) {
        $pdo->rollBack(); // Revertir transacción en caso de error
        echo "Error al eliminar el vuelo y las reservas asociadas: " . $e->getMessage();
    }
}


// Lógica para procesar las operaciones CRUD:

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué acción se ha solicitado:
    if (isset($_POST["crear_vuelo"])) {
        // Procesar la creación de un nuevo vuelo:
        crearVuelo($pdo, $_POST["origen"], $_POST["destino"], $_POST["fecha"], $_POST["hora_salida"], $_POST["aerolinea"], $_POST["capacidad"], $_POST["precio"]);
    } elseif (isset($_POST["modificar_vuelo"])) {
        // Procesar la modificación de un vuelo:
        modificarVuelo($pdo, $_POST["id"], $_POST["origen"], $_POST["destino"], $_POST["fecha"], $_POST["hora_salida"], $_POST["aerolinea"], $_POST["capacidad"], $_POST["precio"]);
    } elseif (isset($_POST["eliminar_vuelo"])) {
        // Procesar la eliminación de un vuelo:
        eliminarVuelo($pdo, $_POST["id_eliminar"]);
    }
}

// Mostrar el formulario para crear un nuevo vuelo:
echo "<h2>Crear Nuevo Vuelo:</h2>";
echo "<form method='post'>";
echo "Origen: <input type='text' name='origen' required><br>";
echo "Destino: <input type='text' name='destino' required><br>";
echo "Fecha: <input type='date' name='fecha' required><br>";
echo "Hora de Salida: <input type='time' name='hora_salida' required><br>";
echo "Aerolínea: <input type='text' name='aerolinea' required><br>";
echo "Capacidad: <input type='number' name='capacidad' required><br>";
echo "Precio: <input type='number' name='precio' required><br>";
echo "<input type='submit' name='crear_vuelo' value='Crear Vuelo'>";
echo "</form>";
// Formulario para mostrar vuelos por ID:
echo "<h2>Mostrar Vuelo por ID:</h2>";
echo "<form method='post'>";
echo "ID del Vuelo: <input type='number' name='id_vuelo'>";
echo "<input type='submit' name='mostrar_vuelo' value='Mostrar Vuelo'>";
echo "</form>";

// Lógica para mostrar vuelos por ID o todos si no se proporciona ID:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mostrar_vuelo"])) {
    $id_vuelo = $_POST["id_vuelo"];
    try {
        if ($id_vuelo !== null) {
            $sql = "SELECT * FROM vuelos WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_vuelo]);
        } else {
            $sql = "SELECT * FROM vuelos";
            $stmt = $pdo->query($sql);
        }
        
        if ($stmt->rowCount() > 0) {
            echo "<h2>Lista de Vuelos:</h2>";
            echo "<table border='1'><tr><th>ID</th><th>Origen</th><th>Destino</th><th>Fecha</th><th>Hora de Salida</th><th>Aerolínea</th><th>Capacidad</th><th>Precio</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["origen"] . "</td><td>" . $row["destino"] . "</td><td>" . $row["fecha"] . "</td><td>" . $row["hora_salida"] . "</td><td>" . $row["aerolinea"] . "</td><td>" . $row["capacidad"] . "</td><td>" . $row["precio"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No se encontraron vuelos.";
        }
    } catch (PDOException $e) {
        echo "Error al mostrar vuelos: " . $e->getMessage();
    }
}
// Formulario para modificar vuelo por ID:
    echo "<h2>Modificar Vuelo por ID:</h2>";
    echo "<form method='post'>";
    echo "ID del Vuelo: <input type='number' name='id_modificar'>";
    echo "<input type='submit' name='buscar_modificar_vuelo' value='Buscar y Modificar Vuelo'>";
    echo "</form>";
    
    
    
    // Procesar la búsqueda y modificación de un vuelo:
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscar_modificar_vuelo"])) {
        $id_modificar = $_POST["id_modificar"];
        try {
            $sql = "SELECT * FROM vuelos WHERE id=?";
           
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_modificar]);
            if ($stmt->rowCount() > 0) {
                echo "<h2>Modificar Vuelo:</h2>";
                echo "<form method='post'>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "ID: <input type='text' name='id' value='" . $row["id"] . "' readonly><br>";
                    echo "Origen: <input type='text' name='origen' value='" . $row["origen"] . "' required><br>";
                    echo "Destino: <input type='text' name='destino' value='" . $row["destino"] . "' required><br>";
                    echo "Fecha: <input type='date' name='fecha' value='" . $row["fecha"] . "' required><br>";
                    echo "Hora de Salida: <input type='time' name='hora_salida' value='" . $row["hora_salida"] . "' required><br>";
                    echo "Aerolínea: <input type='text' name='aerolinea' value='" . $row["aerolinea"] . "' required><br>";
                    echo "Capacidad: <input type='number' name='capacidad' value='" . $row["capacidad"] . "' required><br>";
                    echo "Precio: <input type='number' name='precio' value='" . $row["precio"] . "' required><br>";
                }
                echo "<input type='submit' name='modificar_vuelo' value='Modificar Vuelo'>";
                echo "</form>";
            } else {
                echo "No se encontró ningún vuelo con el ID proporcionado.";
            }
        } catch (PDOException $e) {
            echo "Error al buscar y modificar vuelo: " . $e->getMessage();
        }
    }

// Formulario para eliminar vuelo por ID:
echo "<h2>Eliminar Vuelo por ID:</h2>";
echo "<form method='post'>";
echo "ID del Vuelo: <input type='number' name='id_eliminar'>";
echo "<input type='submit' name='eliminar_vuelo' value='Eliminar Vuelo'>";
echo "</form>";



?>
