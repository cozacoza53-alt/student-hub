-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 19 2026 г., 22:11
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `blog_system`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Student Hub');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `type` enum('news','tip') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `type`, `title`, `content`, `author`, `created_at`) VALUES
(1, 1, 'news', 'Розклад консультацій', 'Кафедра опублікувала графік консультацій для студентів перед сесією.', 'Адміністрація', '2026-05-19 20:02:16'),
(2, 1, 'tip', 'Як здати лабораторні роботи', 'Розбий завдання на маленькі кроки та залиш резервний день.', 'Викладач', '2026-05-19 20:02:16');

-- --------------------------------------------------------

--
-- Структура таблицы `posts_news`
--

CREATE TABLE `posts_news` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `post_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts_news`
--

INSERT INTO `posts_news` (`post_id`, `post_date`) VALUES
(1, '2026-02-08');

-- --------------------------------------------------------

--
-- Структура таблицы `posts_tips`
--

CREATE TABLE `posts_tips` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `difficulty` tinyint(4) NOT NULL
) ;

--
-- Дамп данных таблицы `posts_tips`
--

INSERT INTO `posts_tips` (`post_id`, `difficulty`) VALUES
(2, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `response_tools`
--

CREATE TABLE `response_tools` (
  `response_id` int(10) UNSIGNED NOT NULL,
  `tool_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `response_tools`
--

INSERT INTO `response_tools` (`response_id`, `tool_id`) VALUES
(1, 1),
(1, 3),
(2, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(10) UNSIGNED NOT NULL,
  `prep` varchar(20) NOT NULL,
  `study_time` varchar(10) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `prep`, `study_time`, `submitted_at`) VALUES
(1, 'normal', '2', '2026-05-19 20:02:16'),
(2, 'good', '2', '2026-05-19 20:08:59');

-- --------------------------------------------------------

--
-- Структура таблицы `tools`
--

CREATE TABLE `tools` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tools`
--

INSERT INTO `tools` (`id`, `code`, `name`) VALUES
(1, 'notes', 'конспекти'),
(2, 'youtube', 'YouTube'),
(3, 'chatgpt', 'ChatGPT');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_category` (`category_id`),
  ADD KEY `idx_posts_type` (`type`);

--
-- Индексы таблицы `posts_news`
--
ALTER TABLE `posts_news`
  ADD PRIMARY KEY (`post_id`);

--
-- Индексы таблицы `posts_tips`
--
ALTER TABLE `posts_tips`
  ADD PRIMARY KEY (`post_id`);

--
-- Индексы таблицы `response_tools`
--
ALTER TABLE `response_tools`
  ADD PRIMARY KEY (`response_id`,`tool_id`),
  ADD KEY `fk_res_tools_tool` (`tool_id`);

--
-- Индексы таблицы `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_survey_prep` (`prep`);

--
-- Индексы таблицы `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `tools`
--
ALTER TABLE `tools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `posts_news`
--
ALTER TABLE `posts_news`
  ADD CONSTRAINT `fk_news_parent` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `posts_tips`
--
ALTER TABLE `posts_tips`
  ADD CONSTRAINT `fk_tips_parent` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `response_tools`
--
ALTER TABLE `response_tools`
  ADD CONSTRAINT `fk_res_tools_response` FOREIGN KEY (`response_id`) REFERENCES `survey_responses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_tools_tool` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
