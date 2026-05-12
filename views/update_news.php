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

    $old_data = $conn->query("SELECT image FROM news WHERE id = $id")->fetch_assoc();
    $image_name = $old_data['image'];

    if (empty($title) || empty($content)) {
        $error = "Заповніть усі обов'язкові поля.";
    } else {
        if (isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
            if ($image_name && file_exists('uploads/' . $image_name)) {
                unlink('uploads/' . $image_name);
            }
            $image_name = null;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            if (getimagesize($tmp_name) !== false) {
                if ($image_name && file_exists('uploads/' . $image_name)) {
                    unlink('uploads/' . $image_name);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid() . '.' . $ext;
                move_uploaded_file($tmp_name, 'uploads/' . $image_name);
            } else {
                $error = "Завантажений файл не є зображенням.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, visible = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $title, $content, $visible, $image_name, $id);
            
            if ($stmt->execute()) {
                $success = "Зміни успішно збережено!";

                if (file_exists('cache/news_public.json')) unlink('cache/news_public.json');
                if (file_exists('cache/news_admin.json')) unlink('cache/news_admin.json');
            } else {
                $error = "Помилка оновлення: " . $conn->error;
            }
            $stmt->close();
        }
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

        <form action="index.php?action=update_news&id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Заголовок:</label><br>
                <input type="text" id="title" name="title" class="news-input" value="<?php echo htmlspecialchars($news_item['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Текст новини:</label><br>
                <textarea id="content" name="content" rows="8" class="news-input" required><?php echo htmlspecialchars($news_item['content']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Поточне зображення:</label><br>
                <?php if ($news_item['image']): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="uploads/<?php echo $news_item['image']; ?>" alt="Поточне фото" style="max-width: 200px; display: block; margin-bottom: 5px;">
                        
                        <label class="checkbox-label" style="color: #e74c3c;">
                            <input type="checkbox" name="delete_image" value="1"> 
                            Видалити це зображення
                        </label>
                    </div>
                <?php else: ?>
                    <p style="color: gray; font-size: 0.9em;">Зображення відсутнє</p>
                <?php endif; ?>
                
                <label for="image">Завантажити нове (або замінити існуюче):</label><br>
                <input type="file" id="image" name="image" accept="image/*" class="news-input">
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