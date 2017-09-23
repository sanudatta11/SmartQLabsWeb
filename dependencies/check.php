<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 11:30 AM
 */
session_start();
function check($var)
{
    return (isset($var) && !empty($var));
}