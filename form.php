<?php
declare(strict_types=1);
require_once 'db.php';

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
        die("Пост з таким ID не знайдено.");
    }
}

// ОБРОБКА ДАНИХ ФОРМИ (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $type = $_POST['type'] ?? $type;
    $post_date = $_POST['post_date'] ?? date('Y-m-d');
    $difficulty = isset($_POST['difficulty']) ? (int)$_POST['difficulty'] : 3;

    // Валідація згідно з бізнес-правилами твоїх PHP-класів
    if (mb_strlen($title) < 3) {
        $error = "Назва повинна містити мінімум 3 символи.";
    } elseif (empty($content)) {
        $error = "Контент не може бути порожнім.";
    } elseif (mb_strlen($author) < 2) {
        $error = "Ім’я автора закоротке.";
    } elseif ($type === 'tip' && ($difficulty < 1 || $difficulty > 5)) {
        $error = "Складність повинна бути від 1 до 5.";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction(); // Запускаємо транзакцію для безпеки зв'язків структур таблиць

            if ($is_edit) {
                // Оновлюємо основну таблицю posts
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, author = ? WHERE id = ?");
                $stmt->execute([$title, $content, $author, $id]);

                // Очищаємо дочірні записи, щоб перезаписати нові (у разі зміни доп. параметрів)
                $pdo->prepare("DELETE FROM posts_news WHERE post_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM posts_tips WHERE post_id = ?")->execute([$id]);
                
                $post_id = $id;
            } else {
                // Створюємо новий запис (INSERT)
                // category_id за замовчуванням 1 ('Student Hub')
                $stmt = $pdo->prepare("INSERT INTO posts (category_id, type, title, content, author) VALUES (1, ?, ?, ?, ?)");
                $stmt->execute([$type, $title, $content, $author]);
                $post_id = (int)$pdo->lastInsertId();
            }

            // Записуємо дані у відповідну дочірню таблицю
            if ($type === 'news') {
                $stmtNews = $pdo->prepare("INSERT INTO posts_news (post_id, post_date) VALUES (?, ?)");
                $stmtNews->execute([$post_id, $post_date]);
            } elseif ($type === 'tip') {
                $stmtTips = $pdo->prepare("INSERT INTO posts_tips (post_id, difficulty) VALUES (?, ?)");
                $stmtTips->execute([$post_id, $difficulty]);
            }

            $pdo->commit(); // Зберігаємо транзакцію в БД
            header("Location: admin.php"); // Повертаємось в адмінку
            exit;

        } catch (Exception $e) {
            $pdo->rollBack(); // Скасовуємо зміни у разі збою
            $error = "Помилка збереження в БД: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Редагувати' : 'Створити'; ?> публікацію</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; }
        .error-box { background: #ffebee; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ef9a9a; }
        .btn-save { background-color: #34495e; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background-color: #1abc9c; }
    </style>
    <script>
        // Динамічне перемикання полів залежно від типу поста
        function handleTypeFields() {
            var type = document.getElementById('type').value;
            document.getElementById('fields_news').style.display = (type === 'news') ? 'block' : 'none';
            document.getElementById('fields_tip').style.display = (type === 'tip') ? 'block' : 'none';
        }
        window.onload = function() { handleTypeFields(); }
    </script>
</head>
<body>
<header>
    <h1><?= $is_edit ? '✏ Редагування запису' : '➕ Створення публікації'; ?></h1>
    <p><a href="admin.php" style="color: #1abc9c; text-decoration: none; font-weight: bold;">⬅ Скасувати та повернутися</a></p>
</header>

<main style="display: block;">
    <div class="form-container">
        <?php if (!empty($error)): ?>
            <div class="error-box"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="type">Тип контенту</label>
                <select name="type" id="type" class="form-control" onchange="handleTypeFields()" <?= $is_edit ? 'disabled' : ''; ?>>
                    <option value="news" <?= $type === 'news' ? 'selected' : ''; ?>>Новина</option>
                    <option value="tip" <?= $type === 'tip' ? 'selected' : ''; ?>>Порада</option>
                </select>
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

            <button type="submit" class="btn-save"><?= $is_edit ? 'Зберегти зміни' : 'Опублікувати на сайт'; ?></button>
        </form>
    </div>
</main>
</body>
</html>