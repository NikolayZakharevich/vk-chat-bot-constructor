<?php

define('VK_API_VERSION', '5.92');
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');

function vkApi_messagesSend($peer_id, $message, $keyboard = null)
{
    $parameters_array = array(
        'peer_id' => $peer_id,
        'message' => $message,
        'random_id' => rand(),
    );
    if ($keyboard) {
        $parameters_array['keyboard'] = $keyboard;
    }

    return _vkApi_call('messages.send', $parameters_array);
}

function vkApi_usersGet($user_id)
{
    return _vkApi_call('users.get', array(
        'user_ids' => $user_id,
    ));
}

function _vkApi_call($method, $params = array())
{
    $params['access_token'] = VK_API_ACCESS_TOKEN;
    $params['v'] = VK_API_VERSION;

    $query = http_build_query($params);
    $url = VK_API_ENDPOINT . $method . '?' . $query;

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($curl);
    $error = curl_error($curl);
    if ($error) {
        log_error($error);
        throw new Exception("Failed {$method} request");
    }


    curl_close($curl);

    $response = json_decode($json, true);
    if (!$response || !isset($response['response'])) {
        log_error($json);
        throw new Exception("Invalid response for {$method} request");
    }

    return $response['response'];
}