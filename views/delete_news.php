<?php
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php?action=main");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

$conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);

if (!$conn->connect_error) {
    $check_sql = "SELECT id FROM news WHERE id = $id";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM news WHERE id = $id";
        if ($conn->query($delete_sql) === TRUE) {
            $message = "Новину успішно видалено назавжди.";
        } else {
            $message = "Помилка при видаленні: " . $conn->error;
        }
    } else {
        $message = "Помилка: новини з таким ID не існує в базі даних.";
    }
    $conn->close();
}
?>

<main class="content">
    <h2>Видалення новини</h2>
    
    <div class="admin-message-box">
        <p><strong><?php echo $message; ?></strong></p>
        <a href="index.php?action=news" class="btn-back">Повернутися до стрічки новин</a>
    </div>
</main>