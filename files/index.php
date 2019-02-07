<?php

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');

require_once 'config.php';
require_once 'logging.php';

require_once 'engine/bot.php';
require_once 'engine/scenario.php';

if (!isset($_REQUEST)) {
    exit;
}

callback_handleEvent();

function callback_handleEvent()
{
    $event = _callback_getEvent();

    try {

        switch ($event['type']) {

            case CALLBACK_API_EVENT_CONFIRMATION:
                if (defined('CLIENT_SECRET') && strcmp($event['secret'], CLIENT_SECRET) != 0) {
                    _callback_response('Wrong client secret');
                }
                _callback_handleConfirmation();
                break;

            case CALLBACK_API_EVENT_MESSAGE_NEW:
                if (defined('CLIENT_SECRET') && strcmp($event['secret'], CLIENT_SECRET) != 0) {
                    _callback_response('Wrong client secret');
                }
                _callback_handleMessageNew($event['object']);
                break;

            default:
                _callback_response('Unsupported event');
                break;
        }
    } catch (Exception $e) {
        log_error($e);
    }

    _callback_okResponse();
}

function _callback_getEvent()
{
    return json_decode(file_get_contents('php://input'), true);
}

function _callback_handleConfirmation()
{
    _callback_response(CALLBACK_API_CONFIRMATION_TOKEN);
}

function _callback_handleMessageNew($data)
{
    $user_id = $data['from_id'];
    $message = $data['text'];
    $payload = json_decode($data['payload'], true);

    if (!scenario_isInit($user_id)) {
        scenario_init($user_id);
    } else {
        scenario_processMessage($user_id, $message, $payload);
    }

    _callback_okResponse();
}

function _callback_okResponse()
{
    _callback_response('ok');
}

function _callback_response($data)
{
    echo $data;
    exit();
}
