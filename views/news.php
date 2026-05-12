<?php
$is_admin = (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
$cache_file = $is_admin ? 'cache/news_admin.json' : 'cache/news_public.json';
$news_data = [];

if (file_exists($cache_file)) {
    $json = file_get_contents($cache_file);
    $news_data = json_decode($json, true);
} else {
    $conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
    if (!$conn->connect_error) {
        $sql = $is_admin ? "SELECT * FROM news ORDER BY date DESC" : "SELECT * FROM news WHERE visible = 1 ORDER BY date DESC";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $news_data[] = $row;
            }
        }
        $conn->close();
        file_put_contents($cache_file, json_encode($news_data, JSON_UNESCAPED_UNICODE));
    }
}
?>

<main class="content">
    <h2>Стрічка новин Формули-1</h2>

    <?php if (!empty($news_data)): ?>
        <?php foreach ($news_data as $row): ?>
            
            <article class="news-article">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                
                <p class="news-meta">
                    Додано: <?php echo date('d.m.Y H:i', strtotime($row['date'])); ?>
                    <?php if ($is_admin && $row['visible'] == 0): ?>
                        <span class="status-pending">(Очікує модерації)</span>
                    <?php endif; ?>
                </p>

                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?php echo $row['image']; ?>" alt="News Image" class="news-image" style="max-height: 300px; width: auto;">
                <?php endif; ?>
                
                <p><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 200, "..."))); ?></p>

                <div class="action-buttons">
                    <a href="index.php?action=view_news&id=<?php echo $row['id']; ?>" class="btn-action btn-view">Перегляд</a>
                    <?php if ($is_admin): ?>
                        <a href="index.php?action=update_news&id=<?php echo $row['id']; ?>" class="btn-action btn-edit">Редагувати</a>
                        <a href="index.php?action=delete_news&id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Ви дійсно хочете видалити цю новину?');">Видалити</a>
                    <?php endif; ?>
                </div>
            </article>

        <?php endforeach; ?>
    <?php else: ?>
        <p>Наразі новин немає. Зайдіть пізніше!</p>
    <?php endif; ?>
</main>