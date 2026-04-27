<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
if ($conn->connect_error) {
    die("Помилка підключення до БД: " . $conn->connect_error);
}

$sql = "SELECT * FROM news WHERE id = $id";
$result = $conn->query($sql);
$news_item = $result->fetch_assoc();
?>

<main class="content">
    <?php if ($news_item): ?>
        <article class="news-article">
            <h2><?php echo htmlspecialchars($news_item['title']); ?></h2>
            <p class="news-meta">
                Опубліковано: <?php echo date('d.m.Y H:i', strtotime($news_item['date'])); ?>
            </p>
            <hr>
            <div class="news-content">
                <?php echo nl2br(htmlspecialchars($news_item['content'])); ?>
            </div>
            
            <a href="index.php?action=news" class="btn-back">&larr; Назад до списку</a>
        </article>
    <?php else: ?>  
        <p>На жаль, такої сторінки не існує.</p>
        <a href="index.php?action=news" class="btn-back">Повернутися до новин</a>
    <?php endif; ?>
</main>

<?php $conn->close(); ?>