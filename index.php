<?php
// --- ДАНІ ТА ЛОГІКА (краще тримати на початку файлу) ---

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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Головна | Student Hub</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Student Hub — навчальний приклад інформаційного порталу для студентів.">
</head>
<body>

<header>
    <h1>Student Hub</h1>
    <p>Hello world</p>
    <p>Студентський портал для навчання, дедлайнів та корисних порад.</p>
</header>

<nav>
    <ul>
        <?php foreach ($nav as $name => $link): ?>
            <li><a href="<?php echo htmlspecialchars($link); ?>"><?php echo htmlspecialchars($name); ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>

<main>

    <h2 id="announcements">Оголошення</h2>
    <figure>
        <img src="images/5fa51771cbae7647226875.gif" alt="Іконка дедлайну" width="200">
        <figcaption>20.02.2026 року відбудеться дедлайн</figcaption>
    </figure>

    <article>
        <h3>Розклад консультацій перед сесією</h3>
        <section>
            <h4>Основна інформація</h4>
            <p>Кафедра опублікувала графік консультацій для студентів перед екзаменаційною сесією.</p>
            <p>Дата публікації: <time datetime="2026-02-08">8 лютого 2026</time></p>
        </section>

        <section>
            <h4>Пояснення для студентів</h4>
            <p>Якщо ти пропустив(ла) лекції або хочеш уточнити питання перед екзаменом — консультація допоможе.</p>
            <blockquote>
                <p>Краще прийти на консультацію з 3 конкретними питаннями.</p>
            </blockquote>
        </section>
    </article>

    <h2 id="study">Навчання</h2>
    <article>
        <h3>Як здати лабораторні роботи без стресу</h3>
        <section>
            <h4>План роботи</h4>
            <p>Якщо розбити завдання на маленькі кроки — стає легше.</p>
        </section>

        <section>
            <h4>Типові помилки</h4>
            <ul>
                <?php foreach ($mistakes as $m): ?>
                    <li><?php echo htmlspecialchars($m); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    </article>

    <h2 id="tips">Поради</h2>
    <article>
        <h3>Як організувати навчання</h3>
        <section>
            <h4>Міні-інструкція</h4>
            <ol>
                <?php foreach ($steps as $step): ?>
                    <li><?php echo htmlspecialchars($step); ?></li>
                <?php endforeach; ?>
            </ol>
        </section>
    </article>

    <h2 id="survey">Опитування для студентів</h2>
    <article>
        <h3>Тестова форма опитування</h3>
        <form action="result.php" method="post" style="background:#fefefe;padding:20px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.1);">
            <label for="prep">Як ти оцінюєш свою підготовку?</label><br>
            <select name="prep" id="prep" required>
                <option value="">-- Обери варіант --</option>
                <option value="good">Добре</option>
                <option value="normal">Нормально</option>
                <option value="bad">Погано</option>
            </select>
            <br><br>

            <label>Скільки часу ти приділяєш навчанню на день?</label><br>
            <input type="radio" name="time" value="1" required> Менше 1 години<br>
            <input type="radio" name="time" value="2"> 1-3 години<br>
            <input type="radio" name="time" value="3"> Більше 3 годин<br>
            <br>

            <label>Які ресурси ти використовуєш?</label><br>
            <input type="checkbox" name="tools[]" value="notes"> Конспекти<br>
            <input type="checkbox" name="tools[]" value="youtube"> YouTube<br>
            <input type="checkbox" name="tools[]" value="chatgpt"> ChatGPT<br>
            <br>

            <button type="submit" style="padding:10px 20px;background:#34495e;color:white;border:none;border-radius:6px;cursor:pointer;">
                Надіслати
            </button>
        </form>
    </article>

</main>

<aside>
    <h2 id="pip">Додатково</h2>
    <section>
        <h3>Швидка пам’ятка студенту</h3>
        <ul>
            <?php foreach ($aside_tips as $t): ?>
                <li><?php echo htmlspecialchars($t); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</aside>

<footer id="contacts">
    <h2>Контакти</h2>
    <address>
        <p>Student Hub — навчальний портал</p>
        <p>Email: <a href="mailto:studenthub@example.com">studenthub@example.com</a></p>
        <p>Телефон: <a href="tel:+380000000000">+38 (000) 000-00-00</a></p>
    </address>
    <p>Останнє оновлення: <time datetime="2026-02-08">8 лютого 2026</time></p>
    <p>&copy; 2026 Student Hub</p>
</footer>

</body>
</html>