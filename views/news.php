<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
if ($conn->connect_error) {
    die("Помилка підключення до БД: " . $conn->connect_error);
}

$is_admin = (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);

if ($is_admin) {
    $sql = "SELECT * FROM news ORDER BY date DESC";
} else {
    $sql = "SELECT * FROM news WHERE visible = 1 ORDER BY date DESC";
}

$result = $conn->query($sql);
?>

<main class="content">
    <h2>Стрічка новин Формули-1</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            
            <article class="news-article">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                
                <p class="news-meta">
                    Додано: <?php echo date('d.m.Y H:i', strtotime($row['date'])); ?>
                    <?php if ($is_admin && $row['visible'] == 0): ?>
                        <span class="status-pending">(Очікує модерації)</span>
                    <?php endif; ?>
                </p>
                
                <p><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 200, "..."))); ?></p>

                <div class="action-buttons">
                    <a href="index.php?action=view_news&id=<?php echo $row['id']; ?>" class="btn-action btn-view">Перегляд</a>
                    
                    <?php if ($is_admin): ?>
                        <a href="index.php?action=update_news&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">Редагувати</a>
                        <a href="index.php?action=delete_news&id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Ви дійсно хочете видалити цю новину?');">Видалити</a>
                    <?php endif; ?>
                </div>
            </article>

        <?php endwhile; ?>
    <?php else: ?>
        <p>Наразі новин немає. Зайдіть пізніше!</p>
    <?php endif; ?>
</main>

<?php $conn->close(); ?>