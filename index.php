<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual de Inventario</title>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #1E1E2E;
            color: #FFFFFF;
        }

        header {
            background-color: #0D1117;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0DB39E;
        }

        .user-options {
            display: flex;
            align-items: center;
        }

        .notification-icon, .user-icon {
            font-size: 20px;
            margin-left: 20px;
        }

        .main-container {
            display: flex;
        }

        .sidebar {
            background-color: #0D1117;
            width: 250px;
            padding: 20px;
        }

        .menu-item {
            display: block;
            width: 100%;
            background-color: #282A36;
            color: #FFFFFF;
            border: none;
            padding: 15px;
            margin-bottom: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        .menu-item:hover {
            background-color: #0DB39E;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .camera-feed, .inventory-summary, .statistics {
            margin-bottom: 20px;
        }

        .camera-view {
            background-color: #33334D;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .inventory-table th, .inventory-table td {
            border: 1px solid #555;
            padding: 10px;
            text-align: left;
        }

        .status.ok {
            color: #00FF00;
        }

        .status.low {
            color: #FF9900;
        }

        .statistics .stats-box {
            display: flex;
            justify-content: space-between;
        }

        .stat {
            background-color: #282A36;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .stat h3 {
            margin: 0;
            font-size: 18px;
        }

        .stat p {
            margin: 5px 0 0;
            font-size: 24px;
        }

        /* Chatbot Styles */
        .chatbot {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0D1117;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 300px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .chatbot h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #0DB39E;
        }

        .chatbot input {
            padding: 10px;
            border: none;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .chatbot button {
            background-color: #0DB39E;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .chatbot button:hover {
            background-color: #0BB39E;
        }

        #chat-log {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
            background-color: #282A36;
            padding: 10px;
            border-radius: 4px;
        }

        #chat-log div {
            margin-bottom: 5px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #1E1E2E;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .modal-content button {
            margin: 10px;
            padding: 10px 20px;
            background-color: #0DB39E;
            border: none;
            cursor: pointer;
            color: white;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">SENTRY BOT </div>
        <div class="user-options">
            <div class="notification-icon">🔔</div>
            <div class="user-icon">👤</div>
        </div>
    </header>

    <!-- Main Dashboard -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <button class="menu-item">Escanear Inventarios</button>
            <button class="menu-item" onclick="window.location.href='revision.php';">Revisión de Inventarios</button>
            <button class="menu-item" onclick="window.location.href='alertas.php';">Historial de Alertas</button>
            <button class="menu-item">Configuraciones</button>
        </aside>

        <!-- Content -->
        <section class="content">
            <!-- Camera Feed Section -->
            <div class="camera-feed">
                <h2>Camara en Tiempo Real</h2>
                <div class="camera-view">
                    <img src="placeholder-camera.png" alt="Cámara en vivo" id="camera-feed">
                </div>
            </div>

            <!-- Inventory Summary -->
            <div class="inventory-summary">
                <h2>Resumen de Inventarios</h2>
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = new mysqli('localhost', 'root', '', 'inventariosentry');

                        // Verificar conexión
                        if ($conn->connect_error) {
                            die("Conexión fallida: " . $conn->connect_error);
                        }

                        // Ejecutar la consulta para obtener el registro más reciente de cada producto
                        $sql = "
                        SELECT p.nombre, p.cantidad, p.estado
                        FROM productos p
                        INNER JOIN (
                            SELECT nombre, MAX(fecha_hora_captura) AS max_fecha
                            FROM productos
                            GROUP BY nombre
                        ) AS subquery ON p.nombre = subquery.nombre AND p.fecha_hora_captura = subquery.max_fecha
                        ORDER BY p.nombre;";

                        $result = $conn->query($sql);

                        // Verificar si hay resultados y mostrarlos en la tabla
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['nombre']}</td>
                                    <td>{$row['cantidad']}</td>
                                    <td><span class='status " . ($row['estado'] == 'OK' ? 'ok' : 'low') . "'>{$row['estado']}</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No hay datos disponibles</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Statistics Section -->
            <div class="statistics">
                <h2>Estadísticas</h2>
                <div class="stats-box">
                    <div class="stat">
                        <h3>Stock Total</h3>
                        <p>2500 Items</p>
                    </div>
                    <div class="stat">
                        <h3>Productos Detectados</h3>
                        <p>124</p>
                    </div>
                    <div class="stat">
                        <h3>Alertas de Fraude</h3>
                        <p>3</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Chatbot Section -->
    <div class="chatbot">
        <h2>Sentry bot</h2>
        <div id="chat-log"></div>
        <input type="text" id="user-input" placeholder="Escribe tu pregunta...">
        <button id="send-button">Enviar</button>
    </div>

    <script>
        const chatLog = document.getElementById("chat-log");
        const userInput = document.getElementById("user-input");
        const sendButton = document.getElementById("send-button");

        // Respuestas predefinidas
        const responses = {
            "¿Cuál es el estado de los productos?": "Revisando... Los productos están en buen estado.",
            "¿Hay productos que deben ser reabastecidos?": "Sí, algunos productos tienen bajo stock. Te recomendaría reabastecer.",
            "¿Qué productos están disponibles?": "Los productos disponibles son A, B, C, D, E y F.",
            "¿Cuál es la cantidad de producto A?": "La cantidad de producto A es 50 unidades.",
            "¿Cuál es la cantidad de producto B?": "La cantidad de producto B es 30 unidades.",
            "¿Cuál es la cantidad de producto C?": "La cantidad de producto C es 70 unidades.",
            "¿Cuál es la cantidad de producto D?": "La cantidad de producto D es 20 unidades.",
            "¿Cuál es la cantidad de producto E?": "La cantidad de producto E es 15 unidades.",
            "¿Cuál es la cantidad de producto F?": "La cantidad de producto F es 5 unidades.",
            "¿Cuándo fue la última actualización del inventario?": "El inventario fue actualizado recientemente. Verifique los detalles en la sección de revisión.",
            "¿Qué hacer si hay un producto con stock bajo?": "Te sugeriría que reabastezcas el producto lo más pronto posible.",
            "¿Cómo puedo ver el historial de inventario?": "Puedes ver el historial en la sección de revisión de inventarios.",
            "¿Hay alguna alerta en el inventario?": "Sí, hay 3 alertas de fraude en el inventario.",
            "¿Qué productos tienen más stock?": "Los productos C y A tienen el mayor stock.",
            "¿Cuál es la tendencia del stock?": "Actualmente, el stock de la mayoría de los productos está en tendencia a disminuir.",
            "¿Hay alguna nueva llegada de productos?": "No hay nuevas llegadas en este momento.",
            "¿Cuánto tiempo tardará el nuevo pedido?": "El nuevo pedido puede tardar de 3 a 5 días hábiles.",
            "¿Cuándo se deben hacer pedidos?": "Se deben hacer pedidos cuando el stock esté por debajo de 20 unidades.",
            "¿Qué productos son más vendidos?": "Los productos A y B son los más vendidos.",
            "¿Cómo puedo contactar a soporte?": "Puedes contactar a soporte a través del correo electrónico de la empresa.",
            "¿Dónde puedo encontrar la lista completa de productos?": "La lista completa de productos está disponible en la sección de inventarios.",
            "¿Hay productos fuera de stock?": "Sí, el producto F está actualmente fuera de stock."
        };

        sendButton.addEventListener("click", function() {
            const userText = userInput.value;
            if (userText.trim() === "") return;

            // Añadir el texto del usuario al log del chat
            const userMessage = document.createElement("div");
            userMessage.textContent = `Tú: ${userText}`;
            chatLog.appendChild(userMessage);
            userInput.value = "";

            // Responder según la pregunta
            const botResponse = responses[userText] || "Lo siento, no entiendo la pregunta. Por favor, inténtalo de nuevo.";
            const botMessage = document.createElement("div");
            botMessage.textContent = `Bot: ${botResponse}`;
            chatLog.appendChild(botMessage);
            chatLog.scrollTop = chatLog.scrollHeight; // Desplazar hacia abajo
        });
    </script>
</body>
</html>
