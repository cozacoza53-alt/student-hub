<?php

declare(strict_types=1);

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
        $this->setTitle($title);
        $this->setContent($content);
        $this->setAuthor($author);
    }

    public function setTitle(string $title): void
    {
        if (mb_strlen($title) < 3) {
            throw new Exception("Назва повинна містити мінімум 3 символи.");
        }
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        if (empty(trim($content))) {
            throw new Exception("Контент не може бути порожнім.");
        }
        $this->content = $content;
    }

    public function setAuthor(string $author): void
    {
        if (mb_strlen($author) < 2) {
            throw new Exception("Ім’я автора закоротке.");
        }
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
        $title = htmlspecialchars($this->title);
        $content = htmlspecialchars($this->content);
        $author = htmlspecialchars($this->author);
        $date = htmlspecialchars($this->date);

        return "
        <article class='oop-card'>
            <h3>{$title}</h3>
            <section>
                <p>{$content}</p>
                <p><strong>Автор:</strong> {$author}</p>
                <p><strong>Дата:</strong> {$date}</p>
            </section>
        </article>
        ";
    }

    public function getShortContent(): string
    {
        return mb_substr($this->content, 0, 50) . "...";
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
        $this->setDifficulty($difficulty);
    }

    public function setDifficulty(int $difficulty): void
    {
        if ($difficulty < 1 || $difficulty > 5) {
            throw new Exception("Складність повинна бути від 1 до 5.");
        }
        $this->difficulty = $difficulty;
    }

    public function publish(): string
    {
        $title = htmlspecialchars($this->title);
        $content = htmlspecialchars($this->content);
        $author = htmlspecialchars($this->author);

        return "
        <article class='oop-card'>
            <h3>{$title}</h3>
            <section>
                <p>{$content}</p>
                <p><strong>Складність:</strong> {$this->difficulty}/5</p>
                <p><strong>Автор:</strong> {$author}</p>
            </section>
        </article>
        ";
    }

    public function isHard(): bool
    {
        return $this->difficulty >= 4;
    }
}

/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

class Category
{
    private string $name;
    private array $posts = [];

    public function __construct(string $name)
    {
        if (mb_strlen($name) < 3) {
            throw new Exception("Назва категорії закоротка.");
        }
        $this->name = $name;
    }

    public function addPost(Publishable $post): void
    {
        $this->posts[] = $post;
    }

    public function getPosts(): array
    {
        return $this->posts;
    }
}

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$nav = [
    "Оголошення" => "#announcements",
    "Навчання" => "#study",
    "Поради" => "#tips",
    "Контакти" => "#contacts",
    "Додатково" => "#pip"
];

$mistakes = [
    "Починати роботу без читання умови",
    "Не зберігати проміжні результати",
    "Не перевіряти вимоги до оформлення",
    "Здавати роботу без тестування"
];

$steps = [
    "Запиши дедлайни на тиждень",
    "Розбий завдання на кроки",
    "Залиш 1 день резервний"
];

$aside_tips = [
    "Перевір дедлайни на початку тижня",
    "Пиши викладачу заздалегідь",
    "Не здавай роботу без перевірки"
];

/*
|--------------------------------------------------------------------------
| OOP OBJECTS (ВИПРАВЛЕНО ЛОГІКУ)
|--------------------------------------------------------------------------
*/

$category = new Category("Student Hub");
$system_errors = []; // Збираємо помилки тут

// Створюємо кожен пост окремо, щоб помилка в одному не зупиняла інші
try {
    $post1 = new NewsPost("Розклад консультацій", "Кафедра опублікувала графік консультацій для студентів перед сесією.", "Адміністрація", "2026-02-08");
    $category->addPost($post1);
} catch (Exception $e) { $system_errors[] = $e->getMessage(); }

try {
    $post2 = new TipPost("Як здати лабораторні роботи", "Розбий завдання на маленькі кроки та залиш резервний день.", "Викладач", 3);
    $category->addPost($post2);
} catch (Exception $e) { $system_errors[] = $e->getMessage(); }

try {
    // НЕВАЛІДНИЙ ОБ’ЄКТ - викличе помилку
    $post3 = new TipPost("Hi", "test", "A", 10);
    $category->addPost($post3);
} catch (Exception $e) { $system_errors[] = $e->getMessage(); }

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Головна | Student Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
        .oop-card { background: #ffffff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .error-message { background: #ffebee; color: #c62828; padding: 15px; margin: 20px 0; border-radius: 10px; border: 1px solid #ef9a9a; }
        header, nav, main, aside, footer { margin-bottom: 30px; }
        nav ul { list-style: none; padding: 0; display: flex; gap: 15px; background: #34495e; padding: 10px; border-radius: 5px; }
        nav ul li a { color: white; text-decoration: none; }
        form { background: #fefefe; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<header>
    <h1>Student Hub</h1>
    <p>Студентський портал для навчання, дедлайнів та корисних порад.</p>
</header>

<nav>
    <ul>
        <?php foreach ($nav as $name => $link): ?>
            <li><a href="<?= htmlspecialchars($link); ?>"><?= htmlspecialchars($name); ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>

<main>
    <!-- ПОВІДОМЛЕННЯ ПРО ПОМИЛКИ -->
    <?php if (!empty($system_errors)): ?>
        <div class="error-message">
            <strong>Повідомлення системи:</strong>
            <ul>
                <?php foreach ($system_errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- OOP POSTS -->
    <section>
        <h2>ООП Публікації</h2>
        <?php foreach ($category->getPosts() as $post): ?>
            <?= $post->publish(); ?>
        <?php endforeach; ?>
    </section>

    <!-- BUSINESS LOGIC (БЕЗПЕЧНО) -->
    <section class="oop-card">
        <h2>Бізнес-логіка</h2>
        <?php if (isset($post1)): ?>
            <p><strong>Скорочений текст новини:</strong> <?= $post1->getShortContent(); ?></p>
        <?php endif; ?>

        <?php if (isset($post2)): ?>
            <p><strong>Порада складна?</strong> <?= $post2->isHard() ? 'Так' : 'Ні'; ?></p>
        <?php endif; ?>
    </section>

    <!-- ORIGINAL CONTENT -->
    <h2 id="announcements">Оголошення</h2>
    <article class="oop-card">
        <h3>Розклад консультацій перед сесією</h3>
        <p>Дата публікації: <time datetime="2026-02-08">8 лютого 2026</time></p>
        <blockquote>Краще прийти на консультацію з 3 конкретними питаннями.</blockquote>
    </article>

    <h2 id="study">Навчання</h2>
    <article class="oop-card">
        <h3>Типові помилки при здачі робіт</h3>
        <ul>
            <?php foreach ($mistakes as $m): ?>
                <li><?= htmlspecialchars($m); ?></li>
            <?php endforeach; ?>
        </ul>
    </article>

    <h2 id="survey">Опитування</h2>
    <form action="result.php" method="post">
        <label for="prep">Як ти оцінюєш підготовку?</label><br>
        <select name="prep" id="prep" required>
            <option value="">-- Обери --</option>
            <option value="good">Добре</option>
            <option value="normal">Нормально</option>
        </select>
        <br><br>
        <button type="submit" style="padding:10px; background:#34495e; color:white; border:none; border-radius:5px; cursor:pointer;">Надіслати</button>
    </form>
</main>

<footer id="contacts">
    <hr>
    <p>&copy; 2026 Student Hub | Email: <a href="mailto:studenthub@example.com">studenthub@example.com</a></p>
</footer>

</body>
</html>