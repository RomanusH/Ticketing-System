<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$role = $_SESSION['Role'];

if ($role === 'customer') {
    $customerID = $_SESSION['CustomerID'];
} elseif ($role === 'customerManager') {
    $customerID = $_SESSION['CustomerManagerID'];
} else {
    echo "Role not recognized.";
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

$customerData = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['context'])) {
        // Ticket submission logic here
        $context = $conn->real_escape_string($_POST['context']);
        $priority = $conn->real_escape_string($_POST['priority']);
        $description = $conn->real_escape_string($_POST['description']);
        
        $attachedDocument = null;
        if (isset($_FILES['attached_document']) && $_FILES['attached_document']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploaded_files/";
            $attachedDocument = basename($_FILES["attached_document"]["name"]);
            $targetFilePath = $targetDir . $attachedDocument;
            if (!move_uploaded_file($_FILES["attached_document"]["tmp_name"], $targetFilePath)) {
                echo "Error uploading the file.";
                exit();
            }
        }

        $sql = "INSERT INTO Ticket_Details (CustomerID, Context, Priority, Description, AttachedDocuments, CreateDate, Status) 
                VALUES ('$customerID', '$context', '$priority', '$description', '$attachedDocument', NOW(), 1)";

        if ($conn->query($sql) === TRUE) {
            echo "Ticket successfully submitted.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Handle customer manager tools
    if ($role === 'customerManager' && isset($_POST['managerAction'])) {
        $managerAction = $_POST['managerAction'];

        if ($managerAction === 'addCustomer') {
            // Add customer logic here
            $customerID = $conn->real_escape_string($_POST['customerID']);
            $customerName = $conn->real_escape_string($_POST['customerName']);
            $customerSurname = $conn->real_escape_string($_POST['customerSurname']);
            $customerGender = $conn->real_escape_string($_POST['customerGender']);
            $customerTelephone = $conn->real_escape_string($_POST['customerTelephone']);
            $customerEmail = $conn->real_escape_string($_POST['customerEmail']);
            $customerPassword = password_hash($conn->real_escape_string($_POST['customerPassword']), PASSWORD_DEFAULT);

            $sql = "INSERT INTO Customers (CustomerID, Name, Surname, Gender, Telephone, Email, LoginPasscode) 
                    VALUES ('$customerID', '$customerName', '$customerSurname', '$customerGender', '$customerTelephone', '$customerEmail', '$customerPassword')";

            if ($conn->query($sql) === TRUE) {
                echo "Customer successfully added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif ($managerAction === 'deleteCustomer') {
            // Delete customer logic here
            $customerEmail = $conn->real_escape_string($_POST['customerEmail']);
            $sql = "DELETE FROM Customers WHERE Email = '$customerEmail'";
            if ($conn->query($sql) === TRUE) {
                echo "Customer successfully deleted.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif ($managerAction === 'selectCustomer') {
            $customerName = $conn->real_escape_string($_POST['customerName']);
            $customerSurname = $conn->real_escape_string($_POST['customerSurname']);
            $sql = "SELECT * FROM Customers WHERE Name = ? AND Surname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $customerName, $customerSurname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $customerData = $result->fetch_assoc();
            } else {
                echo "No customer found.";
            }
        } elseif ($managerAction === 'updateCustomer') {
            $customerID = $conn->real_escape_string($_POST['customerID']);
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
                $fieldsToUpdate[] = "LoginPasscode = '" . password_hash($conn->real_escape_string($_POST['customerPassword']), PASSWORD_DEFAULT) . "'";
            }

            if (!empty($fieldsToUpdate)) {
                $sql = "UPDATE Customers SET " . implode(", ", $fieldsToUpdate) . " WHERE CustomerID = '$customerID'";

                if ($conn->query($sql) === TRUE) {
                    echo "Customer updated successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "No fields to update.";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Ticket - Support Ticket System</title>
    <link rel="stylesheet" href="ticketing.css">
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
                    <a href="account.php" class="navbar__link"><i data-feather="users"></i><span>Heasbım</span></a>  
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
                <h2>Hoş Geldin <?php echo $_SESSION['Name'];?>!</h2>
                <p>Programımızla ilgili bir sorununuz mu var? Burdan bize sorunlarınızı göndererek ulaşa bilirsiniz. Teknik servisimiz size kısa bir sürede ulaşıcaktır.</p>
            </div>
            <div class="form-container">
                <h2>Ticket Gönder</h2>
                <form action="ticketing.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="context">Başlık:</label>
                        <input type="text" id="context" name="context" required>
                    </div>
                    <div class="form-group">
                        <label for="priority">Önemlilik Durumu:</label>
                        <select id="priority" name="priority" required>
                            <option value="Düşük">Düşük</option>
                            <option value="Orta">Orta</option>
                            <option value="Yüksek">Yüksek</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama:</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attached_document">Ekli Dosya:</label>
                        <input type="file" id="attached_document" name="attached_document">
                    </div>
                    <div class="form-group">
                        <button type="submit">Gönder</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($role === 'customerManager'): ?>
        <div class="manager-tools">
            <h2>Müşteri Yönetim Araçları</h2>
            <form action="ticketing.php" method="post">
                <input type="hidden" name="managerAction" value="addCustomer">
                <h3>Müşteri Ekle</h3>
                <div class="form-group">
                    <label for="customerID">Müşteri ID:</label>
                    <input type="text" id="customerID" name="customerID" required>
                </div>
                <div class="form-group">
                    <label for="customerName">Adı:</label>
                    <input type="text" id="customerName" name="customerName" required>
                </div>
                <div class="form-group">
                    <label for="customerSurname">Soyadı:</label>
                    <input type="text" id="customerSurname" name="customerSurname" required>
                </div>
                <div class="form-group">
                    <label for="customerGender">Cinsiyet:</label>
                    <select id="customerGender" name="customerGender" required>
                        <option value="Male">Erkek</option>
                        <option value="Female">Kadın</option>
                        <option value="Other">Diğer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="customerTelephone">Telefon:</label>
                    <input type="text" id="customerTelephone" name="customerTelephone" required>
                </div>
                <div class="form-group">
                    <label for="customerEmail">E-mail:</label>
                    <input type="email" id="customerEmail" name="customerEmail" required>
                </div>
                <div class="form-group">
                    <label for="customerPassword">Parola:</label>
                    <input type="password" id="customerPassword" name="customerPassword" required>
                </div>
                <div class="form-group">
                    <button type="submit">Ekle</button>
                </div>
            </form>

            <form action="ticketing.php" method="post">
                <input type="hidden" name="managerAction" value="selectCustomer">
                <h3>Müşteri Seç</h3>
                <div class="form-group">
                    <label for="customerName">Adı:</label>
                    <input type="text" id="customerName" name="customerName" required>
                </div>
                <div class="form-group">
                    <label for="customerSurname">Soyadı:</label>
                    <input type="text" id="customerSurname" name="customerSurname" required>
                </div>
                <div class="form-group">
                    <button type="submit">Seç</button>
                </div>
            </form>

            <?php if ($customerData): ?>
            <form action="ticketing.php" method="post">
                <input type="hidden" name="managerAction" value="updateCustomer">
                <input type="hidden" name="customerID" value="<?= $customerData['CustomerID'] ?>">
                <h3>Müşteri Güncelle</h3>
                <div class="form-group">
                    <label>Mevcut Adı: <?= $customerData['Name'] ?></label>
                </div>
                <div class="form-group">
                    <label>Mevcut Soyadı: <?= $customerData['Surname'] ?></label>
                </div>
                <div class="form-group">
                    <label for="customerGender">Cinsiyet:</label>
                    <select id="customerGender" name="customerGender">
                        <option value="">-- Seçim Yapınız --</option>
                        <option value="Male">Erkek</option>
                        <option value="Female">Kadın</option>
                        <option value="Other">Diğer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="customerTelephone">Telefon:</label>
                    <input type="text" id="customerTelephone" name="customerTelephone">
                </div>
                <div class="form-group">
                    <label for="customerEmail">E-mail:</label>
                    <input type="email" id="customerEmail" name="customerEmail">
                </div>
                <div class="form-group">
                    <label for="customerPassword">Parola:</label>
                    <input type="password" id="customerPassword" name="customerPassword">
                </div>
                <div class="form-group">
                    <button type="submit">Güncelle</button>
                </div>
            </form>
            <?php endif; ?>

            <form action="ticketing.php" method="post">
                <input type="hidden" name="managerAction" value="deleteCustomer">
                <h3>Müşteri Sil</h3>
                <div class="form-group">
                    <label for="customerEmail">E-mail:</label>
                    <input type="email" id="customerEmail" name="customerEmail" required>
                </div>
                <div class="form-group">
                    <button type="submit">Sil</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </main>
    <script>
        feather.replace();
    </script>
</body>
</html>
