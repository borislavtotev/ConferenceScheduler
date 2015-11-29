<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

use SoftUni\FrameworkCore\Database;

include_once('Controller.php');


class LecturesController extends Controller
{
    /**
     * @Authorize
     * @Route("my-schedule")
     */

    public function mySchedule() {
        //echo "vlezna";
        $userId = $this->httpContext->getLoggedUser()->getId();
        $myLectures = Database::getUserLectures($userId);
        var_dump($myLectures);
        //die;
    }
}