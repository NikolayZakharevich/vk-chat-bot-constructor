<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 07.02.19
 * Time: 21:15
 */

require_once BOT_BASE_DIRECTORY . '/vendor/predis/predis/autoload.php';

class Storage
{

    const CURRENT_STATE_KEY = 'bot_scenario_state_current'; // concat with $user_id
    const STATE_KEY = 'bot_scenario_state';
    const START_STATE_KEY = 'bot_scenario_state_start';

    private $redis;

    public function __construct()
    {
        Predis\Autoloader::register();
        $this->redis = new Predis\Client(array(
            'host' => REDIS_HOSTNAME,
            'port' => REDIS_PORT
        ));
    }

    function saveState($state)
    {
        $this->redis->set(self::STATE_KEY . $state["id"], json_encode($state, JSON_UNESCAPED_UNICODE));
    }

    function getState($state_id)
    {
        return json_decode($this->redis->get(self::STATE_KEY . $state_id), true);
    }

    function setStartState($start_id)
    {
        $this->redis->set(self::START_STATE_KEY, $start_id);
    }

    function getStartStateId()
    {
        return $this->redis->get(self::START_STATE_KEY);
    }

    function getStartState()
    {
        return json_decode($this->redis->get(self::START_STATE_KEY), true);
    }

    function getCurrentStateId($user_id)
    {
        return $this->redis->get(self::CURRENT_STATE_KEY . $user_id);
    }

    function setCurrentState($user_id, $state_id)
    {
        $this->redis->set(self::CURRENT_STATE_KEY . $user_id, $state_id);
    }

    function getCurrentState($user_id)
    {
        return $this->getState($this->getCurrentStateId($user_id));
    }
}