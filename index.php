<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| INTERFACE & CLASSES (Твой оригинальный ООП код)
|--------------------------------------------------------------------------
*/

interface Publishable
{
    public function publish(): string;
}

abstract class AbstractPost implements Publishable
{
    protected string $title;
    protected string $content;
    protected string $author;

    public function __construct(string $title, string $content, string $author) {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setAuthor($author);
    }

    public function setTitle(string $title): void {
        if (mb_strlen($title) < 3) {
            throw new Exception("Назва повинна містити мінімум 3 символи.");
        }
        $this->title = $title;
    }

    public function setContent(string $content): void {
        if (empty(trim($content))) {
            throw new Exception("Контент не може бути порожнім.");
        }
        $this->content = $content;
    }

    public function setAuthor(string $author): void {
        if (mb_strlen($author) < 2) {
            throw new Exception("Ім’я автора закоротке.");
        }
        $this->author = $author;
    }

    abstract public function publish(): string;
}

class NewsPost extends AbstractPost
{
    private string $date;

    public function __construct(string $title, string $content, string $author, string $date) {
        parent::__construct($title, $content, $author);
        $this->date = $date;
    }

    public function publish(): string {
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

    public function getShortContent(): string {
        return mb_substr($this->content, 0, 50) . "...";
    }
}

class TipPost extends AbstractPost
{
    private int $difficulty;

    public function __construct(string $title, string $content, string $author, int $difficulty) {
        parent::__construct($title, $content, $author);
        $this->setDifficulty($difficulty);
    }

    public function setDifficulty(int $difficulty): void {
        if ($difficulty < 1 || $difficulty > 5) {
            throw new Exception("Складність повинна бути від 1 до 5.");
        }
        $this->difficulty = $difficulty;
    }

    public function publish(): string {
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

    public function isHard(): bool {
        return $this->difficulty >= 4;
    }
}

class Category
{
    private string $name;
    private array $posts = [];

    public function __construct(string $name) {
        if (mb_strlen($name) < 3) {
            throw new Exception("Назва категорії закоротка.");
        }
        $this->name = $name;
    }

    public function addPost(Publishable $post): void {
        $this->posts[] = $post;
    }

    public function getPosts(): array {
        return $this->posts;
    }
}

/*
|--------------------------------------------------------------------------
| ТВОИ СТАТИЧЕСКИЕ ДАННЫЕ ДЛЯ ВЕБ-СТРАНИЦЫ
|--------------------------------------------------------------------------
*/
$nav = [
    "Оголошення" => "#announcements",
    "Навчання" => "#study",
    "Поради" => "#tips",
    "Контакти" => "#contacts",
    "Додатково" => "#pip",
    "Адмінка ⚙️"  => "admin.php" // 🔥 Додано посилання на майбутню сторінку адмінки
];

$mistakes = [
    "Починати роботу без читання умови",
    "Не зберігати проміжні результати",
    "Не перевіряти вимоги до оформлення",
    "Здавати роботу без тестування"
];

/*
|--------------------------------------------------------------------------
| ДИНАМИЧЕСКОЕ ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ (НОВЫЙ КОД)
|--------------------------------------------------------------------------
*/
require_once 'db.php'; // Подключаем файл с настройками PDO

$category = new Category("Student Hub");
$system_errors = [];
$dynamic_news_post = null;
$dynamic_tip_post = null;

try {
    // 1. Робимо запит до БД, сортуючи пости за ID від новіших до старіших
    $query = "
        SELECT 
            p.id, p.type, p.title, p.content, p.author,
            pn.post_date,
            pt.difficulty
        FROM posts p
        LEFT JOIN posts_news pn ON p.id = pn.post_id
        LEFT JOIN posts_tips pt ON p.id = pt.post_id
        ORDER BY p.id DESC
    ";
    
    $stmt = $pdo->query($query);
    $rows = $stmt->fetchAll();

    // 2. Превращаем строки из БД в ООП-объекты
    foreach ($rows as $row) {
        if ($row['type'] === 'news') {
            $post = new NewsPost($row['title'], $row['content'], $row['author'], $row['post_date']);
            if (!$dynamic_news_post) $dynamic_news_post = $post; // Сохраняем первую новость для теста бизнес-логики ниже
        } elseif ($row['type'] === 'tip') {
            $post = new TipPost($row['title'], $row['content'], $row['author'], (int)$row['difficulty']);
            if (!$dynamic_tip_post) $dynamic_tip_post = $post;   // Сохраняем первый совет для теста бизнес-логики ниже
        }
        
        $category->addPost($post);
    }

} catch (Exception $e) {
    $system_errors[] = "Помилка системи: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Головна | Student Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
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
    <?php if (!empty($system_errors)): ?>
        <div class="error-message" style="background: #ffebee; color: #c62828; padding: 15px; margin: 20px 0; border-radius: 10px; border: 1px solid #ef9a9a;">
            <strong>Повідомлення системи:</strong>
            <ul>
                <?php foreach ($system_errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section style="width: 100%;">
        <h2>ООП Публікації (Завантажено з БД)</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php foreach ($category->getPosts() as $post): ?>
                <?= $post->publish(); ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="oop-card" style="width: 100%; margin-top: 20px;">
        <h2>Бізнес-логіка (Перевірка методів класів)</h2>
        <?php if ($dynamic_news_post): ?>
            <p><strong>Скорочений текст першої новини:</strong> <?= $dynamic_news_post->getShortContent(); ?></p>
        <?php else: ?>
            <p style="color: gray;">Новин в базі данных не знайдено.</p>
        <?php endif; ?>

        <?php if ($dynamic_tip_post): ?>
            <p><strong>Перша порада складна?</strong> <?= $dynamic_tip_post->isHard() ? 'Так' : 'Ні'; ?></p>
        <?php else: ?>
            <p style="color: gray;">Порад в базі даних не знайдено.</p>
        <?php endif; ?>
    </section>

    <h2 id="announcements" style="width: 100%; margin-top: 40px;">Оголошення</h2>
    <article class="oop-card">
        <h3>Розклад консультацій перед сесією</h3>
        <p>Дата публікації: <time datetime="2026-02-08">8 лютого 2026</time></p>
        <blockquote>Краще прийти на консультацію з 3 конкретними питаннями.</blockquote>
    </article>

    <h2 id="study" style="width: 100%;">Навчання</h2>
    <article class="oop-card">
        <h3>Типові помилки при здачі робіт</h3>
        <ul>
            <?php foreach ($mistakes as $m): ?>
                <li><?= htmlspecialchars($m); ?></li>
            <?php endforeach; ?>
        </ul>
    </article>

    <h2 id="survey" style="width: 100%;">Опитування</h2>
    <form action="result.php" method="post" style="background: #fefefe; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); width: 100%;">
        <label for="prep">Як ти оцінюєш підготовку?</label><br><br>
        <select name="prep" id="prep" required style="padding: 5px; width: 200px;">
            <option value="">-- Обери --</option>
            <option value="good">Добре</option>
            <option value="normal">Нормально</option>
            <option value="bad">Погано</option>
        </select>
        <br><br>
        
        <label>Які ресурси використовуєш? (можна декілька)</label><br><br>
        <input type="checkbox" name="tools[]" value="notes" id="tool_notes"> <label for="tool_notes">Конспекти</label><br>
        <input type="checkbox" name="tools[]" value="youtube" id="tool_yt"> <label for="tool_yt">YouTube</label><br>
        <input type="checkbox" name="tools[]" value="chatgpt" id="tool_gpt"> <label for="tool_gpt">ChatGPT</label><br>
        <br>
        
        <input type="hidden" name="time" value="2"> <button type="submit" style="padding:10px 20px; background:#34495e; color:white; border:none; border-radius:5px; cursor:pointer;">Надіслати</button>
    </form>
</main>

<footer id="contacts">
    <p>&copy; 2026 Student Hub | Email: <a href="mailto:studenthub@example.com">studenthub@example.com</a></p>
</footer>

</body>
</html>