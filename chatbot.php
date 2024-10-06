<?php
// Configura la conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "inventariosentry");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el mensaje del usuario
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'];

// Consulta a la base de datos para obtener información sobre el inventario
$sql = "SELECT nombre, cantidad, estado FROM productos WHERE nombre LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $message . "%"; // Búsqueda con comodines
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$response = "No encontré información sobre eso.";

// Si hay resultados, generar la respuesta
if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = "{$row['nombre']}: {$row['cantidad']} en stock, Estado: {$row['estado']}";
    }
    $response = implode("\n", $products);
}

// Cerrar conexión
$stmt->close();
$conn->close();

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode(['response' => $response]);
?>
