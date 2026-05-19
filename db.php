<?php

try {

    $pdo = new PDO(
        "mysql:host=localhost;dbname=blog_system;charset=utf8",
        "root",
        ""
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die(
        "Помилка підключення: "
        . $e->getMessage()
    );

}