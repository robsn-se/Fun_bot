<?php
function env(string $envName, mixed $default = null): mixed {
    return $_ENV[$envName] ?? $default;
}

$_ENV = parse_ini_file(".env");

const TELEGRAM_API_URL = "https://api.telegram.org/bot";

const ADMIN_ID = "544421875";

const LOG_FOLDER = "logs";

const DICTIONARY_FOLDER = "dictionary";
