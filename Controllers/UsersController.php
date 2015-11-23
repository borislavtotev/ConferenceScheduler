<?php
namespace SoftUni\Controllers;

include_once('Controller.php');

use SoftUni\Models\User;
use SoftUni\ViewModels\UserViewModel;
use SoftUni\FrameworkCore\View;
use SoftUni\ViewModels\LoginInformation;
use SoftUni\ViewModels\RegisterInformation;
use SoftUni\FrameworkCore\Database;

/**
 * @Route("user")
 * @Authorize(Roles="Administrator")
 */
class UsersController extends Controller
{
    /**
     * @Route("login")
     * @POST
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
     */
    public function register()
    {
        $viewModel = new RegisterInformation();

        if (isset($_POST['username'], $_POST['password'])) {
            try {
                $username = $_POST['username'];
                $password = $_POST['password'];

                $userModel = new User($username, $password);
                $db = Database::getInstance('app');

//                if ($this->exists($username)) {
//                    throw new \Exception("User already registered");
//                }

                self::createModelTable();
                $result = $db->prepare("
                        INSERT INTO users (username, password)
                        VALUES (?, ?);
                    ");

                $result->execute(
                    [
                        $username,
                        password_hash($password, PASSWORD_DEFAULT),
                    ]
                );

                if ($result->rowCount() > 0) {
                    return true;
                }

                throw new \Exception('Cannot register user');;

                $this->initLogin($username, $password);
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
        $userModel = new User($user, $pass);

        $userId = $userModel->login($user, $pass);
        $_SESSION['id'] = $userId;
        header("Location: profile");
    }

    private function createModelTable()
    {
        $db = Database::getInstance('app');

        $result = $db->prepare("
                            CREATE TABLE Users (
                            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            username VARCHAR(30) NOT NULL,
                            password VARCHAR(30) NOT NULL,
                            reg_date TIMESTAMP
                            )
                    ");

        $result->execute(
            [
                0,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                ''
            ]
        );

        if ($result->rowCount() > 0) {
            return true;
        }
    }
}

//const GOLD_DEFAULT = 1500;
//const FOOD_DEFAULT = 1500;
//
//public function register($username, $password)
//{

//}

//public function exists($username)
//{
//    $db = Database::getInstance('app');
//
//    $result = $db->prepare("SELECT id FROM users WHERE username = ?");
//    $result->execute([ $username ]);
//
//    return $result->rowCount() > 0;
//}
//
//public function login($username, $password)
//{
//    $db = Database::getInstance('app');
//
//    $result = $db->prepare("
//            SELECT
//                id, username, password, gold, food
//            FROM
//                users
//            WHERE username = ?
//        ");
//
//    $result->execute([$username]);
//
//    if ($result->rowCount() <= 0) {
//        throw new \Exception('Invalid username');
//    }
//
//    $userRow = $result->fetch();
//
//    if (password_verify($password, $userRow['password'])) {
//        return $userRow['id'];
//    }
//
//    throw new \Exception('Invalid credentials');
//}
//
//public function getInfo($id)
//{
//    $db = Database::getInstance('app');
//
//    $result = $db->prepare("
//            SELECT
//                id, username, password, gold, food
//            FROM
//                users
//            WHERE id = ?
//        ");
//
//    $result->execute([$id]);
//
//    return $result->fetch();
//}
//
//public function edit($user, $pass, $id)
//{
//    $db = Database::getInstance('app');
//
//    $result = $db->prepare("UPDATE users SET password = ?, username = ? WHERE id = ?");
//    $result->execute([
//        $pass,
//        $user,
//        $id
//    ]);
//
//    return $result->rowCount() > 0;
//}