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
    if (isset($_POST['customerID'], $_POST['customerName'], $_POST['customerSurname'])) {
        $customerID = $conn->real_escape_string($_POST['customerID']);
        $customerName = $conn->real_escape_string($_POST['customerName']);
        $customerSurname = $conn->real_escape_string($_POST['customerSurname']);
        
        $fieldsToUpdate = [];
        if (!empty($_POST['customerGender'])) {
            $fieldsToUpdate[] = "Gender = '" . $conn->real_escape_string($_POST['customerGender']) . "'";
        }
        if (!empty($_POST['customerTelephone'])) {
            $fieldsToUpdate[] = "Telephone = '" . $conn->real_escape_string($_POST['customerTelephone']) . "'";
        }
        if (!empty($_POST['customerEmail'])) {
            $fieldsToUpdate[] = "Email = '" . $conn->real_escape_string($_POST['customerEmail']) . "'";
        }
        if (!empty($_POST['customerPassword'])) {
            $fieldsToUpdate[] = "LoginPasscode = '" . password_hash($_POST['customerPassword'], PASSWORD_DEFAULT) . "'";
        }

        if (!empty($fieldsToUpdate)) {
            $sql = "UPDATE Customers SET " . implode(", ", $fieldsToUpdate) . " WHERE CustomerID = '$customerID' AND Name = '$customerName' AND Surname = '$customerSurname'";

            if ($conn->query($sql) === TRUE) {
                echo "Customer updated successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "No fields to update.";
        }
    } else {
        echo "Customer ID, Name, and Surname are required.";
    }
}

$conn->close();
?>
