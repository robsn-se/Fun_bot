<?php

function addLog(mixed $data, string $fileName = "log"):void {
    file_put_contents(
        LOG_FOLDER ."/$fileName.log",
        "-- " . date("H:i:s d-m-Y") . "\n" . var_export($data, true) . "\n\n",
        FILE_APPEND
    );
}

function telegramAPIRequest(string $method, ?array $params = null): array {
    /** @var string $BOT_TOKEN */
    $response = file_get_contents(
        TELEGRAM_API_URL . $BOT_TOKEN . "/" . $method . "?" . http_build_query($params)
    );
    addLog([
        "method" => $method,
        "params" => $params,
        "response" => $response
    ],
        "requests_to_telegram"
    );
    if ($response) {
        $response = json_decode($response, true);
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

function getAnswerByRules(string|int $request): string|int|null {
    $answer = null;
    foreach (BOT_RULES as $rule) {
        if (in_array($request, readDictionary($rule[REQUESTS_KEY]))) {
            $answer = getRandomWord($rule[RESPONSES_KEY][WORLDS_KEY]);
            if (isset($rule[RESPONSES_KEY][SIGNS_KEY]))  {
                $answer .= getRandomWord($rule[RESPONSES_KEY][SIGNS_KEY]);
            }
            if (isset($rule[SUB_RESPONSES_KEY])) {
                $answer .= " " . getRandomWord($rule[SUB_RESPONSES_KEY][WORLDS_KEY]);
                if (isset($rule[SUB_RESPONSES_KEY][SIGNS_KEY]))  {
                    $answer .= getRandomWord($rule[SUB_RESPONSES_KEY][SIGNS_KEY]);
                }
            }
        }
    }
    return $answer;
}

function getDictionaryArray(string $fileName): array {
    $file = file_get_contents(DICTIONARY_FOLDER . "/" . $fileName);
    if (!$file || !trim($file)) {
        throw new Exception("Файл {$fileName} не найден или пустой");
    }
    $worldsArray = explode("\n", $file);
    if (empty($worldsArray)) {
        throw new Exception("Файл словаря {$fileName} неправильного формата");
    }
    return array_map("trim", $worldsArray);
}

function getRandomWord(array|string $dictionary): string|int {
    $dictionary = readDictionary($dictionary);
    return $dictionary[rand(0, count($dictionary) - 1)];
}

function readDictionary(string|array $dictionary): array {
    if (is_string($dictionary)) {
        $dictionary = getDictionaryArray($dictionary);
    }
    return $dictionary;
}


function createInlineButtons(array $buttons, int $columnCount = 1): string {
    $rowCounter = 0; // укладка ряда
    $columnCounter = 0; // укладка в колонну
    $result = []; // массив кнопок
    foreach ($buttons as $button) {
        if (!isset($result[$rowCounter])) {
            $result[$rowCounter] = [];
        }
        $result[$rowCounter][] = $button;
        $columnCounter++;
        if($columnCounter % $columnCount) {
            continue;
        }
        $rowCounter++;
    }
    return json_encode(["inline_keyboard" => $result], JSON_UNESCAPED_UNICODE);
}

function quotePhrase(string $phraseText):string {
    return QUOTES . "$phraseText" . QUOTES;
}

function getQuotedPhrase(string $messageText):string {
    preg_match_all("/.+" . QUOTES . "(.+)" . QUOTES . ".?/", $messageText, $matches);
    addLog($matches, "matches");
    return $matches[1][0];
}