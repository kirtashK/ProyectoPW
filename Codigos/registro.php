<?php
require_once "config.php";

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];
    $fechaNacimiento = $_POST["fechaNacimiento"];

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT COUNT(*) AS count FROM usuarios WHERE correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['correo' => $correo]);
    $resultado_correo = $stmt->fetch();

    // Verificar si el DNI ya está registrado
    $sql = "SELECT COUNT(*) AS count FROM usuarios WHERE dni = :dni";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['dni' => $dni]);
    $resultado_dni = $stmt->fetch();

    // Verificar el formato del DNI (solo números)
    if (!is_numeric($dni)) {
        $mensaje_error .= "El DNI debe contener solo números.<br>";
    }

    // Verificar el formato del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error .= "El correo electrónico tiene un formato incorrecto.<br>";
    }

    // Verificar si hay duplicados en el correo o DNI
    if ($resultado_correo['count'] > 0 && $resultado_dni['count'] > 0) {
        $mensaje_error .= "El correo y el DNI ya están registrados.<br>";
    } elseif ($resultado_correo['count'] > 0) {
        $mensaje_error .= "El correo electrónico ya está registrado.<br>";
    } elseif ($resultado_dni['count'] > 0) {
        $mensaje_error .= "El DNI ya está registrado.<br>";
    }

    // Si no hay errores, insertar el nuevo usuario en la base de datos
    if (empty($mensaje_error)) {
        $rol = "usuario";

        $sql = "INSERT INTO usuarios (dni, nombre, apellidos, correo, contrasena, fechaNacimiento, rol) 
                VALUES (:dni, :nombre, :apellidos, :correo, :contrasena, :fechaNacimiento, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'dni' => $dni,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'contrasena' => $contrasena, // Asegúrate de hashear la contraseña antes de almacenarla en la base de datos!
            'fechaNacimiento' => $fechaNacimiento,
            'rol' => $rol
        ]);

        header("Location: InicioSesion.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Registro</title>
</head>
<body>
    <h2>Registro</h2>
    <?php if (!empty($mensaje_error)) : ?>
        <div style="color: red;"><?php echo $mensaje_error; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="dni">DNI:</label><br>
        <input type="text" id="dni" name="dni" required><br><br>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>
        <label for="apellidos">Apellidos:</label><br>
        <input type="text" id="apellidos" name="apellidos" required><br><br>
        <label for="correo">Correo electrónico:</label><br>
        <input type="email" id="correo" name="correo" required><br><br>
        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        <label for="fechaNacimiento">Fecha de Nacimiento:</label><br>
        <input type="date" id="fechaNacimiento" name="fechaNacimiento" required><br><br>
        <input type="submit" value="Registrarse">
    </form>
</body>
</html>