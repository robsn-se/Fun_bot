<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once "config.php";
require_once "functions.php";
require_once "rules.php";

try {
    if (isset($_GET["hook"])) {
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
    telegramAPIRequest("sendChatAction", [
        "action" => "typing",
        "chat_id" => @$phpInput["callback_query"]["message"]["chat"]["id"] ?? @$phpInput["message"]["chat"]["id"]
    ]);

    if (@$phpInput["callback_query"]) {
        $params = [];
        $dictionaryButtons = [];
        $callbackQueryParams = explode(CALLBACK_DATA_DELIMITER, $phpInput["callback_query"]["data"]);
        $params["chat_id"] = $phpInput["callback_query"]["message"]["chat"]["id"];
        $params["message_id"] = $phpInput["callback_query"]["message"]["message_id"];
        if ($callbackQueryParams[0] == "add_to_dict") {
            if ($callbackQueryParams[1] == "no") {
                $params["text"] = substr(
                    $phpInput["callback_query"]["message"]["text"],
                    0,
                    strpos($phpInput["callback_query"]["message"]["text"], "?") + 1
                );
            }
            elseif ($callbackQueryParams[1] == "yes") {
                $phrase = getQuotedPhrase($phpInput["callback_query"]["message"]["text"]);
                $params["text"] = "В какую тему будет уместно добавить фразу " . quotePhrase($phrase) . "?";
                foreach (array_diff(scandir(DICTIONARY_FOLDER), ["..", ".", "from_users"]) as  $fileName) {
                    $dictionaryFile = file_get_contents(DICTIONARY_FOLDER . "/" . $fileName);
                    $dictionaryTitle = trim(substr(
                        $dictionaryFile,
                        0,
                        strpos($dictionaryFile, "\n")
                    ));
                    $dictionaryButtons[] = [
                        "text" => $dictionaryTitle,
                        "callback_data" => "add_to_dict" . CALLBACK_DATA_DELIMITER . $fileName,
                    ];
                }
            }
            else {
                $phrase = getQuotedPhrase($phpInput["callback_query"]["message"]["text"]);
                if (!in_array($callbackQueryParams[1], array_diff(scandir(DICTIONARY_FOLDER), ["..", ".", "from_users"]))) {
                    throw new Exception("Такой словарь не существует!");
                }
                if (!file_put_contents(
                    DICTIONARY_FOLDER . "/from_users/" . $callbackQueryParams[1],
                    $phrase . "\n",
                    FILE_APPEND
                )){
                    throw new Exception("Не удалось сохранить фразу!");
                }
                $params["text"] = "Фраза " . quotePhrase($phrase) . " отправлена на модерацию и будет добавлена в ближайшее время в словарь, при прохождении проверки!";
            }
            if (!empty($dictionaryButtons)) {
                $params["reply_markup"] = createInlineButtons($dictionaryButtons, 2);
            }
        }
        telegramAPIRequest("editMessageText", $params);
    }

    if (@$phpInput["message"]) {
        $params["chat_id"] = $phpInput["message"]["chat"]["id"];
        $request = mb_strtolower($phpInput["message"]["text"]);
        $params["text"] = getAnswerByRules($request);
        if (!$params["text"]) {
            $params["reply_markup"] = createInlineButtons(YES_NO_BUTTONS, 2);
            $params["text"] = "{$phpInput["message"]["from"]["first_name"]}, я не понимаю тебя!\nЧто значит, " . quotePhrase($request) . "?\n\nДобавить слово в словарь?";
        }
        telegramAPIRequest("sendMessage", $params);
    }
}
catch (Throwable $e) {
    if (@$phpInput["callback_query"]) {
        telegramAPIRequest("answerCallbackQuery", [
            "callback_query_id" =>$phpInput["callback_query"]["id"],
            "text" => "что то пошло не так, приносим свои извинения!"
        ]);
    } elseif(@$phpInput["message"]) {
        telegramAPIRequest("sendMessage", [
            "chat_id" => $phpInput["message"]["chat"]["id"],
            "text" => "Что то пошло не так, приносим свои извинения!",
        ]);
    }
    addLog(
        "{$e->getMessage()} | {$e->getFile()}({$e->getLine()}) \n{$e->getTraceAsString()} \n\n",
        "errors"
    );
}