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

    const UserIdentityClassName = "IdentityUser";
}