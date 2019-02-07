<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 06.02.19
 * Time: 0:07
 */


function console_info($message) {
    if (is_array($message)) {
        $message = json_encode($message, JSON_UNESCAPED_UNICODE);
    }

    _console_write('[INFO] ' . $message);
}

function console_warning($message) {
    if (is_array($message)) {
        $message = json_encode($message, JSON_UNESCAPED_UNICODE);
    }

    _console_write('[WARNING] ' . $message);
}

function _console_write($message) {
    echo $message . "\n";
}
