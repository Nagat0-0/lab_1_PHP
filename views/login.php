<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);

    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT id, login, password, admin FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['is_admin'] = $user['admin'];

                $stmt->close();
                $conn->close();
                
                header("Location: index.php?action=main");
                exit;
            } 
            else {
                $error = "Невірний логін або пароль.";
            }
        } 
        else {
            $error = "Невірний логін або пароль.";
        }
        $stmt->close();
        $conn->close();
    } 
    else {
        $error = "Помилка підключення до бази даних.";
    }
}
?>

<main class="content">
    <h2>Авторизація</h2>

    <?php if (!empty($error)): ?>
        <div class="error-messages">
            <strong><?php echo $error; ?></strong>
        </div>
    <?php endif; ?>

    <form action="index.php?action=login" method="POST">
        <div class="form-group">
            <label for="login">Логін:</label><br>
            <input type="text" id="login" name="login" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-submit">Увійти</button>
    </form>
</main>