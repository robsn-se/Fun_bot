<?php
// функция предназначена для создания логов -
//Логи полезны для отладки различных частей приложения,
// а также для сбора и анализа информации о работе системы с целью выявления ошибок.
// функция addLog принимает 2 параметра: 1-ый принимает значения с различными типами данных,
// 2-ой имя файла куда записываются эти значения, по умолчанию log, т.е если не указать имя файла, то запишется в log
function addLog(mixed $data, string $fileName = "log"):void {
    // далее мы используем встроенную функцию php file_put_contents для записывания этих самых данные в файл
    file_put_contents(
    // 1-ый параметр это имя путь к файлу
    LOG_FOLDER ."/$fileName.log",
        // 2-ой параметр это записываемые данные при помощи встроенной функции php var_export()
    // для преобразования значения переменной в строку и сохранения ее в файл
    // var_export (первый параметр это переменная, которая необходима,
    // второй параметр выводит строку с преобразованным значением (если true) )
        "-- " . date("H:i:s d-m-Y") . "\n" . var_export($data, true) . "\n\n",
        // 3-ий параметр FILE_APPEND означает если файл filename уже существует,
    // данные будут дописаны в конец файла вместо того, чтобы его перезаписать.
        FILE_APPEND
    );
}

// функция telegramAPIRequest позволяет нам общаться с сервером телеграмм
// функция принимает 2 параметра: 1-ый это МЕТОД запроса по которому мы будем
// получать или отправлять определённые данные. В зависимости от названия метода, мы будем выполнять разные действия.
// 2-ой параметр это опции или парметр для того чтобы конкретизировать, например нам нужно передать сообщение
// и для этого необходимо передать id чата, которая как раз находится в $params
function telegramAPIRequest(string $method, ?array $params = null): array {
    // встроенная функция php file_get_contents() возвращает содержимое файла в строку,2
    $response = file_get_contents(
      //  http_build_query() генерирует параметры url сторки из массива $params
        TELEGRAM_API_URL . BOT_TOKEN . "/" . $method . "?" . http_build_query($params)
    );
    //функция addLog записывает все данные в файл "requests_to_telegram"
    addLog([
        "method" => $method,
        "params" => $params,
        "response" => $response
    ],
        "requests_to_telegram"
    );
    // если $response существует (напоминаю что $response это строка), тогда
    if ($response) {
        // мы преобразуем строку-$response в массив
        $response = json_decode($response, true);
    }
    // возвращает функция массив
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