<?php

namespace SoftUni\FrameworkCore;

use SoftUni\FrameworkCore\Drivers;

class Database
{
    private static $inst = [];

    /**
     * @var \PDO
     */
    private $db;

    private function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $instanceName
     * @return Database
     * @throws \Exception
     */
    public static function getInstance($instanceName = 'default') {
        if (!isset(self::$inst[$instanceName])) {
            throw new \Exception('Instance with that name was not set');
        }

        return self::$inst[$instanceName];
    }

    public static function setInstance(
        $instanceName,
        $driver,
        $user,
        $pass,
        $dbName,
        $host = null
    ) {
        $driver = Drivers\DriverFactory::create($driver, $user, $pass, $dbName, $host);

        try {
            $pdo = new \PDO(
                $driver->getDsn(),
                $user,
                $pass
            );
        }
        catch(\PDOException $e) {
            self::initializeDb($host, $user, $pass, $dbName);
            try {
                $pdo = new \PDO(
                    $driver->getDsn(),
                    $user,
                    $pass
                );
            }
            catch(\PDOException $e1) {
                throw new \Exception("Unable to build the database.");
            }
        }

        self::$inst[$instanceName] = new self($pdo);
    }

    /**
     * @param string $statement
     * @param array $driverOptions
     * @return Statement
     */
    public function prepare($statement, array $driverOptions = [])
    {
        $statement = $this->db->prepare($statement, $driverOptions);

        return new Statement($statement);
    }

    public function query($query)
    {
        $this->db->query($query);
    }

    public function lastId($name = null)
    {
        return $this->db->lastInsertId($name);
    }

    private function initializeDb($host, $user, $pass, $dbName) {
        $connection = mysqli_connect($host, $user, $pass);
        if (!mysqli_select_db($connection, $dbName)) {
            mysqli_query($connection, "CREATE DATABASE if not EXISTS $dbName");
            mysqli_close($connection);
        }
    }

    public static function createUserTable() {
        $db = self::getInstance('app');

        $result = $db->prepare("
                            CREATE TABLE if not EXISTS users  (
                            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            username VARCHAR(30) NOT NULL,
                            password VARCHAR(255) NOT NULL,
                            reg_date TIMESTAMP
                            )
                    ");

        $result->execute([]);

        if ($result->rowCount() > 0) {
            return true;
        }
    }

    public static function createRoleTable()
    {
        $db = self::getInstance('app');

        $result = $db->prepare("
                            CREATE TABLE if not EXISTS roles (
                            id INT(6) NOT NULL,
                            name VARCHAR(50) NOT NULL
                            )
                    ");

        $result->execute([]);

        if ($result->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public static function createUserRoleTable()
    {
        $db = self::getInstance('app');

        $result = $db->prepare("
                            CREATE TABLE if not EXISTS user_roles (
                              user_id INT(6) NOT NULL,
                              role_id INT(6) NOT NULL
                            )
                    ");

        $result->execute([]);

        if ($result->rowCount() > 0) {
            return true;
        }

        return false;
    }
}

class Statement
{

    /**
     * @var \PDOStatement
     */
    private $stmt;

    public function __construct(\PDOStatement $statement)
    {
        $this->stmt = $statement;
    }

    /**
     * @param int $fetchStyle
     * @return mixed
     */
    public function fetch($fetchStyle = \PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($fetchStyle);
    }

    public function fetchAll($fetchStyle = \PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($fetchStyle);
    }

    public function bindParam($parameter, &$variable, $dataType = \PDO::PARAM_STR, $length = null, $driverOptions = null)
    {
        return $this->stmt->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
    }

    /**
     * @param array|null $inputParameters
     * @return bool
     */
    public function execute(array $inputParameters = null)
    {
        return $this->stmt->execute($inputParameters);
    }

    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}