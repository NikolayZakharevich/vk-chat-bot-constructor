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

    private $ways = array();

    private $used = false;

    function __construct($id, $label, $text, $ways = array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->text = $text;

        if ($ways) {
            foreach ($ways as $row) {
                $this->ways[] = $row;
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

    function getWays() {
        return $this->ways;
    }

    function hasWays() {
        return !empty($this->ways);
    }

    function isUsed() {
        return $this->used == true;
    }

    function markUsed($used = true) {
        $this->used = $used;
    }

}