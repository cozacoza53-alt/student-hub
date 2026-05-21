<?php
declare(strict_types=1);
require_once 'db.php';

$system_message = "";

// ВИДАЛЕННЯ ЗАПИСУ (Delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Завдяки ON DELETE CASCADE у твоїй структурі БД,
        // при видаленні з таблиці posts дочірні таблиці очистяться самі.
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $system_message = "Публікацію успішно видалено з бази даних!";
    } catch (Exception $e) {
        $system_message = "Помилка при видаленні: " . $e->getMessage();
    }
}

// ЧИТАННЯ ДАНИХ (Read)
$query = "
    SELECT p.id, p.type, p.title, p.author,
           pn.post_date, pt.difficulty
    FROM posts p
    LEFT JOIN posts_news pn ON p.id = pn.post_id
    LEFT JOIN posts_tips pt ON p.id = pt.post_id
    ORDER BY p.id DESC
";
$posts = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Панель адміністратора | CRUD</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; color: #333; }
        .admin-table th { background-color: #34495e; color: white; }
        .btn { padding: 8px 14px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; display: inline-block; }
        .btn-add { background-color: #2ecc71; font-weight: bold; margin-bottom: 15px; }
        .btn-edit { background-color: #3498db; margin-right: 5px; }
        .btn-delete { background-color: #e74c3c; border: none; cursor: pointer; }
        .alert { padding: 15px; background: #e8f5e9; color: #2e7d32; border-radius: 8px; margin-bottom: 15px; border: 1px solid #c8e6c9; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; color: white; font-weight: bold; }
        .badge-news { background: #e67e22; }
        .badge-tip { background: #9b59b6; }
    </style>
</head>
<body>
<header>
    <h1>Панель адміністратора (CRUD)</h1>
    <p><a href="index.php" style="color: #1abc9c; text-decoration: none; font-weight: bold;">⬅ Повернутися на сайт</a></p>
</header>

<div class="admin-container">
    <?php if (!empty($system_message)): ?>
        <div class="alert"><?= htmlspecialchars($system_message); ?></div>
    <?php endif; ?>

    <a href="form.php" class="btn btn-add">➕ Створити нову публікацію</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Тип</th>
                <th>Заголовок</th>
                <th>Автор</th>
                <th>Додаткові параметри</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($posts)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: gray;">Таблиця порожня. Додайте перший пост!</td>
                </tr>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= $post['id']; ?></td>
                        <td>
                            <?php if($post['type'] === 'news'): ?>
                                <span class="badge badge-news">Новина</span>
                            <?php else: ?>
                                <span class="badge badge-tip">Порада</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($post['title']); ?></strong></td>
                        <td><?= htmlspecialchars($post['author']); ?></td>
                        <td>
                            <?php if ($post['type'] === 'news'): ?>
                                Дата: <?= htmlspecialchars($post['post_date'] ?? '-'); ?>
                            <?php else: ?>
                                Складність: <?= $post['difficulty'] ?? '-'; ?>/5
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="form.php?id=<?= $post['id']; ?>" class="btn btn-edit">✏ Редагувати</a>
                            <a href="admin.php?action=delete&id=<?= $post['id']; ?>" class="btn btn-delete" onclick="return confirm('Ви впевнені, що хочете видалити цей запис?')">❌ Видалити</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>