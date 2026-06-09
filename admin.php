<?php
declare(strict_types=1);

// Безпечне підключення конфігурації бази даних для XAMPP
require_once __DIR__ . '/db.php';

$system_message = "";

// ВИДАЛЕННЯ ЗАПИСУ (Delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Завдяки ON DELETE CASCADE у структурі твого SQL-дампу,
        // при видаленні з таблиці posts дочірні записи (news/tips) очистяться самі.
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
    <title>Панель адміністрування | Student Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Локальні стилі для акуратної структури таблиці адмінки */
        .admin-container { padding: 30px; max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-top: 30px; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .admin-table th { background-color: #34495e; color: white; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-news { background-color: #3498db; }
        .badge-tip { background-color: #e67e22; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; margin-right: 5px; }
        .btn-add { background-color: #2ecc71; color: white; margin-bottom: 20px; padding: 10px 20px; }
        .btn-edit { background-color: #f1c40f; color: #333; }
        .btn-delete { background-color: #e74c3c; color: white; }
        .btn-back { background-color: #7f8c8d; color: white; margin-left: 10px; }
        .alert { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>

<header>
    <h1>Панель керування матеріалами</h1>
    <p>Редагування, видалення та додавання нових публікацій у базу даних.</p>
</header>

<div class="admin-container">
    <?php if (!empty($system_message)): ?>
        <div class="alert"><?= htmlspecialchars($system_message); ?></div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="form.php" class="btn btn-add">➕ Створити нову публікацію</a>
        <a href="index.php" class="btn btn-back">⬅ На головну</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Тип</th>
                <th>Заголовок</th>
                <th>Автор</th>
                <th>Специфічні дані</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: gray;">У базі даних немає жодної публікації.</td>
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
                            <a href="admin.php?action=delete&id=<?= $post['id']; ?>" class="btn btn-delete" onclick=\"return confirm('Ви впевнені, що хочете видалити цей запис?')\">❌ Видалити</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>