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
    $image_name = null;

    if (empty($title) || empty($content)) {
        $error = "Будь ласка, заповніть усі обов'язкові поля.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            if (getimagesize($tmp_name) !== false) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid() . '.' . $ext;
                move_uploaded_file($tmp_name, 'uploads/' . $image_name);
            } else {
                $error = "Завантажений файл не є зображенням.";
            }
        }

        if (empty($error)) {
            $conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
            if (!$conn->connect_error) {
                $author_id = (int)$_SESSION['user_id'];
                $visible = (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 1 : 0;

                $stmt = $conn->prepare("INSERT INTO news (title, content, visible, author_id, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiis", $title, $content, $visible, $author_id, $image_name);

                if ($stmt->execute()) {
                    $success = "Новину успішно додано!";

                    if (file_exists('cache/news_public.json')) unlink('cache/news_public.json');
                    if (file_exists('cache/news_admin.json')) unlink('cache/news_admin.json');
                } else {
                    $error = "Помилка при збереженні: " . $conn->error;
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>

<main class="content">
    <h2>Додати новину</h2>

    <?php if ($error): ?><div class="error-messages"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success-message"><strong><?php echo $success; ?></strong></div><?php endif; ?>

    <form action="index.php?action=create_news" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Заголовок *:</label><br>
            <input type="text" id="title" name="title" class="news-input" required>
        </div>

        <div class="form-group">
            <label for="content">Текст новини *:</label><br>
            <textarea id="content" name="content" rows="6" class="news-input" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Зображення (необов'язково):</label><br>
            <input type="file" id="image" name="image" accept="image/*" class="news-input">
        </div>

        <button type="submit" class="btn-submit">Опублікувати</button>
    </form>
</main>