<?php
require_once "config.php";
require_once "functions.php";
require_once "rules.php";

try {
    if (@$_GET["hook"]) {
        setHook((bool) $_GET["hook"]);
    }

    if (@$_GET["text"]) {
        $params["chat_id"] = ADMIN_ID;
        $params["text"] = @$_GET["text"] ?: "check";
        telegramAPIRequest("sendMessage", $params);
    }

    $phpInput = json_decode(file_get_contents("php://input"), true);
//    "php://input"  позволяет читать необработанные данные из тела запроса, сюда приходят данные из телеграпм API
//    file_get_contents() возвращает содержимое файла в строке,
//    мы получаем в нашем случае данные в формате json (документация телеграмм API)
//    json_decode декодирует наши данные формата json в ассоциативный массив(потому что параметр: associative true)


    if (!isset($phpInput["update_id"])) {
        die();
    }
//    если уникальный идентификатор не существует, тогда код умирает


//    далее срабатывает функция addLog($phpInput, "from_telegram")
//    необходима для добавления данных из Telegram в файл c логами
//    первый параметр - массив данных от телеграмм API;
//    второй параметр - "from_telegram" имя файла, куда записать данные.
    addLog($phpInput, "from_telegram");


//  если объект "message" существует и не пустой, тогда
    if (@$phpInput["message"]) {

//      создаем переменную $params с ключом["chat_id"] ей присваиваем $phpInput["message"]["chat"]["id"]
        $params["chat_id"] = $phpInput["message"]["chat"]["id"]; //"id пользователя с которым переписываемся"

//      создаем переменную $request ей присваиваем результат !!$phpInput["message"]["text"]!! это сообщение от пользователя
//      функция mb_strtolower() преобразует все символы в нижний регистр
//      В переменной $request содержится !!!СООБЩЕНИЕ ПОЛЬЗОВАТЕЛЯ!!!
        $request = mb_strtolower($phpInput["message"]["text"]);

//      переменной $params создаем новый ключ ["text"] и присваиваем ей результат условия,
//      если getAnswerByRules($request)


        $params["text"] =
            getAnswerByRules($request)
            ?? "{$phpInput["message"]["from"]["first_name"]}, я не понимаю тебя!\nЧто значит, '{$request}'?";
        telegramAPIRequest("sendMessage", $params);
    }
}
catch (Throwable $e) {
    addLog(
        "{$e->getMessage()} | {$e->getFile()}({$e->getLine()}) \n{$e->getTraceAsString()} \n\n",
        "errors"
    );
}