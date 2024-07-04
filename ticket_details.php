<?php
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No ticket ID provided.";
    exit();
}

$ticketID = $_GET['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Ticketing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Ticket_Details WHERE ID = $ticketID";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "No ticket found with ID $ticketID.";
    $conn->close();
    exit();
}

$ticket = $result->fetch_assoc();
$isClosed = $ticket['Status'] == 0; // Assuming 0 means closed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - Support Ticket System</title>
    <link rel="stylesheet" href="ticket_details.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Support Ticket System</h1>
            <nav>
                <ul>
                    <li><a href="ticketing.php">Ticket Gönder</a></li>
                    <li><a href="list_tickets.php">Ticketleri Gör</a></li>
                    <li><a href="logout.php">Çıkış Yap</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Ticket Detayları</h2>
            <table>
                <tr><th>ID</th><td><?php echo $ticket['ID']; ?></td></tr>
                <tr><th>Gönderilme Tarihi</th><td><?php echo $ticket['CreateDate']; ?></td></tr>
                <tr><th>Başlık</th><td><?php echo $ticket['Context']; ?></td></tr>
                <tr><th>Önemlilik Durumu</th><td><?php echo $ticket['Priority']; ?></td></tr>
                <tr><th>Konu</th><td><?php echo $ticket['Description']; ?></td></tr>
                <tr><th>Belgeler</th><td><?php echo $ticket['AttachedDocuments'] ? "<a href='uploaded_files/" . $ticket['AttachedDocuments'] . "' target='_blank'>View Document</a>" : "No document attached"; ?></td></tr>
            </table>
            <h3>Cevaplar</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Numarası</th>
                        <th>Cevap</th>
                        <th>Cevap Verildiği Zaman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $responseSql = "SELECT * FROM Ticket_Threads WHERE TicketID = $ticketID";
                    $responseResult = $conn->query($responseSql);

                    if ($responseResult->num_rows > 0) {
                        while ($responseRow = $responseResult->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $responseRow['ID'] . "</td>";
                            echo "<td>" . $responseRow['UserID'] . "</td>";
                            echo "<td>" . $responseRow['ResponseText'] . "</td>";
                            echo "<td>" . $responseRow['ResponseDate'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No responses found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php if (!$isClosed): ?>
            <h3>Cevapı Gönder</h3>
            <form action="submit_response.php" method="post">
                <input type="hidden" name="ticketID" value="<?php echo $ticketID; ?>">
                <div class="form-group">
                    <label for="responseText">Cevap:</label>
                    <textarea id="responseText" name="responseText" required></textarea>
                </div>
                <button type="submit">Gönder</button>
            </form>
            <?php else: ?>
            <p>Bu ticket kapanmıştır ve artık cevap verilemez.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Support Ticket System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php
$conn->close();
?>
