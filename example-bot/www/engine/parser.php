<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 05.02.19
 * Time: 22:37
 */

require_once BOT_BASE_DIRECTORY . "/www/logging.php";

function parser_parseScenarioJson($path_to_file)
{
    $nodes = array();

    if (!file_exists($path_to_file)) {
        log_error("There is no config file with name $path_to_file");
        throw new Exception("Scenario uploading failed");
    }

    $json = file_get_contents($path_to_file);
    $scenario_data = json_decode($json, true);

    if (!is_array($scenario_data)) {
        log_error("Invalid format of configuration file");
        throw new Exception("Scenario uploading failed");
    }

    foreach ($scenario_data["buttons"] as $buttons) {
        foreach ($buttons as $button => $value) {
            $nodes[$button] = array(
                "id" => $button,
                "label" => $value["label"],
                "text" => $value["text"]
            );
            if (array_key_exists("ways", $value)) {
                $nodes[$button]["ways"] = $value["ways"];
            }
            if (array_key_exists("color", $value)) {
                $nodes[$button]["color"] = $value["color"];
            }
        }
    }

    $initial_state_id = "inital_state" . rand();

    $initial_state = array(
        "id" => $initial_state_id
    );

    if (array_key_exists("start_buttons", $scenario_data)) {
        $initial_state["ways"] = $scenario_data["start_buttons"];
    }
    if (array_key_exists("default_message", $scenario_data)) {
        $initial_state["text"] = $scenario_data["default_message"];
    }

    $nodes[$initial_state_id] = $initial_state;

    return array(
        "start" => $initial_state_id,
        "states" => $nodes
    );
}