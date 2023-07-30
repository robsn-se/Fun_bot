<?php
function addLog(mixed $data, string $fileName = "log"):void {
    file_put_contents(
        LOG_FOLDER ."/$fileName.log",
        "-- " . date("H:i:s d-m-Y") . "\n" . print_r($data, true) . "\n\n",
        FILE_APPEND
    );
}

function telegramAPIRequest(string $method, ?array $params = null): array {
    $response = file_get_contents(
        TELEGRAM_API_URL . BOT_TOKEN . "/" . $method . "?" . http_build_query($params)
    );
    addLog([
        "method" => $method,
        "params" => $params,
        "response" => $response
    ],
        "requests_to_telegram"
    );
    if ($response) {
        $response = json_decode($response, JSON_UNESCAPED_UNICODE);
    }
    return $response;
}

function setHook(bool $unset = false): void {
    $params["url"] = $unset ?
        $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]
        : "";
    echo "<pre>";
    print_r(telegramAPIRequest("setWebhook", $params));
    exit();
}

//if (@$phpInput["message"]) {
//    $params["chat_id"] = $phpInput["message"]["chat"]["id"];
//    if ($phpInput["message"]["text"] == "привет" || "Hello" || "hi" || "здарова" || "здравствуйте" || "приветствую") {
//        $params["text"] = "Привет, {$phpInput["message"]["from"]["first_name"]}";
//    }
//    else{
//        $params["text"] = "{$phpInput["message"]["from"]["first_name"]}, я не понял тебя";
//    }
//    telegramAPIRequest("sendMessage", $params);
//}