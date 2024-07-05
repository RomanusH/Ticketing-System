<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "?"; // or your server name
$username = "?"; // your username
$password = "?"; // your password
$dbname = "?"; // your database name

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
        // Generate CustomerID
        $sql = "SELECT MAX(CustomerID) AS max_id FROM Customers";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'];
        
        if ($max_id) {
            $max_id_num = (int)str_replace('CST-', '', $max_id);
            $new_id_num = $max_id_num + 1;
        } else {
            $new_id_num = 1;
        }

        $CustomerID = 'CST-' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT);

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
