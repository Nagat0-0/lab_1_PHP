<?php
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php?action=main");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

$conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $visible = isset($_POST['visible']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        $error = "Заповніть усі обов'язкові поля.";
    } else {
        $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, visible = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $content, $visible, $id);
        
        if ($stmt->execute()) {
            $success = "Новину успішно оновлено!";
        } else {
            $error = "Помилка оновлення: " . $conn->error;
        }
        $stmt->close();
    }
}

$sql = "SELECT * FROM news WHERE id = $id";
$result = $conn->query($sql);
$news_item = $result->fetch_assoc();

$conn->close();
?>

<main class="content">
    <h2>Редагування новини</h2>

    <?php if (!$news_item): ?>
        <p>Новини з таким ID не існує.</p>
    <?php else: ?>
        <?php if ($error): ?><div class="error-messages"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success-message"><strong><?php echo $success; ?></strong></div><?php endif; ?>

        <form action="index.php?action=update_news&id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="title">Заголовок:</label><br>
                <input type="text" id="title" name="title" class="news-input" value="<?php echo htmlspecialchars($news_item['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Текст новини:</label><br>
                <textarea id="content" name="content" rows="8" class="news-input" required><?php echo htmlspecialchars($news_item['content']); ?></textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="visible" value="1" <?php echo ($news_item['visible'] == 1) ? 'checked' : ''; ?>>
                    Опублікувати на сайті
                </label>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-submit">Зберегти зміни</button>
                <a href="index.php?action=news" class="btn-cancel">Скасувати</a>
            </div>
        </form>
    <?php endif; ?>
</main>