class DatabaseConnectionTest extends PHPUnit\Framework\TestCase {
    public function testConnection() {
        $conn = new mysqli("localhost", "root", "", "inventariosentry");
        $this->assertNotNull($conn);
        $this->assertFalse($conn->connect_error);
        $conn->close();
    }
}
