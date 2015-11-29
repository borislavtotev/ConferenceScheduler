<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Http;

class HttpRequest
{
    private $type;
    private $parameters;
    private $headers;

    public function __construct($type = null, $params = null, $headers = null)
    {
        if (isset($type)) {
            $this->setType($type);
        } else {
            $this->setType($_SERVER['REQUEST_METHOD']);
        }

        if (isset($params)) {
            $this->setParameters($params);
        } else {
            $this->setParameters($this->parseParams());
        }

        if (isset($headers)) {
            $this->setHeaders($headers);
        } else {
            $this->setHeaders(getallheaders());
        }
    }

    private function setType(string $type)
    {
        $this->type = $type;
    }

    public function getType() :string
    {
        return $this->type;
    }

    private function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders() :array
    {
        return $this->headers;
    }

    private function setParameters(\stdClass $params)
    {
        $this->parameters = $params;
    }

    public function getParameters() : \stdClass
    {
        return $this->parameters;
    }

    private function parseParams() :\stdClass
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $params = array();
        if ($method == "PUT" || $method == "DELETE" || $method == "PATCH") {
            parse_str(file_get_contents('php://input'), $params);
            $GLOBALS["_{$method}"] = $params;
            // Add these request vars into _REQUEST, mimicing default behavior, PUT/DELETE will override existing COOKIE/GET vars
            $_REQUEST = $params + $_REQUEST;
        } else if ($method == "GET") {
            $params = $_GET;
        } else if ($method == "POST") {
            $params = $_POST;
        }

        return (object)$params;
    }
}