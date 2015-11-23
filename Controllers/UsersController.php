<?php
namespace SoftUni\Controllers;

include_once('Controllers' . DIRECTORY_SEPARATOR . 'Controller.php');

use SoftUni\Models\User;
use SoftUni\ViewModels\UserViewModel;
use SoftUni\FrameworkCore\View;
use SoftUni\ViewModels\LoginInformation;
use SoftUni\ViewModels\RegisterInformation;

/**
 * @Route("user")
 * @User
 * @Admin
 * @Route("user/login/{id:integer}")
 */
class UsersController extends Controller
{
    /**
     * @Editor
     * @Route("login/{id:integer}/{name:string}")
     * @DELETE
     */
    public function login()
    {
        $viewModel = new LoginInformation();

        if (isset($_POST['username'], $_POST['password'])) {
            try {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                $this->initLogin($user, $pass);
            } catch (\Exception $e) {
                $viewModel->error = $e->getMessage();
                return new View($viewModel);
            }
        }

        return new View($viewModel);
    }

    /**
     * @Route("register")
     * @POST
     * @afafasfa
     */
    public function register()
    {
        $viewModel = new RegisterInformation();

        if (isset($_POST['username'], $_POST['password'])) {
            try {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                $userModel = new User();
                $userModel->register($user, $pass);

                $this->initLogin($user, $pass);
            } catch (\Exception $e) {
                $viewModel->error = $e->getMessage();
                return new View($viewModel);
            }
        }

        return new View();
    }

    /**
     * @User
     * @Route("profile")
     * @GET
     */
    public function profile()
    {
        if (!$this->isLogged()) {
            header("Location: login");
        }

        $userModel = new User();
        $userInfo = $userModel->getInfo($_SESSION['id']);


        $userViewModel = new UserViewModel(
            $userInfo['username'],
            $userInfo['password'],
            $userInfo['id'],
            $userInfo['gold'],
            $userInfo['food']
        );

        if (isset($_POST['edit'])) {
            if ($_POST['password'] != $_POST['confirm'] || empty($_POST['password'])) {
                $userViewModel->error = 1;
                return new View($userViewModel);
            }

            if ($userModel->edit(
                $_POST['username'],
                $_POST['password'],
                $_SESSION['id']
            )) {
                $userViewModel->success = 1;
                $userViewModel->setUsername($_POST['username']);
                $userViewModel->setPass($_POST['password']);

                return new View($userViewModel);
            }

            $userViewModel->error = 1;
            return new View($userViewModel);
        }

        return new View($userViewModel);
    }

    private function initLogin($user, $pass)
    {
        $userModel = new User();

        $userId = $userModel->login($user, $pass);
        $_SESSION['id'] = $userId;
        header("Location: profile");
    }
}