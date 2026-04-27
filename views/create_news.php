<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        $error = "Будь ласка, заповніть усі поля.";
    } else {
        $conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
        
        if (!$conn->connect_error) {
            $author_id = (int)$_SESSION['user_id'];
            $visible = (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 1 : 0;

            $stmt = $conn->prepare("INSERT INTO news (title, content, visible, author_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $title, $content, $visible, $author_id);

            if ($stmt->execute()) {
                $success = "Новину успішно додано!";
            } else {
                $error = "Помилка при збереженні: " . $conn->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<main class="content">
    <h2>Додати новину</h2>

    <?php if ($error): ?>
        <div class="error-messages"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-message"><strong><?php echo $success; ?></strong></div>
    <?php endif; ?>

    <form action="index.php?action=create_news" method="POST">
        <div class="form-group">
            <label for="title">Заголовок:</label><br>
            <input type="text" id="title" name="title" class="news-input" required>
        </div>

        <div class="form-group">
            <label for="content">Текст новини:</label><br>
            <textarea id="content" name="content" rows="6" class="news-input" required></textarea>
        </div>

        <button type="submit" class="btn-submit">Опублікувати</button>
    </form>
</main>