<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('../avionadmi.jpg');
      
        }

        h1 {
            color: white;
            margin-bottom: 20px;
        }

        p {
            color: white;
            margin-bottom: 30px;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        button {
            background-color: #45a049;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
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
