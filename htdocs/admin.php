<?php
session_start();

$servername = "localhost";
$username = "kudlay"; 
$password = "a5EzA3ad"; 
$dbname = "kudlay";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $admin_username = $_POST['username'];
        $admin_password = $_POST['password'];

        if ($admin_username == 'admin' && $admin_password == 'admin') {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = "Неправильный логин или пароль.";
        }
    }

    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Админ панель - Вход</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
        <div class="container">
            <h1>Вход в админ панель</h1>
            <form method="post">
                <input type="text" name="username" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" name="login">Войти</button>
            </form>
            <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
        </div>
        </body>
        </html>
        <?php
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $fio = $_POST['fio'];
    $faculty = $_POST['faculty'];
    $group_number = $_POST['group_number'];
    $education_form = $_POST['education_form'];
    $education_type = $_POST['education_type'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE students SET fio = ?, faculty = ?, group_number = ?, education_form = ?, education_type = ?, phone = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $fio, $faculty, $group_number, $education_form, $education_type, $phone, $email, $id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM students");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            max-width: 95%;
            margin: 0 auto;
            overflow-x: auto;
        }
        .admin-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-container th, .admin-container td {
            padding: 8px;
            border: 1px solid #ddd;
            word-wrap: break-word;
            max-width: 200px;
        }
        .admin-container th {
            background-color: #4caf50;
            color: white;
            text-align: left;
        }
        .admin-container td img {
            max-width: 100px;
            height: auto;
        }
        .admin-container td a {
            display: block;
            text-align: center;
        }
        .admin-container button {
            margin-top: 5px;
        }
        .admin-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <h1>Админ панель</h1>
    <div class="admin-links">
        <a href="index.php" class="admin-link">Вернуться назад</a>
        <a href="logout.php" class="admin-link">Выйти</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Факультет</th>
                <th>Номер группы</th>
                <th>Форма обучения</th>
                <th>Вид обучения</th>
                <th>Телефон</th>
                <th>Почта</th>
                <th>Фото паспорта</th>
                <th>Фото формы 086у</th>
                <th>Фото мед. осмотра</th>
                <th>Фото теста на ВИЧ/СПИД</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><?php echo $row['id']; ?><input type="hidden" name="id" value="<?php echo $row['id']; ?>"></td>
                        <td><input type="text" name="fio" value="<?php echo $row['fio']; ?>"></td>
                        <td><input type="text" name="faculty" value="<?php echo $row['faculty']; ?>"></td>
                        <td><input type="text" name="group_number" value="<?php echo $row['group_number']; ?>"></td>
                        <td><input type="text" name="education_form" value="<?php echo $row['education_form']; ?>"></td>
                        <td><input type="text" name="education_type" value="<?php echo $row['education_type']; ?>"></td>
                        <td><input type="text" name="phone" value="<?php echo $row['phone']; ?>"></td>
                        <td><input type="email" name="email" value="<?php echo $row['email']; ?>"></td>
                        <td><a href="<?php echo $row['folder_name']; ?>/<?php echo $row['passport_photo']; ?>"><img src="<?php echo $row['folder_name']; ?>/<?php echo $row['passport_photo']; ?>" alt="Фото паспорта"></a></td>
                        <td><a href="<?php echo $row['folder_name']; ?>/<?php echo $row['form_086u_photo']; ?>"><img src="<?php echo $row['folder_name']; ?>/<?php echo $row['form_086u_photo']; ?>" alt="Фото формы 086у"></a></td>
                        <td><a href="<?php echo $row['folder_name']; ?>/<?php echo $row['mor_photo']; ?>"><img src="<?php echo $row['folder_name']; ?>/<?php echo $row['mor_photo']; ?>" alt="Фото мед. осмотра"></a></td>
                        <td><a href="<?php echo $row['folder_name']; ?>/<?php echo $row['hiv_aids_photo']; ?>"><img src="<?php echo $row['folder_name']; ?>/<?php echo $row['hiv_aids_photo']; ?>" alt="Фото теста на ВИЧ/СПИД"></a></td>
                        <td>
                            <button type="submit" name="update">Обновить</button>
                            <button type="submit" name="delete">Удалить</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
