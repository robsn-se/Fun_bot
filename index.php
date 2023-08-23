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

    if (!isset($phpInput["update_id"])) {
        die();
    }

    addLog($phpInput, "from_telegram");



    if (@$phpInput["message"]) {
        $params["chat_id"] = $phpInput["message"]["chat"]["id"];
        $request = mb_strtolower($phpInput["message"]["text"]);
        $params["text"] =
            getAnswerByRules($request)

            ?? "{$phpInput["message"]["from"]["first_name"]}, я не понимаю тебя!\nЧто значит, '{$request}'?";
//        $params["reply_markup"] = '{"inline_keyboard":[
//    [
//        {"text":"Yes", "callback_data":"1"},
//        {"text":"No", "callback_data":"0"}
//    ],
//    [
//        {"text":"maybe", "callback_data":"maybe"}
//    ]
//]}';
        telegramAPIRequest("sendMessage", $params);
    }
}
catch (Throwable $e) {
    addLog(
        "{$e->getMessage()} | {$e->getFile()}({$e->getLine()}) \n{$e->getTraceAsString()} \n\n",
        "errors"
    );
}