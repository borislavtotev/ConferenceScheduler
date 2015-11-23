<?php
namespace SoftUni\Config;

define('ROLES', serialize(array(
    'User' => 0,
    'ConferenceOwner' => 1,
    'ConferenceAdministrator' => 2,
    'Administrator' => 3
)));