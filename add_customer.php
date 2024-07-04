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
    if (isset($_POST['customerID'], $_POST['customerName'], $_POST['customerSurname'], $_POST['customerGender'], $_POST['customerTelephone'], $_POST['customerEmail'], $_POST['customerPassword'])) {
        $customerID = $conn->real_escape_string($_POST['customerID']);
        $customerName = $conn->real_escape_string($_POST['customerName']);
        $customerSurname = $conn->real_escape_string($_POST['customerSurname']);
        $customerGender = $conn->real_escape_string($_POST['customerGender']);
        $customerTelephone = $conn->real_escape_string($_POST['customerTelephone']);
        $customerEmail = $conn->real_escape_string($_POST['customerEmail']);
        $customerPassword = password_hash($_POST['customerPassword'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO Customers (CustomerID, Name, Surname, Gender, Telephone, Email, LoginPasscode) VALUES ('$customerID', '$customerName', '$customerSurname', '$customerGender', '$customerTelephone', '$customerEmail', '$customerPassword')";

        if ($conn->query($sql) === TRUE) {
            echo "Customer added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>
