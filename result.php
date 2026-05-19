<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Результат опитування</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php

// 1. Отримуємо дані з форми (суперглобальний масив)
$prep = $_POST['prep'] ?? '';
$time = $_POST['time'] ?? '';
$tools = $_POST['tools'] ?? [];

// 2. ПІДКЛЮЧЕННЯ ДО БД ТА ЗБЕРЕЖЕННЯ РЕЗУЛЬТАТІВ
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($prep)) {
    try {
        // Вставляємо основні дані опитування (підготовка та час)
        $stmt = $pdo->prepare("INSERT INTO survey_responses (prep, study_time) VALUES (?, ?)");
        $stmt->execute([$prep, $time]);
        
        // Отримуємо ID цього конкретного заповнення форми
        $response_id = $pdo->lastInsertId();

        // Якщо студент обрав хоча б один інструмент, записуємо зв'язки в БД
        if (!empty($tools)) {
            foreach ($tools as $tool_code) {
                // Дізнаємося ID інструменту з довідника за його кодом (notes, youtube, chatgpt)
                $stmtTool = $pdo->prepare("SELECT id FROM tools WHERE code = ?");
                $stmtTool->execute([$tool_code]);
                $tool = $stmtTool->fetch();

                if ($tool) {
                    // Записуємо зв'язок у таблицю зв'язку багато-до-багатьох
                    $stmtLink = $pdo->prepare("INSERT INTO response_tools (response_id, tool_id) VALUES (?, ?)");
                    $stmtLink->execute([$response_id, $tool['id']]);
                }
            }
        }
    } catch (Exception $e) {
        echo "<div style='background: #ffebee; color: #c62828; padding: 15px; margin: 20px; border-radius: 8px;'>";
        echo "<strong>Помилка збереження в БД:</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

// --- ТВОЇ ОРИГІНАЛЬНІ ФУНКЦІЇ ДЛЯ ОБРОБКИ ТА ВИВОДУ ---

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

// --- ВИВІД НА СТОРІНКУ ---

echo "<header><h1>Результати опитування</h1></header>";

echo "<main>";
echo "<article class='oop-card' style='width: 100%;'>";
echo "<h3>Дякуємо за відповідь! Дані успішно збережено в БД.</h3>";
echo "<p>" . checkPrep($prep) . "</p>";
echo "<p>" . checkTime($time) . "</p>";
echo "<p>" . checkTools($tools) . "</p>";

echo "<br><a href='index.php' style='color: #1abc9c; text-decoration: none;'>⬅ Повернутися назад</a>";
echo "</article>";
echo "</main>";

?>

</body>
</html>