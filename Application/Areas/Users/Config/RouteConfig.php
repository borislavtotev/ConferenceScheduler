<?php
/* Add custom routes
 * You can use regex with named groups <controller>, <action>, <params>
 * to create specific routing
 *
 * Example:
 * To match the creation of a new product/user/promotion, you can use this route
 *
 * $customRoutes = array(
    array(
        'regex' => '/^new(?<controller>.*?)\/(?<action>.*?)\/(?<params>.*)$/i',
    )
 *  );
 *
 * matched uri will be:
 *    "newProduct/Create/Cake/With3Eggs"
 *    "newUser/CreateProfile/FromFacebook"
 * */

$routes = array('/^users\/(?<controller>.*?)\/(?<action>.*?)\/(?<params>.*)$/i');