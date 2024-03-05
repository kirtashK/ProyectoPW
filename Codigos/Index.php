<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva de Vuelos</title>
</head>
<body>
    <h2>Formulario de Reserva de Vuelos</h2>
    <form action="procesar_reserva.php" method="post">
        
        <label for="origen">Origen:</label><br>
        <input type="text" id="origen" name="origen" required><br><br>
        
        <label for="destino">Destino:</label><br>
        <input type="text" id="destino" name="destino" required><br><br>
        
        <label for="fecha_salida">Fecha de Salida:</label><br>
        <input type="date" id="fecha_salida" name="fecha_salida" required><br><br>
        
        <!--Opcional-->
        <label for="fecha_regreso">Fecha de Regreso:</label><br>
        <input type="date" id="fecha_regreso" name="fecha_regreso"><br><br>
        
        <input type="submit" value="Consultar Vuelos">
    </form>
</body>
</html>
