<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/Applications/XAMPP/xamppfiles/htdocs/Proje/PHPMailer/src/Exception.php';
require '/Applications/XAMPP/xamppfiles/htdocs/Proje/PHPMailer/src/PHPMailer.php';
require '/Applications/XAMPP/xamppfiles/htdocs/Proje/PHPMailer/src/SMTP.php';

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
        $Department = isset($_POST['Department']) ? $_POST['Department'] : null;

        // Generate the next CustomerManagerID
        $result = $conn->query("SELECT COUNT(*) AS total FROM CustomerManager");
        $row = $result->fetch_assoc();
        $nextManagerId = 'MNG-' . str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);

        if ($Name && $Surname && $Gender && $Email && $Telephone && $Department && $LoginPasscode) {
            $sql = "INSERT INTO CustomerManager (CustomerManagerID, Name, Surname, Gender, Email, Telephone, Department, LoginPasscode) 
                    VALUES ('$nextManagerId', '$Name', '$Surname', '$Gender', '$Email', '$Telephone', '$Department', '$LoginPasscode')";

            if ($conn->query($sql) === TRUE) {
                sendWelcomeEmail($Email, $Name);
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
        $result = $conn->query("SELECT COUNT(*) AS total FROM Customers");
        $row = $result->fetch_assoc();
        $nextCustomerId = 'CST-' . str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);

        if ($Name && $Surname && $Gender && $Email && $Telephone && $LoginPasscode) {
            $sql = "INSERT INTO Customers (CustomerID, Name, Surname, Gender, Email, Telephone, LoginPasscode) 
                    VALUES ('$nextCustomerId', '$Name', '$Surname', '$Gender', '$Email', '$Telephone', '$LoginPasscode')";

            if ($conn->query($sql) === TRUE) {
                sendWelcomeEmail($Email, $Name);
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

function sendWelcomeEmail($to, $name) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'xxxx@gmail.com'; // Replace with your Gmail address
        $mail->Password = 'xxx'; // Replace with your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = ???;

        // Character encoding
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Recipients
        $mail->setFrom('xxxx@gmail.com', 'LCN Group');
        $mail->addAddress($to, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Hoş geldiniz';
        $mail->Body    = "Merhaba $name,<br><br>Ticketing sistemimize hoş geldiniz. Ürünümüzle ilgili sorularınızı sormaktan çekinmeyin.<br><br>Saygılarımızla,<br>LCN Group";

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
