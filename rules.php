 <?php
const REQUESTS_KEY = 1;
const RESPONSES_KEY = 2;
const SUB_RESPONSES_KEY = 3;

const SIGNS_KEY = 1;
const WORLDS_KEY = 2;

const CALLBACK_DATA_DELIMITER = "<^>";

const QUOTES = "'''";

 const YES_NO_BUTTONS = [
    ["text" => "Yes", "callback_data" => "add_to_dict" . CALLBACK_DATA_DELIMITER . "yes"],
    ["text" => "No", "callback_data" => "add_to_dict" . CALLBACK_DATA_DELIMITER . "no"],
];

const BOT_RULES = [
    [
        REQUESTS_KEY => "welcome",
        RESPONSES_KEY => [
            WORLDS_KEY => "welcome",
            SIGNS_KEY => ["!", "!!", "!!!", "👍", ".", "😌", "👋", "🤝"],
        ],
    ],
    [
        REQUESTS_KEY => "parting",
        RESPONSES_KEY => [
            WORLDS_KEY => "parting",
            SIGNS_KEY => ["!", "!!!", "👍", "👋"],
        ],
    ],
    [
        REQUESTS_KEY => "how_are_you",
        RESPONSES_KEY => [
            WORLDS_KEY => ["отлично, спасибо", "супер", "пойдет", "всё хорошо"],
            SIGNS_KEY => ["!", "!!!", "!!!", "👍"],
        ],
        SUB_RESPONSES_KEY => [
            WORLDS_KEY => ["как ты", "ты как", "а ты", "сам как", "у тебя как дела", "как самочувствие"],
            SIGNS_KEY => ["?", "!?", "😉", "😊", "😇"],
        ],
    ],
    [
        REQUESTS_KEY => ["что делаешь", "че делаешь", "чем занимаешься", "чем занят", "че умеешь", "что умеешь"],
        RESPONSES_KEY => [
            WORLDS_KEY => ["учу программирование", "развиваюсь в сфере IT", "общаюсь с тобой😄"],
            SIGNS_KEY => ["!", "!!!", "!!!", "👍", "😉", "😊", "😇" ],
        ],
        SUB_RESPONSES_KEY => [
            WORLDS_KEY => ["а ты", "а ты что делаешь", "а ты чем занят", "сам что делаешь"],
            SIGNS_KEY => ["?", "!?", "😉", "😊", "😇"],
        ],
    ],
    [
        REQUESTS_KEY => ["откуда ты", "где живешь", "родом откуда", "где обитаешь"],
        RESPONSES_KEY => [
            WORLDS_KEY => ["я родился в телеграмме😄", "какая разница"],
            SIGNS_KEY => ["!", "!!!", "!!!", "👍", "😉", "😊", "😇" ],
        ],
        SUB_RESPONSES_KEY => [
            WORLDS_KEY => ["а ты", "а ты что откуда", "а ты сам откуда", "сам где живешь", "а ты где живешь"],
            SIGNS_KEY => ["?", "!?", "😉", "😊", "😇"],
        ],
    ],
    [
        REQUESTS_KEY => ["сколько тебе лет", "сколько тебе", "когда создали тебя", "давно здесь"],
        RESPONSES_KEY => [
            WORLDS_KEY => ["меня создали недавно😉",],
            SIGNS_KEY => ["!", "!!!", "!!!", "👍", "😉", "😊", "😇" ],
        ],
        SUB_RESPONSES_KEY => [
            WORLDS_KEY => ["а тебе сколько лет", "а тебе сколько", "а у тебя какой возраст"],
            SIGNS_KEY => ["?", "!?", "😉", "😊", "😇"],
        ],
    ],
    [
        REQUESTS_KEY => "offensive_words",
        RESPONSES_KEY => [
            WORLDS_KEY => ["эй, не матерись",],
            SIGNS_KEY => ["!", "!!", "!!!", "😢,", "😕", "😤", "🫣", "🫠", "🤨"],
        ],
    ],
];
