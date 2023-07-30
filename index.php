<?php
require_once "config.php";
require_once "functions.php";

if (@$_GET["hook"]) {
    setHook((bool) $_GET["hook"]);
}

if (@$_GET["text"]) {
    $params["chat_id"] = ADMIN_ID;
    $params["text"] = @$_GET["text"] ?: "check";
    telegramAPIRequest("sendMessage", $params);
}

$phpInput = json_decode(file_get_contents("php://input"), true);

if (!isset($phpInput["update_id"])) {
    die();
}

addLog($phpInput, "from_telegram");

if (@$phpInput["message"]) {
    $params["chat_id"] = $phpInput["message"]["chat"]["id"];
    if (mb_strtolower($phpInput["message"]["text"]) == "привет") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "hi") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "здарова") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "здравствуйте") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "приветствую") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "hello") {
        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "Как дела?") {
        $params["text"] = "Отлично, спасибо!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "Как дела") {
        $params["text"] = "Отлично, спасибо!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "как ты?") {
        $params["text"] = "Отлично, спасибо!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "как ты") {
        $params["text"] = "Отлично, спасибо!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "пока") {
        $params["text"] = "Пока, {$phpInput["message"]["from"]["first_name"]}!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "досвидания") {
        $params["text"] = "Досвидания, {$phpInput["message"]["from"]["first_name"]}!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "что делаешь") {
        $params["text"] = "Учу программирование!";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "откуда ты") {
        $params["text"] = "я из телеграмма";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "как дела") {
        $params["text"] = "нормально, спасибо";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "как дела?") {
        $params["text"] = "нормально, спасибо";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "сколько тебе лет?") {
        $params["text"] = "Меня создали недавно";
    }
    elseif (mb_strtolower($phpInput["message"]["text"]) == "сколько тебе лет") {
        $params["text"] = "Меня создали недавно";
    }
    else {
        $params["text"] = "{$phpInput["message"]["from"]["first_name"]}, я не понимаю тебя!\nЧто значит, '{$phpInput["message"]["text"]}'?";
    }
    telegramAPIRequest("sendMessage", $params);
}
