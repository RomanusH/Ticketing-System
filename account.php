<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$role = $_SESSION['Role'];
$customerID = $_SESSION['CustomerID'] ?? null;
$customerManagerID = $_SESSION['CustomerManagerID'] ?? null;

$servername = "?";
$username = "?";
$password = "?";
$dbname = "?";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userData = null;

if ($role === 'customer' && $customerID) {
    $sql = "SELECT * FROM Customers WHERE CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $customerID); // Changed to 's' for string
} elseif ($role === 'customerManager' && $customerManagerID) {
    $sql = "SELECT * FROM CustomerManagers WHERE CustomerManagerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $customerManagerID); // Changed to 's' for string
}

if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information - Support Ticket System</title>
    <link rel="stylesheet" href="account.css">
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
                    <a href="ticketing.php" class="navbar__link"><i data-feather="home"></i><span>Ana Sayfa</span></a>
                </li>
                <li class="navbar__item">
                    <a href="list_tickets.php" class="navbar__link"><i data-feather="message-square"></i><span>Ticketleri Gör</span></a>        
                </li>
                <li class="navbar__item">
                    <a href="account.php" class="navbar__link"><i data-feather="user"></i><span>Hesabım</span></a>  
                </li>
                <li class="navbar__item">
                    <a href="logout.php" class="navbar__link"><i data-feather="settings"></i><span>Çıkış</span></a>        
                </li>
            </ul>
        </nav>
    </div>
    <main>
        <div class="container">
            <div class="info-container">
                <h2>Hoş geldin, <?php echo htmlspecialchars($_SESSION['Name']); ?>!</h2>
                <?php if ($userData): ?>
                    <table>
                        <tr>
                            <th>Müşteri Numarası:</th>
                            <td><?php echo htmlspecialchars($userData['CustomerID']); ?></td>
                        </tr>
                        <tr>
                            <th>İsim:</th>
                            <td><?php echo htmlspecialchars($userData['Name']); ?></td>
                        </tr>
                        <tr>
                            <th>Soyisim:</th>
                            <td><?php echo htmlspecialchars($userData['Surname']); ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo htmlspecialchars($userData['Email']); ?></td>
                        </tr>
                    </table>
                <?php else: ?>
                    <p>No account information available.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script>
        feather.replace();
    </script>
</body>
</html>
