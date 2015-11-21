<?php
echo "alalaa";
session_start();

include_once('Framework' . DIRECTORY_SEPARATOR . 'Autoloader.php');
include_once('Framework' . DIRECTORY_SEPARATOR . 'Application.php');
include_once('Framework' . DIRECTORY_SEPARATOR . 'FrameworkCore' . DIRECTORY_SEPARATOR . 'Annotations.php');

\SoftUni\Autoloader::init();
\SoftUni\Core\Annotations::getAnnotations();

\SoftUni\Core\Database::setInstance(
    \SoftUni\Config\DatabaseConfig::DB_INSTANCE,
    \SoftUni\Config\DatabaseConfig::DB_DRIVER,
    \SoftUni\Config\DatabaseConfig::DB_USER,
    \SoftUni\Config\DatabaseConfig::DB_PASS,
    \SoftUni\Config\DatabaseConfig::DB_NAME,
    \SoftUni\Config\DatabaseConfig::DB_HOST
);

$app = new \SoftUni\Application();
$app->start();
?>


