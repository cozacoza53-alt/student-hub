<?php

declare(strict_types=1);

require 'db.php';

/*
|--------------------------------------------------------------------------
| INTERFACE
|--------------------------------------------------------------------------
*/

interface Publishable
{
    public function publish(): string;
}

/*
|--------------------------------------------------------------------------
| ABSTRACT CLASS
|--------------------------------------------------------------------------
*/

abstract class AbstractPost implements Publishable
{
    protected string $title;
    protected string $content;
    protected string $author;

    public function __construct(
        string $title,
        string $content,
        string $author
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
    }

    abstract public function publish(): string;
}

/*
|--------------------------------------------------------------------------
| NEWS POST
|--------------------------------------------------------------------------
*/

class NewsPost extends AbstractPost
{
    private string $date;

    public function __construct(
        string $title,
        string $content,
        string $author,
        string $date
    ) {
        parent::__construct($title, $content, $author);

        $this->date = $date;
    }

    public function publish(): string
    {
        return "
        <article class='oop-card'>
            <h3>" . htmlspecialchars($this->title) . "</h3>

            <p>" . htmlspecialchars($this->content) . "</p>

            <p><strong>Автор:</strong>
            " . htmlspecialchars($this->author) . "</p>

            <p><strong>Дата:</strong>
            " . htmlspecialchars($this->date) . "</p>
        </article>
        ";
    }
}

/*
|--------------------------------------------------------------------------
| TIP POST
|--------------------------------------------------------------------------
*/

class TipPost extends AbstractPost
{
    private int $difficulty;

    public function __construct(
        string $title,
        string $content,
        string $author,
        int $difficulty
    ) {
        parent::__construct($title, $content, $author);

        $this->difficulty = $difficulty;
    }

    public function publish(): string
    {
        return "
        <article class='oop-card'>
            <h3>" . htmlspecialchars($this->title) . "</h3>

            <p>" . htmlspecialchars($this->content) . "</p>

            <p><strong>Складність:</strong>
            {$this->difficulty}/5</p>

            <p><strong>Автор:</strong>
            " . htmlspecialchars($this->author) . "</p>
        </article>
        ";
    }
}

/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

$nav = [
    "Оголошення" => "#announcements",
    "Навчання" => "#study",
    "Опитування" => "#survey",
    "Контакти" => "#contacts"
];

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *
FROM posts
ORDER BY created_at DESC
";

$stmt = $pdo->query($sql);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$objects = [];

/*
|--------------------------------------------------------------------------
| CREATE OBJECTS
|--------------------------------------------------------------------------
*/

foreach ($posts as $post) {

    if ($post['post_type'] === 'news') {

        $dateStmt = $pdo->prepare("
            SELECT news_date
            FROM news_posts
            WHERE post_id = ?
        ");

        $dateStmt->execute([$post['id']]);

        $news = $dateStmt->fetch(PDO::FETCH_ASSOC);

        $objects[] = new NewsPost(
            $post['title'],
            $post['content'],
            $post['author'],
            $news['news_date'] ?? date('Y-m-d')
        );
    }

    if ($post['post_type'] === 'tip') {

        $difficultyStmt = $pdo->prepare("
            SELECT difficulty
            FROM tip_posts
            WHERE post_id = ?
        ");

        $difficultyStmt->execute([$post['id']]);

        $tip = $difficultyStmt->fetch(PDO::FETCH_ASSOC);

        $objects[] = new TipPost(
            $post['title'],
            $post['content'],
            $post['author'],
            (int)($tip['difficulty'] ?? 1)
        );
    }
}

?>

<!DOCTYPE html>
<html lang="uk">

<head>

    <meta charset="UTF-8">

    <title>Student Hub</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6f8;
            font-family: Arial, sans-serif;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            text-align: center;
            padding: 40px;
        }

        nav {
            background: #34495e;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;

            display: flex;
            justify-content: center;
            gap: 20px;
        }

        nav a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        nav a:hover {
            background: #1abc9c;
        }

        main {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        .oop-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;

            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .oop-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 12px;

            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        button {
            padding: 12px 20px;
            border: none;
            background: #34495e;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #1abc9c;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 40px;
        }

        footer a {
            color: #1abc9c;
        }

        @media (max-width: 700px) {

            nav ul {
                flex-direction: column;
                align-items: center;
            }

        }

    </style>

</head>

<body>

<header>

    <h1>Student Hub</h1>

    <p>
        Студентський портал для навчання,
        дедлайнів та корисних порад
    </p>

</header>

<nav>

    <ul>

        <?php foreach ($nav as $name => $link): ?>

            <li>
                <a href="<?= htmlspecialchars($link); ?>">
                    <?= htmlspecialchars($name); ?>
                </a>
            </li>

        <?php endforeach; ?>

    </ul>

</nav>

<main>

    <section id="announcements">

        <h2>ООП Публікації з MySQL</h2>

        <?php if (empty($objects)): ?>

            <div class="oop-card">
                <p>Постів поки немає.</p>
            </div>

        <?php endif; ?>

        <?php foreach ($objects as $object): ?>

            <?= $object->publish(); ?>

        <?php endforeach; ?>

    </section>

    <section id="study">

        <div class="oop-card">

            <h2>Навчання</h2>

            <ul>
                <li>Плануйте дедлайни заздалегідь</li>
                <li>Розбивайте великі задачі на маленькі</li>
                <li>Перевіряйте роботу перед здачею</li>
            </ul>

        </div>

    </section>

    <section id="survey">

        <h2>Опитування</h2>

        <form action="result.php" method="post">

            <label for="prep">
                Як ти оцінюєш свою підготовку?
            </label>

            <br><br>

            <select name="prep" id="prep" required>

                <option value="">-- Обери --</option>

                <option value="good">
                    Добре
                </option>

                <option value="normal">
                    Нормально
                </option>

            </select>

            <br><br>

            <button type="submit">

                Надіслати

            </button>

        </form>

    </section>

</main>

<footer id="contacts">

    <p>

        &copy; 2026 Student Hub

    </p>

    <p>

        Email:
        <a href="mailto:studenthub@example.com">
            studenthub@example.com
        </a>

    </p>

</footer>

</body>
</html>