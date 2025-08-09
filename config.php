<?php
//https://api.telegram.org/bot6687997891:AAFrkwogyFCs3yPzMBcC88gIffEbs8gBYxc/getUpdates
//https://api.telegram.org/bot6687997891:AAFrkwogyFCs3yPzMBcC88gIffEbs8gBYxc/sendMessage?chat_id=544421875&text=привет+Рубен
$_ENV = parse_ini_file(".env");

const TELEGRAM_API_URL = "https://api.telegram.org/bot";
$BOT_TOKEN = $_ENV["BOT_TOKEN"];
const ADMIN_ID = "544421875";

const LOG_FOLDER = "logs";

const DICTIONARY_FOLDER = "dictionary";
