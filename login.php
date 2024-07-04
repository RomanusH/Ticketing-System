<?php
session_start();

$servername = "?";
$username = "?";
$password = "?";
$dbname = "?";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Email = isset($_POST['Email']) ? $_POST['Email'] : null;
    $LoginPasscode = isset($_POST['LoginPasscode']) ? $_POST['LoginPasscode'] : null;
    $Role = isset($_POST['role']) ? $_POST['role'] : null;

    if ($Email && $LoginPasscode && $Role) {
        $Email = $conn->real_escape_string($Email);
        $LoginPasscode = $conn->real_escape_string($LoginPasscode);

        if ($Role === 'customer') {
            $sql = "SELECT * FROM Customers WHERE Email = '$Email'";
        } elseif ($Role === 'customerManager') {
            $sql = "SELECT * FROM CustomerManager WHERE Email = '$Email'";
        } else {
            echo "Invalid role selected.";
            exit();
        }

        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($LoginPasscode, $row['LoginPasscode'])) {
                $_SESSION['loggedIn'] = true;
                $_SESSION['Name'] = $row['Name'];
                $_SESSION['Email'] = $row['Email'];
                $_SESSION['Telephone'] = $row['Telephone'];
                $_SESSION['Role'] = $Role;

                if ($Role === 'customer') {
                    $_SESSION['CustomerID'] = $row['CustomerID'];
                } elseif ($Role === 'customerManager') {
                    $_SESSION['CustomerManagerID'] = $row['CustomerManagerID'];
                }

                header("Location: ticketing.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User not found.";
        }
    } else {
        echo "Email, password, and role are required.";
    }
}

$conn->close();
?>
