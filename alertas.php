<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Alertas</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <header>
        <h1>Historial de Alertas</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="escanear.php">Escanear Inventarios</a>
            <a href="alertas.php" class="active">Historial de Alertas</a>
            <a href="configuraciones.php">Configuraciones</a>
        </nav>
    </header>
    <main>
        <h2>Alertas Generadas</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo de Alerta</th>
                    <th>Descripción</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
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

                // Consulta para obtener las alertas
                $sql = "SELECT tipo, descripcion, fecha_hora, estado FROM alertas ORDER BY fecha_hora DESC";
                $result = $conn->query($sql);

                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    // Mostrar cada fila de resultados
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['tipo']}</td>
                                <td>{$row['descripcion']}</td>
                                <td>{$row['fecha_hora']}</td>
                                <td><span class='status " . ($row['estado'] == 'Resuelta' ? 'ok' : 'alert') . "'>{$row['estado']}</span></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No hay alertas disponibles.</td></tr>";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Tu Empresa. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
