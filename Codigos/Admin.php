<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Bienvenido al Panel de Administrador</h1>
    <p>Este es el menú principal para el administrador. Seleccione una de las siguientes opciones:</p>
    
    <form action="admin_usuarios.php" method="get">
        <button type="submit" name="administrar_usuarios">Administrar Usuarios</button>
    </form>
    
    <form action="admin_reservas.php" method="get">
        <button type="submit" name="administrar_reservas">Administrar Reservas</button>
    </form>
    
    <form action="admin_vuelos.php" method="get">
        <button type="submit" name="administrar_vuelos">Administrar Vuelos</button>
    </form>
</body>
</html>
