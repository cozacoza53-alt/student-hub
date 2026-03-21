<!DOCTYPE html>
<html lang="uk">
<head>
<link rel="stylesheet" href="style.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Головна | Student Hub</title>

<meta name="description" content="Student Hub — навчальний приклад інформаційного порталу для студентів.">
</head>

<body>

<?php
// Заголовок + Hello World
echo "<header>";
echo "<h1>Student Hub</h1>";
echo "<p>Hello world</p>";
echo "<p>Студентський портал для навчання, дедлайнів та корисних порад.</p>";
echo "</header>";

// Масив навігації
$nav = [
  "Оголошення" => "#announcements",
  "Навчання" => "#study",
  "Поради" => "#tips",
  "Контакти" => "#contacts",
  "Додатково" => "#pip"
];
?>

<nav>
  <ul>
    <?php
    foreach ($nav as $name => $link) {
        echo "<li><a href='$link'>$name</a></li>";
    }
    ?>
  </ul>
</nav>

<main>

<h2 id="announcements">Оголошення</h2>

<figure>
  <img src="images/5fa51771cbae7647226875.gif" alt="HTML5" width="200">
  <figcaption>20.02.2026 року відбудеться дедлайн</figcaption>
</figure>

<article>
  <h3>Розклад консультацій перед сесією</h3>

  <section>
    <h4>Основна інформація</h4>
    <p>
      Кафедра опублікувала графік консультацій для студентів перед екзаменаційною сесією.
    </p>
    <p>
      Дата публікації:
      <time datetime="2026-02-08">8 лютого 2026</time>
    </p>
  </section>

  <section>
    <h4>Пояснення для студентів</h4>
    <p>
      Якщо ти пропустив(ла) лекції або хочеш уточнити питання перед екзаменом —
      консультація допоможе.
    </p>

    <blockquote>
      <p>
        Краще прийти на консультацію з 3 конкретними питаннями.
      </p>
    </blockquote>
  </section>
</article>

<h2 id="study">Навчання</h2>

<article>
  <h3>Як здати лабораторні роботи без стресу</h3>

  <section>
    <h4>План роботи</h4>
    <p>
      Якщо розбити завдання на маленькі кроки — стає легше.
    </p>
  </section>

  <section>
    <h4>Типові помилки</h4>

    <?php
    $mistakes = [
      "Починати роботу без читання умови",
      "Не зберігати проміжні результати",
      "Не перевіряти вимоги до оформлення",
      "Здавати роботу без тестування"
    ];
    ?>

    <ul>
      <?php
      for ($i = 0; $i < count($mistakes); $i++) {
          echo "<li>$mistakes[$i]</li>";
      }
      ?>
    </ul>
  </section>

</article>

<h2 id="tips">Поради</h2>

<article>
  <h3>Як організувати навчання</h3>

  <section>
    <h4>Міні-інструкція</h4>

    <?php
    $steps = [
      "Запиши дедлайни на тиждень",
      "Розбий завдання на кроки",
      "Залиш 1 день резервний"
    ];
    ?>

    <ol>
      <?php
      foreach ($steps as $step) {
          echo "<li>$step</li>";
      }
      ?>
    </ol>
  </section>
</article>

</main>

<aside>
<h2 id="pip">Додатково</h2>

<section>
<h3>Швидка пам’ятка студенту</h3>

<?php
$tips = [
  "Перевір дедлайни на початку тижня",
  "Пиши викладачу заздалегідь",
  "Не здавай роботу без перевірки"
];
?>

<ul>
<?php
$i = 0;
while ($i < count($tips)) {
    echo "<li>$tips[$i]</li>";
    $i++;
}
?>
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