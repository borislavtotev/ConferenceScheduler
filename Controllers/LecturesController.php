<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

include_once('Controller.php');

use SoftUni\FrameworkCore\Http\LoggedUser;
use SoftUni\Models\BindingModels\UserChangeProfileBindingModel;
use SoftUni\Models\IdentityUser;
use SoftUni\Models\ViewModels\UserViewModel;
use SoftUni\FrameworkCore\View;
use SoftUni\Models\BindingModels\UserLoginBindingModel;
use SoftUni\Models\BindingModels\UserBindingModel;
use SoftUni\FrameworkCore\Database;
use SoftUni\Config\UserConfig;

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

//        $viewModels = [];
//
//        foreach ($myLectures as $myLecture) {
//            $viewModel = new LectureViewModel();
//            $viewModel->setLecture($myLecture['Lecture']);
//            $viewModel->setEndtime($myLecture['EndTime']);
//            $viewModel->setStarttime($myLecture['StartTime']);
//            $viewModel->setHall($myLecture['Hall']);
//            $viewModel->setSpeker($myLecture['Speaker']);
//            $viewModels[] = $viewModel;
//        }

        var_dump($myLectures);
//        return View('lecture', $viewModels);
    }
}