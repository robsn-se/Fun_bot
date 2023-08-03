<?php
require_once "config.php";
require_once "functions.php";
require_once "rules.php";


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

function getRandomItem(array $array): mixed {
    $item = $array[rand(0, count($array) - 1)];
    if (is_string($item)) {
//        $item[0] = mb_strtoupper($item[0]);
        return $item;
    }
    return $item;
}


if (@$phpInput["message"]) {
    $params["chat_id"] = $phpInput["message"]["chat"]["id"];
    $request = mb_strtolower($phpInput["message"]["text"]);
    $params["text"] = "{$phpInput["message"]["from"]["first_name"]}, я не понимаю тебя!\nЧто значит, '{$phpInput["message"]["text"]}'?";
    foreach ($rules as $rule) {
        if (in_array($request, $rule[REQUESTS_KEY])) {
            $params["text"] = getRandomItem($rule[RESPONSES_KEY][WORLDS_KEY]);
            if (isset($rule[RESPONSES_KEY][SIGNS_KEY]))  {
                $params["text"] .= getRandomItem($rule[RESPONSES_KEY][SIGNS_KEY]);
            }
            if (isset($rule[SUB_RESPONSES_KEY])) {
                $params["text"] .= " " . getRandomItem($rule[SUB_RESPONSES_KEY][WORLDS_KEY]);
                if (isset($rule[SUB_RESPONSES_KEY][SIGNS_KEY]))  {
                    $params["text"] .= getRandomItem($rule[SUB_RESPONSES_KEY][SIGNS_KEY]);
                }
            }
        }
    }
    telegramAPIRequest("sendMessage", $params);
}