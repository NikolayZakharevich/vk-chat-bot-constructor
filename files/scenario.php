<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 06.02.19
 * Time: 14:25
 */
require_once "parser.php";
require_once "Storage.php";
require_once "Keyboard.php";

$storage = new Storage();

function scenario_isInit($user_id)
{
    global $storage;
    _scenario_checkStorage();

    return $storage->getCurrentStateId($user_id) != null && $storage->getStartStateId() != null;
}

function scenario_load($all_states = array())
{
    global $storage;
    _scenario_checkStorage();

    if (empty($all_states)) {
        $all_states = parser_parseScenarioJson(BOT_BASE_DIRECTORY . "/www/scenario.json");
    }
    if (!array_key_exists("start", $all_states) ||
        !array_key_exists("states", $all_states)) {
        log_error("Scenario uploading failed");
        return;
    }

    foreach ($all_states["states"] as $index => $state) {
        $storage->saveState($state);
    }
    $start_state_id = $all_states["start"];
    $storage->setStartState($start_state_id);
}

function scenario_init($user_id)
{
    global $storage;
    _scenario_checkStorage();

    $start_state_id = $storage->getStartStateId();
    if ($start_state_id == null) {
        log_info("Scenario is not found, uploading to redis from scenario.json");
        scenario_load();
    }

    log_info("Scenario initialization in chat with $user_id");

    $storage->setCurrentState($user_id, $start_state_id);
    _scenario_sendMessage($user_id, GREETING_MESSAGE);
}

function scenario_processMessage($user_id, $message, $payload)
{
    global $storage;
    _scenario_checkStorage();

    log_info("Processing message \"$message\" from $user_id");

    if (!is_array($payload)) {
        $storage->setCurrentState($user_id, $storage->getStartStateId());
        _scenario_sendMessage($user_id, GREETING_MESSAGE);
    }

    if (!$payload["next_state"]) {
        return;
    }

    if ($payload["next_state"] == $storage->getStartStateId()) {
        $storage->setCurrentState($user_id, $payload["next_state"]);
        _scenario_sendMessage($user_id, GREETING_MESSAGE);
        return;
    }

    $current_state = $storage->getCurrentState($user_id);
    if (!array_key_exists("ways", $current_state)) {
        return;
    }

    $correct_next_state = false;
    foreach ($current_state["ways"] as $row) {
        foreach ($row as $way) {
            $next_state = $storage->getState($way);
            if ($next_state["label"] == $message && $way == $payload["next_state"]) {
                $correct_next_state = true;
                break;
            }
        }
        if ($correct_next_state) {
            break;
        }
    }

    if (!$correct_next_state) {
        return;
    }
    $storage->setCurrentState($user_id, $next_state["id"]);
    _scenario_sendMessage($user_id, $next_state["text"]);

}

function _scenario_sendMessage($user_id, $message)
{
    global $storage;
    _scenario_checkStorage();

    $current_state = $storage->getCurrentState($user_id);

    $keyboard = new Keyboard();
    if (array_key_exists("ways", $current_state)) {
        foreach ($current_state["ways"] as $row) {
            foreach ($row as $way) {
                $next_state = $storage->getState($way);
                $next_payload = json_encode(array(
                    "next_state" => $next_state["id"]
                ), JSON_UNESCAPED_UNICODE);
                $keyboard->add_button($next_state["label"], $next_payload, Keyboard::GREEN);
            }
            $keyboard->new_row();
        }
    }

    if ($storage->getCurrentStateId($user_id) != $storage->getStartStateId()) {
        $return_button_payload = json_encode(array(
            "next_state" => $storage->getStartStateId()
        ), JSON_UNESCAPED_UNICODE);
        $keyboard->add_button(TO_MENU_MESSAGE, $return_button_payload, Keyboard::RED);
    }

    bot_sendMessage($user_id, $message, $keyboard->get_value());
}

function _scenario_checkStorage() {
    global $storage;
    if (!$storage) {
        $storage = new Storage();
    }
}