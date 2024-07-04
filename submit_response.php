<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

if (!isset($_SESSION['CustomerManagerID'])) {
    echo "CustomerManagerID is not set in the session.";
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
    $ticketID = $conn->real_escape_string($_POST['ticketID']);
    $responseText = $conn->real_escape_string($_POST['responseText']);
    $customerManagerID = $_SESSION['CustomerManagerID'];

    // Check if the CustomerManagerID exists in the CustomerManagers table
    $checkManagerSql = "SELECT CustomerManagerID FROM CustomerManager WHERE CustomerManagerID = '$customerManagerID'";
    $checkManagerResult = $conn->query($checkManagerSql);

    if ($checkManagerResult->num_rows == 0) {
        echo "Invalid CustomerManagerID.";
        exit();
    }

    // Check if the ticket is already closed
    $checkTicketSql = "SELECT Status FROM Ticket_Details WHERE ID = '$ticketID'";
    $checkTicketResult = $conn->query($checkTicketSql);
    if ($checkTicketResult->num_rows > 0) {
        $ticketStatus = $checkTicketResult->fetch_assoc()['Status'];

        if ($ticketStatus == 0) {
            echo "This ticket is already closed and cannot be responded to.";
            exit();
        }
    } else {
        echo "Invalid TicketID.";
        exit();
    }

    // Insert the response into Ticket_Threads
    $sql = "INSERT INTO Ticket_Threads (TicketID, CustomerManagerID, ResponseText, ResponseDate) 
            VALUES ('$ticketID', '$customerManagerID', '$responseText', NOW())";

    if ($conn->query($sql) === TRUE) {
        // Update the ticket status to closed (assuming 0 is closed and 1 is open)
        $updateStatusSql = "UPDATE Ticket_Details SET Status = 0 WHERE ID = '$ticketID'";
        if ($conn->query($updateStatusSql) === TRUE) {
            header("Location: ticket_details.php?id=$ticketID");
            exit();
        } else {
            echo "Error updating ticket status: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
