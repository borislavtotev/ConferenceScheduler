<?php
namespace SoftUni\Controllers;

include_once('Controller.php');

use SoftUni\Models\IdentityUser;
use SoftUni\ViewModels\UserViewModel;
use SoftUni\FrameworkCore\View;
use SoftUni\ViewModels\LoginInformation;
use SoftUni\ViewModels\RegisterInformation;
use SoftUni\FrameworkCore\Database;
use SoftUni\Config\UserConfig;

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

                $viewModel->username = $user;
                $viewModel->password = $pass;

                $this->initLogin($user, $pass);
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                return new View($viewModel);
            }
        }

        return new View($viewModel);
    }

    /**
     * @Route("logout")
     * @POST
     */
    public function logout()
    {
        session_unset();
        session_destroy();

        header("Location: /");
    }

    /**
     * @Route("register")
     * @POST
     */
    public function register()
    {
        $viewModel = new RegisterInformation();

        try
        {
            if (isset($_POST['username'], $_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $confirm = $_POST['confirm'];
                $viewModel->username = $_POST['username'];
                $viewModel->password = $_POST['password'];
                $viewModel->confirm = $_POST['confirm'];

                if ($password != $confirm) {
                    throw new \Exception("Password and Confirm password are different");
                }

                $dbUserModel = $this->dbContext->getIdentityUsersRepository()->filterByUsername($username)->findOne();
                if ($dbUserModel == null) {
                    if (strlen($password) >=4 ) {
                        $userClassName = UserConfig::UserIdentityClassName;
                        $userModel = new $userClassName($username, password_hash($password, PASSWORD_DEFAULT));
                    } else {
                        throw new \Exception("The password should be at least 4 characters.");
                    }
                } else {
                    throw new \Exception("User with this username already exist!");
                }

                $this->dbContext->getIdentityUsersRepository()->add($userModel);
                $this->dbContext->getIdentityUsersRepository()->save();

                $this->initLogin($username, $password);
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return new View($viewModel);
        }

        return new View();
    }

    /**
     * @User
     * @Route("profile")
     */
    public function profile()
    {
        if (!$_SESSION['isLogged']) {
            header("Location: /user/login");
        }

        $userViewModel = new UserViewModel("");

        try {
            $userModel = $this->dbContext->getIdentityUsersRepository()->filterById($_SESSION['id'])->findOne();

            $userViewModel->setUsername($userModel->getUsername());

            if (!empty($_POST)) {
                $userViewModel->setUsername($_POST['username']);
                $userViewModel->setPassword($_POST['password']);

                if (!password_verify($_POST['currentPassword'], $userModel->getPassword())) {
                    throw new \Exception('Current password is not valid.');
                }

                if ($_POST['password'] != $_POST['confirm']) {
                    throw new \Exception('New password and Confirm password are not equal.');
                }

                $userModel->setUsername($_POST['username']);
                $userModel->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
                $this->dbContext->getIdentityUsersRepository()->save();
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return new View($userViewModel);
        }

        return new View($userViewModel);
    }

    private function initLogin($username, $password)
    {
        $db = Database::getInstance('app');

        $result = $db->prepare("
            SELECT
                id, username, password
            FROM
                users
            WHERE username = ?
        ");

        $result->execute([$username]);

        if ($result->rowCount() <= 0) {
            throw new \Exception('Invalid username');
        }

        $userRow = $result->fetch();

        if (password_verify($password, $userRow['password'])) {
            $_SESSION['id'] = $userRow['id'];
            $_SESSION['isLogged'] = true;
            header("Location: /user/profile");
        } else {
            throw new \Exception('Invalid credentials');
        }
    }
}

//    private function createModelTable()
//    {
//        $db = Database::getInstance('app');
//
//        $result = $db->prepare("
//                            CREATE TABLE Users (
//                            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//                            username VARCHAR(30) NOT NULL,
//                            password VARCHAR(30) NOT NULL,
//                            reg_date TIMESTAMP
//                            )
//                    ");
//
//        $result->execute(
//            [
//                0,
//                $username,
//                password_hash($password, PASSWORD_DEFAULT),
//                ''
//            ]
//        );
//
//        if ($result->rowCount() > 0) {
//            return true;
//        }
//    }
//}
