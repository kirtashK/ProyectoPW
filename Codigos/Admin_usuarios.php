<?php
require_once "config.php";

session_start();

if (!isset($_COOKIE["usuario_rol"]) || $_COOKIE["usuario_rol"] !== "admin") {
    die("Acceso denegado. Debes iniciar sesión como administrador para acceder a esta página.");
}

// Función para crear un nuevo usuario:
function crearUsuario($pdo, $dni, $contrasena, $rol, $nombre, $apellidos, $correo, $fechaNacimiento, $saldo) {
    try {
        $sql = "INSERT INTO usuarios (dni, contrasena, rol, nombre, apellidos, correo, fechaNacimiento, saldo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dni, $contrasena, $rol, $nombre, $apellidos, $correo, $fechaNacimiento, $saldo]);
        echo "Nuevo usuario creado con éxito.";
    } catch (PDOException $e) {
        echo "Error al crear el usuario: " . $e->getMessage();
    }
}

// Función para mostrar todos los usuarios:
function mostrarUsuarios($pdo) {
    try {
        $sql = "SELECT * FROM usuarios";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() > 0) {
            echo "<h2>Lista de Usuarios:</h2>";
            echo "<table border='1'><tr><th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Correo</th><th>Fecha de Nacimiento</th><th>Saldo</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>" . $row["dni"] . "</td><td>" . $row["nombre"] . "</td><td>" . $row["apellidos"] . "</td><td>" . $row["correo"] . "</td><td>" . $row["fechaNacimiento"] . "</td><td>" . $row["saldo"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No se encontraron usuarios.";
        }
    } catch (PDOException $e) {
        echo "Error al mostrar usuarios: " . $e->getMessage();
    }
}

// Función para modificar un usuario:
function modificarUsuario($pdo, $dni, $contrasena, $rol, $nombre, $apellidos, $correo, $fechaNacimiento, $saldo) {
    try {
        $sql = "UPDATE usuarios 
                SET contrasena=?, rol=?, nombre=?, apellidos=?, correo=?, fechaNacimiento=?, saldo=? 
                WHERE dni=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$contrasena, $rol, $nombre, $apellidos, $correo, $fechaNacimiento, $saldo, $dni]);
        echo "Usuario modificado con éxito.";
    } catch (PDOException $e) {
        echo "Error al modificar el usuario: " . $e->getMessage();
    }
}

// Función para eliminar un usuario:
function eliminarUsuario($pdo, $dni) {
    try {
        $sql = "DELETE FROM usuarios WHERE dni=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dni]);
        echo "Usuario eliminado con exito";
    } catch (PDOException $e) {
        echo "Error al eliminar el usuario: " . $e->getMessage();
    }
}

// Lógica para procesar las operaciones CRUD:

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué acción se ha solicitado:
    if (isset($_POST["crear_usuario"])) {
        // Procesar la creación de un nuevo usuario:
        crearUsuario($pdo, $_POST["dni"], $_POST["contrasena"], $_POST["rol"], $_POST["nombre"], $_POST["apellidos"], $_POST["correo"], $_POST["fechaNacimiento"], $_POST["saldo"]);
    } elseif (isset($_POST["modificar_usuario"])) {
        // Procesar la modificación de un usuario:
        modificarUsuario($pdo, $_POST["dni"], $_POST["contrasena"], $_POST["rol"], $_POST["nombre"], $_POST["apellidos"], $_POST["correo"], $_POST["fechaNacimiento"], $_POST["saldo"]);
    } elseif (isset($_POST["eliminar_usuario"])) {
        // Procesar la eliminación de un usuario:
        eliminarUsuario($pdo, $_POST["dni_eliminar"]);
    } elseif (isset($_POST["buscar_modificar_usuario"])) {
        // Procesar la búsqueda y modificación de un usuario:
        $dni_modificar = $_POST["dni_modificar"];
        try {
            $sql = "SELECT * FROM usuarios WHERE dni=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dni_modificar]);
            if ($stmt->rowCount() > 0) {
                echo "<h2>Modificar Usuario:</h2>";
                echo "<form method='post'>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "DNI: <input type='text' name='dni' value='" . $row["dni"] . "' readonly><br>";
                    echo "Contraseña: <input type='password' name='contrasena' value='" . $row["contrasena"] . "' required><br>";
                    echo "Rol: <input type='text' name='rol' value='" . $row["rol"] . "' required><br>";
                    echo "Nombre: <input type='text' name='nombre' value='" . $row["nombre"] . "' required><br>";
                    echo "Apellidos: <input type='text' name='apellidos' value='" . $row["apellidos"] . "' required><br>";
                    echo "Correo: <input type='email' name='correo' value='" . $row["correo"] . "' required><br>";
                    echo "Fecha de Nacimiento: <input type='date' name='fechaNacimiento' value='" . $row["fechaNacimiento"] . "' required><br>";
                    echo "Saldo: <input type='number' name='saldo' value='" . $row["saldo"] . "' required><br>";
                }
                echo "<input type='submit' name='modificar_usuario' value='Modificar Usuario'>";
                echo "</form>";
            } else {
                echo "No se encontró ningún usuario con el DNI proporcionado.";
            }
        } catch (PDOException $e) {
            echo "Error al buscar y modificar usuario: " . $e->getMessage();
        }
    }
}

// Mostrar el formulario para crear un nuevo usuario:
echo "<h2>Crear Nuevo Usuario:</h2>";
echo "<form method='post'>";
echo "DNI: <input type='text' name='dni' required><br>";
echo "Contraseña: <input type='password' name='contrasena' required><br>";
echo "Rol: <input type='text' name='rol' required><br>";
echo "Nombre: <input type='text' name='nombre' required><br>";
echo "Apellidos: <input type='text' name='apellidos' required><br>";
echo "Correo: <input type='email' name='correo' required><br>";
echo "Fecha de Nacimiento: <input type='date' name='fechaNacimiento' required><br>";
echo "Saldo: <input type='number' name='saldo' required><br>";
echo "<input type='submit' name='crear_usuario' value='Crear Usuario'>";
echo "</form>";

// Formulario para mostrar usuarios por DNI:
echo "<h2>Mostrar Usuario por DNI:</h2>";
echo "<form method='post'>";
echo "DNI: <input type='text' name='dni_busqueda'>";
echo "<input type='submit' name='mostrar_usuario' value='Mostrar Usuario'>";
echo "</form>";

// Formulario para modificar usuario por DNI:
echo "<h2>Modificar Usuario por DNI:</h2>";
echo "<form method='post'>";
echo "DNI: <input type='text' name='dni_modificar'>";
echo "<input type='submit' name='buscar_modificar_usuario' value='Buscar y Modificar Usuario'>";
echo "</form>";

// Formulario para eliminar usuario por DNI:
echo "<h2>Eliminar Usuario por DNI:</h2>";
echo "<form method='post'>";
echo "DNI: <input type='text' name='dni_eliminar'>";
echo "<input type='submit' name='eliminar_usuario' value='Eliminar Usuario'>";
echo "</form>";

// Mostrar la lista de usuarios existentes o el resultado de la búsqueda:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mostrar_usuario"])) {
    $dni_busqueda = $_POST["dni_busqueda"];
    if (!empty($dni_busqueda)) {
        // Mostrar el usuario con el DNI proporcionado:
        try {
            $sql = "SELECT * FROM usuarios WHERE dni=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dni_busqueda]);
            if ($stmt->rowCount() > 0) {
                echo "<h2>Usuario Encontrado:</h2>";
                echo "<table border='1'><tr><th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Correo</th><th>Fecha de Nacimiento</th><th>Saldo</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . $row["dni"] . "</td><td>" . $row["nombre"] . "</td><td>" . $row["apellidos"] . "</td><td>" . $row["correo"] . "</td><td>" . $row["fechaNacimiento"] . "</td><td>" . $row["saldo"] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No se encontró ningún usuario con el DNI proporcionado.";
            }
        } catch (PDOException $e) {
            echo "Error al mostrar usuario: " . $e->getMessage();
        }
    } else {
        // Mostrar todos los usuarios si el campo DNI está vacío:
        mostrarUsuarios($pdo);
    }
}


?>


