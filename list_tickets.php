<?php
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Ticketing_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket List - Support Ticket System</title>
    <link rel="stylesheet" href="list_tickets.css">
    <link rel="stylesheet" href="navbar.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="header">  
        <div class="header__logo">
            <strong>LOGO</strong>
        </div>
        <nav class="navbar">
            <ul class="navbar__menu">
                <li class="navbar__item">
                    <a href="ticketing.php" class="navbar__link"><i data-feather="home"></i><span>Ana Sayfa</span> </a>
                </li>
                <li class="navbar__item">
                    <a href="list_tickets.php" class="navbar__link"><i data-feather="message-square"></i><span>Ticketleri Gör</span></a>        
                </li>
                <li class="navbar__item">
                    <a href="#" class="navbar__link"><i data-feather="users"></i><span>Heasbım</span></a>  
                </li>
                <li class="navbar__item">
                    <a href="logout.php" class="navbar__link"><i data-feather="settings"></i><span>Çıkış</span></a>        
                </li>
            </ul>
        </nav>
    </div>
    <main>
        <div class="container">
            <h2>Gönderilmiş Ticketler</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gönderilme Tarihi</th>
                        <th>Kullanıcı Numarası</th>
                        <th>Başlık</th>
                        <th>Önemlilik Durumu</th>
                        <th>Belgeler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM Ticket_Details";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><a href='ticket_details.php?id=" . $row['ID'] . "'>" . $row['ID'] . "</a></td>";
                            echo "<td>" . $row['CreateDate'] . "</td>";
                            echo "<td>" . $row['CustomerID'] . "</td>";
                            echo "<td>" . $row['Context'] . "</td>";
                            echo "<td>" . $row['Priority'] . "</td>";
                            if ($row['AttachedDocuments']) {
                                echo "<td><a href='uploaded_files/" . $row['AttachedDocuments'] . "' target='_blank'>Belgeye göz at</a></td>";
                            } else {
                                echo "<td>Eklenen belge bulunmamaktadır.</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No tickets found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        feather.replace();
    </script>
    <footer>
        <div class="container">
            <p>&copy; 2024 Support Ticket System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
