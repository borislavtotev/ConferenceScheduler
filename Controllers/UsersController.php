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

/**
 * @Route("users")
 */
class UsersController extends Controller
{
    /**
     * @Route("login")
     * @GET
     */
    public function getLogin() {
        if ($this->httpContext->getLoggedUser()->getId() != null) {
            header("Location: /users/profile/my");
        }

        $model = new UserLoginBindingModel();
        return new View('login', $model);
    }

    /**
     * @Route("login")
     * @POST
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

        return new View();
    }

    /**
     * @GET
     */
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: /");
    }

    /**
     * @Route("register")
     * @GET
     */
    public function getRegister() {
        if ($this->httpContext->getLoggedUser()->getId() != null) {
            header("Location: /users/profile/my");
        }

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

            $this->initLogin($username, $password);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->httpContext->getSession()->error = $message;

            return new View($model);
        }

        return new View();
    }

    /**
     * @Authorize
     * @Route("profile/my")
     * @GET
     */
    public function getMyProfile() {
        $username = $this->httpContext->getLoggedUser()->getUsername();
        $userId = $this->httpContext->getLoggedUser()->getId();
        $model = new UserViewModel($username, $userId);

        return new View('profile', $model);
    }

    /**
     * @Authorize
     * @Route("profile/my")
     * @POST
     */
    public function myProfileUpdate(UserChangeProfileBindingModel $model)
    {
        $userViewModel = new UserViewModel("");

        try {
            $errorMsgs = '';

            if ($model->getUsername() == null) {
                $errorMsgs = "Missing username. ";
            };

            $username = $model->getUsername();
            $userViewModel->setUsername($username);

            if ($model->getPassword() == null) {
                $errorMsgs .= "Missing password. ";
            };

            $password = $model->getPassword();

            if ($model->getNewpass() == null) {
                $errorMsgs .= "Missing new password. ";
            };

            $newpass = $model->getNewpass();

            if (strlen($newpass) < 4 ) {
                $errorMsgs .= "The password should be at least 4 characters. ";
            }

            if ($model->getConfirm() == null) {
                $errorMsgs .= "Missing confirm password. ";
            };

            $confirm = $model->getConfirm();

            if ($newpass !== $confirm) {
                $errorMsgs .= "New Password and Confirm password are different. ";
            }

            $loggedUserId = $this->httpContext->getLoggedUser()->getId();
            $loggedUsername = $this->httpContext->getLoggedUser()->getUsername();

            if ($username != $loggedUsername) {
                $dbUserWithTheNewUserName = $this->dbContext->getIdentityUsersRepository()->filterByUsername($username)->findOne();
                if ($dbUserWithTheNewUserName != null) {
                    $errorMsgs .= 'User with this username already exists';
                }
            }

            $this->dbContext->getIdentityUsersRepository()->clearFilters();
            $dbUserModel = $this->dbContext->getIdentityUsersRepository()->filterById($loggedUserId)->findOne();
            if ($dbUserModel != null) {
                if (!password_verify($password, $dbUserModel->getPassword())) {
                    $errorMsgs .= 'Current password is not valid.';
                }
            } else {
                $errorMsgs .= "Unable to find this user! ";
            }

            if ($errorMsgs != '') {
                throw new \Exception($errorMsgs);
            }

            $dbUserModel->setPassword(password_hash($newpass, PASSWORD_DEFAULT));
            $dbUserModel->setUsername($username);

            $this->dbContext->getIdentityUsersRepository()->save();

            $this->httpContext->getLoggedUser()->setUsername($username);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->httpContext->getSession()->error = $message;

            return new View('profile', $userViewModel);
        }

        return new View('profile', $userViewModel);
    }

    private function initLogin($username, $password)
    {
        $user = $this->dbContext->getIdentityUsersRepository()->filterByUsername($username)->findOne();
        if (!isset($user)) {
            throw new \Exception('Invalid credentials');
        }

        if (password_verify($password, $user->getPassword())) {
            $this->httpContext->setLoggedUser(new LoggedUser($user->getId(), $username));
            header("Location: /users/profile/my");
        } else {
            throw new \Exception('Invalid credentials');
        };
    }
}


