<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost"; // or your server name
$username = "root"; // your username
$password = ""; // your password
$dbname = "Ticketing_system"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = isset($_POST['role']) ? $_POST['role'] : null;
    $Name = isset($_POST['Name']) ? $_POST['Name'] : null;
    $Surname = isset($_POST['Surname']) ? $_POST['Surname'] : null;
    $Gender = isset($_POST['Gender']) ? $_POST['Gender'] : null;
    $Email = isset($_POST['Email']) ? $_POST['Email'] : null;
    $Telephone = isset($_POST['Telephone']) ? $_POST['Telephone'] : null;
    $LoginPasscode = isset($_POST['LoginPasscode']) ? password_hash($_POST['LoginPasscode'], PASSWORD_DEFAULT) : null;

    if ($role === 'customerManager') {
        $CustomerManagerID = isset($_POST['CustomerManagerID']) ? $_POST['CustomerManagerID'] : null;
        $Department = isset($_POST['Department']) ? $_POST['Department'] : null;

        if ($CustomerManagerID && $Name && $Surname && $Gender && $Email && $Telephone && $Department && $LoginPasscode) {
            $sql = "INSERT INTO CustomerManager (CustomerManagerID, Name, Surname, Gender, Email, Telephone, Department, LoginPasscode) 
                    VALUES ('$CustomerManagerID', '$Name', '$Surname', '$Gender', '$Email', '$Telephone', '$Department', '$LoginPasscode')";

            if ($conn->query($sql) === TRUE) {
                echo "New customer manager record created successfully";
                header("Location: login.html");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "All fields are required.";
        }
    } elseif ($role === 'customer') {
        $CustomerID = isset($_POST['CustomerID']) ? $_POST['CustomerID'] : null;

        if ($CustomerID && $Name && $Surname && $Gender && $Email && $Telephone && $LoginPasscode) {
            $sql = "INSERT INTO Customers (CustomerID, Name, Surname, Gender, Email, Telephone, LoginPasscode) 
                    VALUES ('$CustomerID', '$Name', '$Surname', '$Gender', '$Email', '$Telephone', '$LoginPasscode')";

            if ($conn->query($sql) === TRUE) {
                echo "New customer record created successfully";
                header("Location: login.html");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "All fields are required.";
        }
    } else {
        echo "Invalid role.";
    }
}

$conn->close();
?>
