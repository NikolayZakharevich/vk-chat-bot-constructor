<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 05.02.19
 * Time: 19:09
 */

class Keyboard
{
    const BLUE = "primary";
    const WHITE = "default";
    const RED = "negative";
    const GREEN = "positive";

    private $value;

    private $current_row = array();

    private $row_index = 0;
    private $col_index = 0;

    static function getColor($color)
    {
        switch ($color) {
            case "blue":
                return self::BLUE;
            case "white":
                return self::WHITE;
            case "red":
                return self::RED;
            case "green":
                return self::GREEN;
            default:
                throw new Exception("Invalid color of button");
        }
    }

    public function __construct()
    {
        $this->value = array("one_time" => false, "buttons" => array());
    }

    function add_button($label, $payload, $color = self::GREEN)
    {
        if (++$this->col_index == 5) {
            throw new Exception("To many columns in keyboard");
        }
        $button = array(
            "action" => array(
                "type" => "text",
                "payload" => $payload,
                "label" => $label),
            "color" => $color);
        $this->current_row[] = $button;

    }

    function new_row()
    {
        if (++$this->row_index == 11) {
            throw new Exception("Too many rows in keyboard");
        }
        $this->value['buttons'][] = $this->current_row;
        $this->current_row = array();
        $this->col_index = 0;

    }

    function get_value()
    {
        if ($this->col_index > 0) {
            $this->new_row();
        }
        return json_encode($this->value, JSON_UNESCAPED_UNICODE);
    }


}