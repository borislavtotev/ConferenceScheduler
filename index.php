<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Autoloader.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Application.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Database.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Annotations' . DIRECTORY_SEPARATOR . 'AnnotationParser.php');

\SoftUni\FrameworkCore\Autoloader::init();
\SoftUni\FrameworkCore\Annotations\AnnotationParser::getAnnotations();

\SoftUni\FrameworkCore\Database::setInstance(
    \SoftUni\Config\DatabaseConfig::DB_INSTANCE,
    \SoftUni\Config\DatabaseConfig::DB_DRIVER,
    \SoftUni\Config\DatabaseConfig::DB_USER,
    \SoftUni\Config\DatabaseConfig::DB_PASS,
    \SoftUni\Config\DatabaseConfig::DB_NAME,
    \SoftUni\Config\DatabaseConfig::DB_HOST
);

$identityUsersRepository = \SoftUni\FrameworkCore\Repositories\IdentityUsersRepository::create();
$dbContext = new \SoftUni\FrameworkCore\DatabaseContext($identityUsersRepository);

$app = new \SoftUni\FrameworkCore\Application($dbContext);
$app->start();