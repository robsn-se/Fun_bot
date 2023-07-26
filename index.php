<?php
//https://api.telegram.org/bot6687997891:AAFrkwogyFCs3yPzMBcC88gIffEbs8gBYxc/getUpdates
//https://api.telegram.org/bot6687997891:AAFrkwogyFCs3yPzMBcC88gIffEbs8gBYxc/sendMessage?chat_id=544421875&text=привет+Рубен


const TELEGRAM_API_URL = "https://api.telegram.org/bot";
const BOT_TOKEN = "6687997891:AAFrkwogyFCs3yPzMBcC88gIffEbs8gBYxc";
//$params = [
//    "chat_id" => 544421875,
//    "text" => "hello, ruben"
//];
$params["chat_id"] = 544421875;
$params["text"] = @$_GET["text"] ?: "check";
$method = "sendMessage";
$request = TELEGRAM_API_URL . BOT_TOKEN . "/" . $method . "?" . http_build_query($params);
$response = file_get_contents($request);
echo "<pre>";
print_r(json_decode($response, JSON_UNESCAPED_UNICODE));