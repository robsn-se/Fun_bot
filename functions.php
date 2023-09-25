<?php
// функция addLog работает в качестве журнала событий
// первый параметр любой тип данных
// второй параметр имя файла (по умолчанию добавляется в файл "log")
function addLog(mixed $data, string $fileName = "log"):void {
    file_put_contents(
//      file_put_contents записывает данные в файл
        LOG_FOLDER ."/$fileName.log",
//       filename Путь к файлу, куда записать данные.
        "-- " . date("H:i:s d-m-Y") . "\n" . var_export($data, true) . "\n\n",
//       data Данные для записи. Может быть строкой, массивом или потоковым ресурсом.?????????????
        FILE_APPEND
//       FILE_APPEND добавляет данные в файл, а не перезаписывает.
    );
}

 // функция от telegram API позволяет общаться с сервером телеграм
function telegramAPIRequest(string $method, ?array $params = null): array {
//     переменная $response отправляет наш запрос и получает ответ
    $response = file_get_contents(
//        http_build_query() генерирует параметры url сторки из массива $params
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

// получить ответ по правилам функция возвращает ответ
function getAnswerByRules(string|int $request): string|int|null {
    $answer = null;
//    пробегаемся циклом по массиву с ответами
    foreach (BOT_RULES as $rule) {
//        если, существует ли значение в массиве,
//                  то есть,
//        ЕСЛИ СУЩЕСТВУЕТ ЗНАЧЕНИЕ $request В МАССИВЕ, тогда создаем переменную $answer и ответ
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

// эта функция нужна, если мы получаем строку и должны преобразовать в массив
// функция getDictionaryArray в качестве параметра принимает строку "$fileName",
// возвращает массив !!!если у нас файл есть
function getDictionaryArray(string $fileName): array {
    $file = file_get_contents(DICTIONARY_FOLDER . "/" . $fileName);
//  переменной $file присваивается результат функции file_get_contents() она возвращает файл в виде строки
    if (!$file || !trim($file)) {
        throw new Exception("Файл {$fileName} не найден или пустой");
    }
//    создаем переменную $worldsArray присваиваем ей массив строк исходя из наших МАТОВ (в нашем случае)
    $worldsArray = explode("\n", $file);
    if (empty($worldsArray)) {
        throw new Exception("Файл словаря {$fileName} неправильного формата");
    }
//    array_map ТРИМИТ КАЖДЫЙ ЭЛЕМЕНТ МАССИВА $worldsArray
    return array_map("trim", $worldsArray);
}

// функция getRandomWord
function getRandomWord(array|string $dictionary): string|int {
    $dictionary = readDictionary($dictionary);
//    1322 мы отнимаем -1, потому что начинаем отсчет с 0
    $word = $dictionary[rand(0, count($dictionary) - 1)];
    if (is_string($word)) {
//        $word[0] = mb_strtoupper($word[0]);
        return $word;
    }
    return $word;
}

// функция readDictionary в качестве параметра принимает массив|строку, возвращает массив
// если, $dictionary является строкой, тогда переменной $dictionary присваиваем результат функции
// ЭТУ ФУНКЦИЮ ИСПОЛЬЗУЕМ ДЛЯ ЧТЕНИЯ ПЕРЕМЕННОЙ $dictionary, А ЭТО В СВОЮ ОЧЕРЕДЬ
// НАШИ ПРАВИЛА МАССИВ И ФАЙЛ *offensive_words*
function readDictionary(string|array $dictionary): array {
    if (is_string($dictionary)) {
//        если это не массив
        $dictionary = getDictionaryArray($dictionary);
    }
    return $dictionary;
}