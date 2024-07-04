<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$role = $_SESSION['Role'];

if ($role !== 'customerManager') {
    echo "Unauthorized access.";
    exit();
}

$servername = "?";
$username = "?";
$password = "?";
$dbname = "?";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['customerEmail'])) {
        $customerEmail = $conn->real_escape_string($_POST['customerEmail']);

        $sql = "DELETE FROM Customers WHERE Email = '$customerEmail'";

        if ($conn->query($sql) === TRUE) {
            echo "Customer deleted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Customer Email is required.";
    }
}

$conn->close();
?>
