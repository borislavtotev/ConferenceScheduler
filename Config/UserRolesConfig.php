<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 10/3/2015
 * Time: 4:47 PM
 */

namespace SoftUni\Application\Config;

define('ROLES', serialize(array(
    'User' => 0,
    'ConferenceOwner' => 1,
    'ConferenceAdministrator' => 2,
    'Administrator' => 3
)));