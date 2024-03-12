<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require_once "config.php";

    if(isset($_POST["acceder"])) {
        // Recuperar los datos del formulario de inicio de sesión
        $correo = $_POST["correo"];
        $contrasena = $_POST["contrasena"];

        // Preparar la consulta SQL para buscar al usuario en la base de datos
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        // Verificar si se encontró un usuario con el correo proporcionado
        if ($usuario) {
            // Verificar la contraseña
            if ($contrasena == $usuario['contrasena']) {
                // Iniciar sesión
                $_SESSION["correo"] = $usuario["correo"];
                
                // Establecer la cookie con el ID de usuario y rol
                setcookie("usuario_id", $usuario["dni"], time() + (86400 * 30), "/"); // Cookie válida por 30 días
                setcookie("usuario_rol", $usuario["rol"], time() + (86400 * 30), "/");

                // Redirigir al usuario a la página de inicio
                header("Location: Index.php");
                exit();
            } else {
                // Contraseña incorrecta
                $error = "Contraseña incorrecta";
            }
        } else {
            // Usuario no encontrado
            $error = "El correo electrónico no está registrado";
        }
    } elseif(isset($_POST["registrarse"])) {
        // Redirigir al usuario a la página de registro
        header("Location: registro.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="correo">Correo electrónico:</label><br>
        <input type="email" id="correo" name="correo" required><br><br>
        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        <input type="submit" name="acceder" value="Acceder">
    </form>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="submit" name="registrarse" value="Registrarse">
    </form>
</body>
</html>