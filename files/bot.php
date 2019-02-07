<?php

require_once BOT_BASE_DIRECTORY . '/www/api/vk_api.php';

function bot_sendMessage($user_id, $message, $keyboard) {
    vkApi_messagesSend($user_id, $message, $keyboard);
}

function bot_sendDefaultMessage($user_id) {
    vkApi_messagesSend($user_id, DEFAULT_MESSAGE);
}