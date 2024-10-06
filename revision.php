<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión de Inventarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Revisión de Inventarios</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="escanear.php">Escanear Inventarios</a>
            <a href="alertas.php">Historial de Alertas</a>
            <a href="configuraciones.php">Configuraciones</a>
        </nav>
    </header>
    <main>
        <h2>Productos en Inventario</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Fecha y Hora de Captura</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Conectar a la base de datos
                $conn = new mysqli("localhost", "root", "", "inventariosentry");

                // Verificar conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener los productos
                $sql = "SELECT nombre, cantidad, estado, fecha_hora_captura FROM productos ORDER BY fecha_hora_captura DESC";
                $result = $conn->query($sql);

                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    // Mostrar cada fila de resultados
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['nombre']}</td>
                                <td>{$row['cantidad']}</td>
                                <td><span class='status " . ($row['estado'] == 'OK' ? 'ok' : 'low') . "'>{$row['estado']}</span></td>
                                <td>{$row['fecha_hora_captura']}</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No hay productos disponibles.</td></tr>";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
