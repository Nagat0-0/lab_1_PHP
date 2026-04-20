<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';
    $email = $_POST['email'] ?? '';
    $first_name = $_POST['first_name'] ?? '';

    if (!preg_match('/^(?=(?:.*[a-zA-Zа-яА-ЯіІїЇєЄґҐ]){4})[a-zA-Zа-яА-ЯіІїЇєЄґҐ0-9_-]+$/u', $login)) {
        $errors[] = "Логін має містити не менше 4 літер. Дозволено використовувати латинські/кириличні букви, цифри, підкреслення та дефіс.";
    }

    if (!preg_match('/^(?=(?:.*[a-zA-Zа-яА-ЯіІїЇєЄґҐ]){7})(?=.*[a-zа-яієїґ])(?=.*[A-ZА-ЯІЄЇҐ])(?=.*\d).+$/u', $password)) {
        $errors[] = "Пароль має містити не менше 7 літер, серед яких обов'язково є великі та малі, а також мінімум одну цифру.";
    }

    if ($password !== $password_repeat) {
        $errors[] = "Введені паролі не співпадають.";
    }

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errors[] = "Введіть коректну Email-адресу.";
    }

    if (!preg_match("/^[a-zA-Zа-яА-ЯіІїЇєЄґҐ'\-\s]{0,255}$/u", $first_name)) {
        $errors[] = "Поле 'Ім'я' не має перевищувати 255 символів і не може містити цифр чи розділових знаків (окрім дефісу та апострофа).";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);

        if ($conn->connect_error) {
            die("Помилка підключення до БД: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO users (login, password, email, first_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $hashed_password, $email, $first_name);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php?action=registration_successful");
            exit;
        } else {
            $errors[] = "Помилка при збереженні в базу: " . $conn->error;
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<main class="content">
    <h2>Реєстрація нового користувача</h2>

    <p style="font-size: 0.9em; color: #555;">Поля, позначені <span style="color: red;">*</span>, є обов'язковими для заповнення.</p>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <strong>Помилки при заповненні форми:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?action=registration" method="POST">
        <div class="form-group">
            <label for="login">Логін <span style="color: red;">*</span>:</label><br>
            <input type="text" id="login" name="login" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль <span style="color: red;">*</span>:</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_repeat">Повторіть пароль <span style="color: red;">*</span>:</label><br>
            <input type="password" id="password_repeat" name="password_repeat" required>
        </div>

        <div class="form-group">
            <label for="email">Електронна пошта <span style="color: red;">*</span>:</label><br>
            <input type="text" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="first_name">Ім'я :</label><br>
            <input type="text" id="first_name" name="first_name">
        </div>

        <button type="submit" class="btn-submit">Зареєструватися</button>
    </form>
</main>