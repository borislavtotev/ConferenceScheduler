<?php
declare(strict_types=1);

namespace SoftUni\Config;

/* Add custom routes
 * You can use regex with named groups <controller>, <action>, <params>
 * to create specific routing
 *
 * Example:
 * To match the creation of a new product/user/promotion, you can add this route
 *
 *  '/^new\/(?<controller>.*?)\/(?<action>.*?)\/(?<params>.*)$/i'
 *
 * matched uri will be:
 *    "newProduct/Create/Cake/With3Eggs"
 *    "newUser/CreateProfile/FromFacebook"
 * */
class RouteConfig
{
    const CustomRouteConfigs = array(
        '#^\/users\/(?<controller>[^\/\\\]+?)\/(?<action>[^\/\\\]+?)\/(?<params>[^/\\\]+?)$#i',
        '#^\/users\/(?<controller>[^\/\\\]+?)\/(?<action>[^\/\\\]+?)$#i'
    );

    const DefaultFrameworkRouteConfigs = array(
        '#^\/(?<controller>[^\/\\\]+?)\/(?<action>[^\/\\\]+?)\/(?<params>[^\/\\\]+?)$#i',
        '#^\/(?<controller>[^\/\\\]+?)\/(?<action>[^\/\\\]+?)$#i'
    );
}