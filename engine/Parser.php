<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 07.02.19
 * Time: 18:42
 */

require_once "ScenarioState.php";

class Parser
{
    const INVALID_FORMAT = 'Invalid format of JSON scenario. ';
    const DEFAULT_REDIS_HOSTNAME = '127.0.0.1';
    const DEFAULT_REDIS_PORT = 6379;
    const DEFAULT_GREETING_MESSAGE = 'Привет!';
    const DEFAULT_DEFAULT_MESSAGE = 'Выберите пункт';

    private $states;

    private $scenario_data;

    public function __construct($path_to_file = __DIR__ . '/scenario.json')
    {
        if (!file_exists($path_to_file)) {
            exit("File $path_to_file is not found");
        }

        $json = file_get_contents($path_to_file);
        $this->scenario_data = json_decode($json, true);

        if (!is_array($this->scenario_data)) {
            exit(self::INVALID_FORMAT);
        }
        if (!array_key_exists("bot_directory", $this->scenario_data)) {
            exit(self::INVALID_FORMAT . "Missing required parameter: bot_directory");
        }
        if (!array_key_exists("access_token", $this->scenario_data)) {
            exit(self::INVALID_FORMAT . "Missing required parameter: access_token");
        }
        if (!array_key_exists("confirmation_token", $this->scenario_data)) {
            exit(self::INVALID_FORMAT . "Missing required parameter: confirmation_token");
        }
        if (!array_key_exists("start_buttons", $this->scenario_data)) {
            exit(self::INVALID_FORMAT . "Missing required parameter: start_buttons");
        }
        if (!array_key_exists("buttons", $this->scenario_data)) {
            exit(self::INVALID_FORMAT . "Missing required parameter: buttons");
        }


        foreach ($this->scenario_data["buttons"] as $buttons) {
            foreach ($buttons as $button => $value) {
                if (!array_key_exists("text", $value) ||
                    !array_key_exists("label", $value)) {
                    exit(self::INVALID_FORMAT . "Check parameters of button $button");
                }

                $options = array();
                if (array_key_exists("ways", $value)) {
                    $options["ways"] = $value["ways"];
                }
                if (array_key_exists("color", $value)) {
                    $options["color"] = $value["color"];
                }
                $this->states[$button] = new ScenarioState($button, $value["label"], $value["text"], $options);
            }
        }

        $this->_checkButtonCorrectness();
    }

    function _checkButtonCorrectness()
    {
        foreach ($this->scenario_data["start_buttons"] as $row) {
            foreach ($row as $start_button) {
                if (!array_key_exists($start_button, $this->states)) {
                    exit(self::INVALID_FORMAT . "Start button \"$start_button\" is undefined");
                } else if (!$this->states[$start_button]->isUsed()) {
                    $this->_buttonDfs($start_button);
                }
            }
        }

        foreach ($this->states as $node_id => $node) {
            if (!$node->isUsed()) {
                $button_id = $node->getId();
                console_warning("There is unreachable button $button_id in scenario.");
            }
        }
    }

    function _buttonDfs($node_id)
    {
        $this->states[$node_id]->markUsed();
        foreach ($this->states[$node_id]->getWays() as $row) {
            foreach ($row as $next_node_id) {
                if (!array_key_exists($next_node_id, $this->states)) {
                    exit("Invalid format of JSON scenario. Button \"$next_node_id\" is undefined");
                }

                if (!$this->states[$next_node_id]->isUsed()) {
                    $this->_buttonDfs($next_node_id);
                }
            }
        }
    }

    function getBotDirectory()
    {
        return $this->scenario_data["bot_directory"];
    }

    function getConfirmationToken()
    {
        return $this->scenario_data["confirmation_token"];
    }

    function getAccessToken()
    {
        return $this->scenario_data["access_token"];
    }

    function getStartButtons()
    {
        return $this->scenario_data["start_buttons"];
    }

    function hasClientSecret()
    {
        return array_key_exists("client_secret", $this->scenario_data);
    }

    function getClientSecret()
    {
        return $this->scenario_data["client_secret"];
    }

    function getRedisHostname()
    {
        if (array_key_exists("redis_hostname", $this->scenario_data)) {
            return $this->scenario_data["redis_hostname"];
        }
        return self::DEFAULT_REDIS_HOSTNAME;
    }

    function getRedisPort()
    {
        if (array_key_exists("redis_port", $this->scenario_data)) {
            return $this->scenario_data["redis_port"];
        }
        return self::DEFAULT_REDIS_PORT;
    }

    function getGreetingMessage()
    {
        if (array_key_exists("greeting_message", $this->scenario_data)) {
            return $this->scenario_data["greeting_message"];
        }
        return self::DEFAULT_GREETING_MESSAGE;
    }

    function getDefaultMessage()
    {
        if (array_key_exists("default_message", $this->scenario_data)) {
            return $this->scenario_data["default_message"];
        }
        return self::DEFAULT_DEFAULT_MESSAGE;
    }

    function hasReturnButton()
    {
        return array_key_exists("return_button", $this->scenario_data);
    }

    function getReturnButton()
    {
        return $this->scenario_data["return_button"];
    }

    function get_states()
    {
        $result = array(
            "start" => array(),
            "states" => array()
        );
        foreach ($this->states as $state) {
            $result["states"][$state->getId()] = array(
                "id" => $state->getId(),
                "label" => $state->getLabel(),
                "text" => $state->getText(),
            );
            if ($state->hasWays()) {
                $result["states"][$state->getId()]["ways"] = $state->getWays();
            }
            if ($state->hasColor()) {
                $result["states"][$state->getId()]["color"] = $state->getColor();
            }
        }

        $initial_state_id = "initial_state" . rand();
        $initial_state = array(
            "id" => $initial_state_id,
            "label" => $this->getDefaultMessage(),
            "ways" => $this->getStartButtons(),
            "text" => $this->getDefaultMessage()
        );

        $result["states"][$initial_state_id] = $initial_state;
        $result["start"] = $initial_state_id;

        return $result;
    }
}