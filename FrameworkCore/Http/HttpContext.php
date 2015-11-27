<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/26/2015
 * Time: 3:21 PM
 */

namespace SoftUni\FrameworkCore\Http;


class HttpContext
{
    private $request;
    private $cookie;
    private $session;
    private $loggedUser;

    public function __construct(HttpRequest $request = null, HttpCookie $cookie = null, Session $session = null, LoggedUser $loggedUser = null)
    {
        $this->setRequest($request);
        $this->setCookies($cookie);
        $this->setSession($session);
        $this->setLoggedUser($loggedUser);
    }

    private function setRequest(HttpRequest $request = null)
    {
        if ($request == null) {
            $this->request = new HttpRequest();
        } else {
            $this->request = $request;
        }
    }

    public function getRequest() : HttpRequest
    {
        return $this->request;
    }

    private function setCookies(HttpCookie $cookie = null)
    {
        if ($cookie = null) {
            $this->cookie = new HttpCookie();
        } else {
            $this->cookie = $cookie;
        }
    }

    public function getCookies() : HttpCookie
    {
        return $this->cookie;
    }

    private function setSession(Session $session = null)
    {
        if ($session = null) {
            $this->session = new Session();
        } else {
            $this->session = $session;
        }
    }

    public function getSession() : HttpCookie
    {
        return $this->session;
    }

    private function setLoggedUser(LoggedUser $loggedUser = null)
    {
        if ($loggedUser = null) {
            $this->loggedUser = new LoggedUser($_SESSION['userId'], $_SESSION['username']);
        } else {
            $this->loggedUser = $loggedUser;
        }
    }

    public function getLoggedUser()  {
        return $this->loggedUser;
    }
}