<?php
namespace SoftUni\Config;

class UserConfig
{
    const Roles = array(
        'User' => 0,
        'ConferenceOwner' => 1,
        'ConferenceAdministrator' => 2,
        'Administrator' => 3
    );

    // if you want to extend the class, you can choose here another class to store the information for the users
    const UserIdentityClassName = 'SoftUni\Models\User';
}