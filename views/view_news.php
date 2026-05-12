<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$conn = new mysqli('127.0.0.1', 'root', '', 'f1_news_db', 3307);
if ($conn->connect_error) {
    die("Помилка підключення до БД: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_like']) && $user_id > 0) {
    $check_like = $conn->query("SELECT id FROM likes WHERE news_id = $id AND user_id = $user_id");
    
    if ($check_like->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE news_id = $id AND user_id = $user_id");
    } else {
        $conn->query("INSERT INTO likes (news_id, user_id) VALUES ($id, $user_id)");
    }
    header("Location: index.php?action=view_news&id=$id");
    exit;
}

$sql = "SELECT * FROM news WHERE id = $id";
$result = $conn->query($sql);
$news_item = $result->fetch_assoc();

$likes_count_res = $conn->query("SELECT COUNT(*) as count FROM likes WHERE news_id = $id");
$total_likes = $likes_count_res->fetch_assoc()['count'];

$user_liked = false;
if ($user_id > 0) {
    $user_like_res = $conn->query("SELECT id FROM likes WHERE news_id = $id AND user_id = $user_id");
    $user_liked = ($user_like_res->num_rows > 0);
}
?>

<main class="content">
    <?php if ($news_item): ?>
        <article class="news-article">
            <h2><?php echo htmlspecialchars($news_item['title']); ?></h2>
            <p class="news-meta">
                Опубліковано: <?php echo date('d.m.Y H:i', strtotime($news_item['date'])); ?>
            </p>
            <hr>

            <?php if (!empty($news_item['image'])): ?>
                <div style="text-align: center; margin: 20px 0;">
                    <img src="uploads/<?php echo $news_item['image']; ?>" alt="News Image" class="news-image">
                </div>
            <?php endif; ?>

            <div class="news-content">
                <?php echo nl2br(htmlspecialchars($news_item['content'])); ?>
            </div>
            
            <div class="like-section">
                <?php if ($user_id > 0): ?>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="toggle_like" value="1">
                        <?php if ($user_liked): ?>
                            <button type="submit" class="btn-like btn-unlike">Не подобається</button>
                        <?php else: ?>
                            <button type="submit" class="btn-like">Подобається</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <span style="font-size: 0.9em; color: gray;"><i>Увійдіть, щоб проголосувати</i></span>
                <?php endif; ?>
                
                <span class="like-count">Вподобали: <?php echo $total_likes; ?></span>
            </div>
            
            <a href="index.php?action=news" class="btn-back" style="margin-top: 30px;">&larr; Назад до списку</a>
        </article>
    <?php else: ?>  
        <p>На жаль, такої сторінки не існує.</p>
        <a href="index.php?action=news" class="btn-back">Повернутися до новин</a>
    <?php endif; ?>
</main>

<?php $conn->close(); ?>