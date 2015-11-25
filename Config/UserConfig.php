<?php
namespace SoftUni\Config;

class UserConfig
{
    const roles = array(
        'User' => 1,
        'ConferenceOwner' => 2,
        'ConferenceAdministrator' => 3,
        'Administrator' => 4
    );

    // if you want to extend the class, you can choose here another class to store the information for the users
    const UserIdentityClassName = 'SoftUni\Models\User';
}