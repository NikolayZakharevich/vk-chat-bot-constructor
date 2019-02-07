<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 05.02.19
 * Time: 23:22
 */

class ScenarioState
{
    private $id;

    private $text;

    private $label;

    private $color;

    private $ways = array();

    private $used = false;

    function __construct($id, $label, $text, $options = array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->text = $text;

        if (!empty($options)) {
            if (array_key_exists("color", $options)) {
                $this->color = $options["color"];
            }
            if (!empty($options["ways"])) {
                foreach ($options["ways"] as $row) {
                    $this->ways[] = $row;
                }
            }
        }
    }

    function getLabel() {
        return $this->label;
    }

    function getText() {
        return $this->text;
    }

    function getId() {
        return $this->id;
    }

    function getColor() {
        return $this->color;
    }

    function getWays() {
        return $this->ways;
    }

    function hasWays() {
        return !empty($this->ways);
    }

    function hasColor() {
        return isset($this->color);
    }

    function isUsed() {
        return $this->used == true;
    }

    function markUsed($used = true) {
        $this->used = $used;
    }

}