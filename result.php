<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Результат</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php

// Отримуємо дані з форми (суперглобальний масив)
$prep = $_POST['prep'] ?? '';
$time = $_POST['time'] ?? '';
$tools = $_POST['tools'] ?? [];

// --- ФУНКЦІЇ ---

// match
function checkPrep($prep) {
    return match($prep) {
        'good' => "Ти добре підготовлений 😎",
        'normal' => "Нормальна підготовка 👍",
        'bad' => "Треба більше вчитися 😬",
        default => "Невідомий рівень підготовки"
    };
}

// switch
function checkTime($time) {
    switch ($time) {
        case '1':
            return "Ти мало вчишся 😅";
        case '2':
            return "Непогано 👍";
        case '3':
            return "Дуже старанно 🔥";
        default:
            return "Час не вказано";
    }
}

// if + цикл
function checkTools($tools) {
    if (empty($tools)) {
        return "Ти не обрав жодного ресурсу 😢";
    }

    $result = "Ти використовуєш: ";

    foreach ($tools as $tool) {
        if ($tool == 'notes') {
            $result .= "конспекти ";
        } elseif ($tool == 'youtube') {
            $result .= "YouTube ";
        } elseif ($tool == 'chatgpt') {
            $result .= "ChatGPT ";
        }
    }

    return $result;
}

// --- ВИВІД ---

echo "<header><h1>Результати опитування</h1></header>";

echo "<main>";

echo "<p>" . checkPrep($prep) . "</p>";
echo "<p>" . checkTime($time) . "</p>";
echo "<p>" . checkTools($tools) . "</p>";

echo "<br><a href='index.php'>⬅ Повернутися назад</a>";

echo "</main>";

?>

</body>
</html>