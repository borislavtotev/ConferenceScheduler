<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Autoloader.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Application.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Database.php');
include_once('FrameworkCore' . DIRECTORY_SEPARATOR . 'Annotations' . DIRECTORY_SEPARATOR . 'AnnotationParser.php');

\SoftUni\FrameworkCore\Autoloader::init();

\SoftUni\FrameworkCore\Database::setInstance(
    \SoftUni\Config\DatabaseConfig::DB_INSTANCE,
    \SoftUni\Config\DatabaseConfig::DB_DRIVER,
    \SoftUni\Config\DatabaseConfig::DB_USER,
    \SoftUni\Config\DatabaseConfig::DB_PASS,
    \SoftUni\Config\DatabaseConfig::DB_NAME,
    \SoftUni\Config\DatabaseConfig::DB_HOST
);

$identityUsersRepository = \SoftUni\FrameworkCore\Repositories\IdentityUsersRepository::create();
$conferecesRepository = \SoftUni\FrameworkCore\Repositories\ConferencesRepository::create();
$hallsRepository = \SoftUni\FrameworkCore\Repositories\HallRepository::create();
$lecturesRepository = \SoftUni\FrameworkCore\Repositories\LecturesRepository::create();
$venuesRepository = \SoftUni\FrameworkCore\Repositories\VenuesRepository::create();
$dbContext = new \SoftUni\FrameworkCore\DatabaseContext($identityUsersRepository,
                                                        $conferecesRepository,
                                                        $hallsRepository,
                                                        $lecturesRepository,
                                                        $venuesRepository);

$httpContext = new \SoftUni\FrameworkCore\Http\HttpContext();

$test = $httpContext->getLoggedUser()->getId();

$app = new \SoftUni\FrameworkCore\Application($dbContext, $httpContext);
$app->start();

?>


