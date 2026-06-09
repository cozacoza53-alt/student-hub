<?php
declare(strict_types=1);

// Безпечне підключення конфігурації бази даних для XAMPP
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$is_edit = $id !== null;

// Дефолтні значення для полів форми
$title = '';
$content = '';
$author = '';
$type = 'news';
$post_date = date('Y-m-d');
$difficulty = 3;
$error = '';
$success = '';

// Якщо це РЕДАГУВАННЯ (Update) — витягуємо поточні дані з БД
if ($is_edit) {
    $query = "
        SELECT p.*, pn.post_date, pt.difficulty 
        FROM posts p
        LEFT JOIN posts_news pn ON p.id = pn.post_id
        LEFT JOIN posts_tips pt ON p.id = pt.post_id
        WHERE p.id = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if ($post) {
        $title = $post['title'];
        $content = $post['content'];
        $author = $post['author'];
        $type = $post['type'];
        $post_date = $post['post_date'] ?? date('Y-m-d');
        $difficulty = (int)($post['difficulty'] ?? 3);
    } else {
        $is_edit = false; // Якщо ID не знайдено, перемикаємо у режим створення
    }
}

// ОБРОБКА ВІДПРАВКИ ФОРМИ (Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $type = $_POST['type'] ?? 'news';
    $post_date = $_POST['post_date'] ?? null;
    $difficulty = isset($_POST['difficulty']) ? (int)$_POST['difficulty'] : null;

    // Базова валідація даних
    if (mb_strlen($title) < 3) {
        $error = "Назва повинна містити мінімум 3 символи.";
    } elseif (empty($content)) {
        $error = "Контент не може бути порожнім.";
    } elseif (mb_strlen($author) < 2) {
        $error = "Ім'я автора закоротке.";
    } else {
        try {
            $pdo->beginTransaction();

            if ($is_edit) {
                // 1. Оновлюємо базову таблицю posts
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, author = ? WHERE id = ?");
                $stmt->execute([$title, $content, $author, $id]);

                // 2. Оновлюємо специфічні таблиці залежно від типу
                if ($type === 'news') {
                    $stmt = $pdo->prepare("INSERT INTO posts_news (post_id, post_date) VALUES (?, ?) ON DUPLICATE KEY UPDATE post_date = ?");
                    $stmt->execute([$id, $post_date, $post_date]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO posts_tips (post_id, difficulty) VALUES (?, ?) ON DUPLICATE KEY UPDATE difficulty = ?");
                    $stmt->execute([$id, $difficulty, $difficulty]);
                }
                $success = "Матеріал успішно оновлено!";
            } else {
                // СТВОРЕННЯ НОВОГО ЗАПИСУ (Insert)
                // За замовчуванням category_id = 1 (Student Hub) відповідно до твого SQL-дампу
                $stmt = $pdo->prepare("INSERT INTO posts (category_id, type, title, content, author) VALUES (1, ?, ?, ?, ?)");
                $stmt->execute([$type, $title, $content, $author]);
                $new_post_id = $pdo->lastInsertId();

                if ($type === 'news') {
                    $stmt = $pdo->prepare("INSERT INTO posts_news (post_id, post_date) VALUES (?, ?)");
                    $stmt->execute([$new_post_id, $post_date]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO posts_tips (post_id, difficulty) VALUES (?, ?)");
                    $stmt->execute([$new_post_id, $difficulty]);
                }
                $success = "Новий матеріал успішно додано до бази даних!";
                
                // Очищення форми після успішного створення
                if (!$is_edit) {
                    $title = $content = $author = '';
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Помилка бази даних: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Редагувати' : 'Створити'; ?> публікацію | Student Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #2c3e50; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: Verdana, sans-serif; font-size: 14px; }
        .btn-save { background-color: #1abc9c; color: white; border: none; padding: 12px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; display: inline-block; }
        .btn-save:hover { background-color: #16a085; }
        .btn-cancel { background-color: #e74c3c; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-left: 10px; display: inline-block; }
        .err { background: #ffebee; color: #c62828; padding: 15px; border-radius: 6px; border: 1px solid #ef9a9a; margin-bottom: 20px; }
        .succ { background: #e8f8f5; color: #117a65; padding: 15px; border-radius: 6px; border: 1px solid #a3e4d7; margin-bottom: 20px; }
    </style>
    <script>
        // Динамічне відображення полів залежно від типу поста
        function toggleFields() {
            const type = document.querySelector('input[name="type"]:checked').value;
            document.getElementById('fields_news').style.display = type === 'news' ? 'block' : 'none';
            document.getElementById('fields_tip').style.display = type === 'tip' ? 'block' : 'none';
        }
        window.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</head>
<body>

<header>
    <h1><?= $is_edit ? '✏ Редагування' : '➕ Створення'; ?> матеріалу</h1>
    <p>Заповніть поля нижче для збереження публікації у базі даних.</p>
</header>

<div class="form-container">
    <?php if(!empty($error)): ?> <div class="err"><?= $error; ?></div> <?php endif; ?>
    <?php if(!empty($success)): ?> <div class="succ"><?= $success; ?></div> <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label>Тип матеріалу</label>
            <input type="radio" name="type" value="news" id="type_news" <?= $type === 'news' ? 'checked' : ''; ?> onchange="toggleFields()" <?= $is_edit ? 'disabled' : ''; ?>>
            <label for="type_news" style="display:inline; font-weight:normal; margin-right: 15px;">Новина</label>

            <input type="radio" name="type" value="tip" id="type_tip" <?= $type === 'tip' ? 'checked' : ''; ?> onchange="toggleFields()" <?= $is_edit ? 'disabled' : ''; ?>>
            <label for="type_tip" style="display:inline; font-weight:normal;">Порада</label>
            
            <?php if ($is_edit): ?>
                <input type="hidden" name="type" value="<?= $type; ?>">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="title">Заголовок</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title); ?>" required>
        </div>

        <div class="form-group">
            <label for="author">Автор</label>
            <input type="text" name="author" id="author" class="form-control" value="<?= htmlspecialchars($author); ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Текст матеріалу</label>
            <textarea name="content" id="content" rows="6" class="form-control" required><?= htmlspecialchars($content); ?></textarea>
        </div>

        <div id="fields_news" class="form-group">
            <label for="post_date">Дата публікації</label>
            <input type="date" name="post_date" id="post_date" class="form-control" value="<?= htmlspecialchars($post_date); ?>">
        </div>

        <div id="fields_tip" class="form-group">
            <label for="difficulty">Рівень складності (від 1 до 5)</label>
            <input type="number" name="difficulty" id="difficulty" class="form-control" min="1" max="5" value="<?= $difficulty; ?>">
        </div>

        <button type="submit" class="btn-save"><?= $is_edit ? 'Зберегти зміни' : 'Опублікувати'; ?></button>
        <a href="admin.php" class="btn-cancel">Скасувати</a>
    </form>
</div>

</body>
</html>