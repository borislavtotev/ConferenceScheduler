<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

include_once('Controller.php');

use SoftUni\FrameworkCore\Http\LoggedUser;
use SoftUni\Models\IdentityUser;
use SoftUni\Models\ViewModels\UserViewModel;
use SoftUni\FrameworkCore\View;
use SoftUni\Models\BindingModels\UserLoginBindingModel;
use SoftUni\Models\BindingModels\UserBindingModel;
use SoftUni\FrameworkCore\Database;
use SoftUni\Config\UserConfig;

/**
 * @Route("user")
 */
class UsersController extends Controller
{
    /**
     * @Route("login")
     */
    public function login(UserLoginBindingModel $model)
    {
        try {
            $user = $model->getUsername();
            $pass = $model->getPassword();

            $this->initLogin($user, $pass);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->httpContext->getSession()->error = $message;

            return new View($model);
        }

        return new View($model);
    }

    /**
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
     * @GET
     */
    public function getRegister() {
        $model = new UserBindingModel();
        return new View('register', $model);
    }

    /**
     * @Route("register")
     * @POST
     */
    public function register(UserBindingModel $model)
    {
        try {
            $errorMsgs = '';

            if ($model->getUsername() == null) {
                $errorMsgs = "Missing username. ";
            };

            $username = $model->getUsername();

            if ($model->getPassword() == null) {
                $errorMsgs .= "Missing password. ";
            };

            $password = $model->getPassword();

            if ($model->getConfirm() == null) {
                $errorMsgs .= "Missing confirm password. ";
            };

            $confirm = $model->getConfirm();

            if ($password !== $confirm) {
                $errorMsgs .= "Password and Confirm password are different. ";
            }

            $dbUserModel = $this->dbContext->getIdentityUsersRepository()->filterByUsername($username)->findOne();
            if ($dbUserModel == null) {
                if (strlen($password) >=4 ) {
                    $userClassName = UserConfig::UserIdentityClassName;
                    $userModel = new $userClassName($username, password_hash($password, PASSWORD_DEFAULT));
                } else {
                    $errorMsgs .= "The password should be at least 4 characters. ";
                }
            } else {
                $errorMsgs .= "User with this username already exist! ";
            }

            if ($errorMsgs != '') {
                throw new \Exception($errorMsgs);
            }

            $this->dbContext->getIdentityUsersRepository()->add($userModel);
            $this->dbContext->getIdentityUsersRepository()->save();

            $this->initLogin($dbUserModel->getUsername(), $dbUserModel->getPassword());
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->httpContext->getSession()->error = $message;

            return new View($model);
        }

        return new View();
    }

    /**
     * @Authorize
     * @Route("profile")
     */
    public function profile()
    {
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
            $message = $e->getMessage();
            $this->httpContext->getSession()->error = $message;

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
            $this->httpContext->setLoggedUser(new LoggedUser($username, $password));
            header("Location: /user/profile");
        } else {
            throw new \Exception('Invalid credentials');
        }
    }
}


