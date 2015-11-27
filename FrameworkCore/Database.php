<?php

namespace SoftUni\FrameworkCore;

use SoftUni\FrameworkCore\Drivers;
use SoftUni\Config;
use SoftUni\Config\UserConfig;

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

    private static function createUserTable() {
        $db = self::getInstance('app');

        $userProperties = self::getUserProperties();

        $propertySQL = [];

        foreach ($userProperties as $property => $propertyType) {
            switch ($propertyType) {
                case 'string' :
                    $dataType = 'VARCHAR(255)';
                    break;
                case 'int' :
                    $dataType = 'INT(6)';
                    break;
                case 'float' :
                    $dataType = 'FLOAT(28,8)';
                    break;
                case 'bool' :
                    $dataType = 'BIT';
                    break;
                default :
                    $dataType = 'VARCHAR(255)';
            }

            if ($property == 'Id') {
                $dataType .= ' UNSIGNED AUTO_INCREMENT PRIMARY KEY';
            }

            $property = strtolower($property);
            $propertySQL[] = "$property $dataType";
        }

        $propertyQuery = implode(', ', $propertySQL);

        $query = "
                            CREATE TABLE if not EXISTS users  (
                           ".$propertyQuery."
                            )
                    ";

        $result = $db->prepare($query);

        $result->execute([':fields' => $propertyQuery]);

        if ($result->rowCount() > 0) {
            return true;
        }
    }

    private static function createRolesTable() {
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

    public static function createUserRolesTable() {
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

    public static function updateRolesTable() {
        $db = self::getInstance('app');

        //check whether the table roles exists
        $result = $db->prepare("SELECT 1 FROM roles LIMIT 1");
        $result->execute([]);

        // truncate the table roles
        if ($result->rowCount() > 0) {
            $roleNames = self::getColumnNames('roles');
            $result = $db->prepare("
                            TRUNCATE roles
                    ");

            $result->execute([]);
        } else {
            self::createRolesTable();
        }

        //insert the new roles
        $roles = Config\UserConfig::roles;

        foreach ($roles as $name => $id) {
            $result = $db->prepare("Insert into roles (id, name) Values (:id, :name)");

            $result->execute([
                ':id' => $id,
                ':name' => $name
            ]);
        }

        //delete from users_roles all rows for which role_id doesn't exists
        $roleIds = array_values($roles);
        $roleIdsStr = implode(",",$roleIds);
        $query = 'Delete from user_roles where role_id not in ('.$roleIdsStr.')';
        $result = $db->prepare($query);
        $result->execute([]);

        if ($result->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public static function updateUserTable() {
        $db = self::getInstance('app');

        //check whether the table roles exists
        $result = $db->prepare("SELECT 1 FROM users LIMIT 1");
        $result->execute([]);

        // truncate the table roles
        if ($result->rowCount() > 0) {
            $dbUserColumns = self::getColumnNames("users");
            $userModelPropertiesWithTypes = self::getUserProperties();
            $userModelProperties = array_keys($userModelPropertiesWithTypes);

            $hasSameProperties = true;
            foreach ($userModelProperties as $userModelProperty) {
                if (!in_array(strtolower($userModelProperty), $dbUserColumns)) {
                    $hasSameProperties = false;
                    break;
                }
            }
            foreach ($dbUserColumns as $dbUserColumn) {
                  if (!in_array(ucfirst($dbUserColumn), $userModelProperties)) {
                    $hasSameProperties = false;
                    break;
                }
            }

            if($hasSameProperties) {
                return true;
            } else {
                $result = $db->prepare("Drop TABLE users");

                $result->execute([]);

                $result = $db->prepare("
                            Truncate Table user_roles
                    ");

                $result->execute([]);

                self::createUserTable();

                return true;
            }
        } else {
            self::createUserTable();

            return true;
        }
    }

    public static function getUserRoles (int $userId) {
        $sql = "Select r.name
                from user_roles as ur
                join roles as r
                on ur.role_id = r.id
                where ur.user_id = $userId";
        try {
            $db = self::getInstance('app');
            $stmt = $db->prepare($sql);
            $stmt->execute([]);
            $output = array();
            while($row = $stmt->fetch()){
                $output[] = $row['name'];
            }
            return $output;
        }
        catch(\Exception $pe) {
            throw new \Exception('Could not connect to MySQL database. ' . $pe->getMessage());
        }
    }

    private static function getColumnNames(string $table) :array {
        $sql = "SHOW columns FROM $table";
        try {
            $db = self::getInstance('app');
            $stmt = $db->prepare($sql);
            $stmt->execute([]);
            $output = array();
            while($row = $stmt->fetch()){
                $output[] = $row['Field'];
            }
            return $output;
        }
        catch(\Exception $pe) {
            throw new \Exception('Could not connect to MySQL database. ' . $pe->getMessage());
        }
    }

    private static function getUserProperties() :array {
        try {
            $userClassName = UserConfig::UserIdentityClassName;
            $identityUserProperties = self::getClassProperties('SoftUni\\Models\\IdentityUser');
            if ($userClassName != 'IdentityUser') {
                $customUserProperties = self::getClassProperties($userClassName);
                //var_dump($customUserProperties);
                $result = $identityUserProperties;
                foreach ($customUserProperties as $customUserProperty => $type) {
                    $result[$customUserProperty] = $type;
                }

                return $result;
            }

            return $identityUserProperties;
        }
        catch(PDOException $pe) {
            throw new \Exception('Could not connect to MySQL database. ' . $pe->getMessage());
        }
    }

    private static function getClassProperties(string $userClassName) :array {
        if (preg_match_all('#[\\\\]([^\\\\]*?)$#', $userClassName, $match)) {
            $className = $match[1][0];
        }

        $output = [];
        $handle = fopen('Models'.DIRECTORY_SEPARATOR.$className.'.php', "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(preg_match("#function get([^\\s\\(\\)]*)#", $line, $match)) {
                    $property = $match[1];
                    if (preg_match("#:\\s*(string|bool|float|int)#", $line, $matchReturnTypes)) {
                        $output[$property] = $matchReturnTypes[1];
                    } else if ($property == "Id") {
                        $output[$property] = 'int';
                    } else {
                        $output[$property] = 'string';
                    }
                }
            }

            fclose($handle);
        } else {
            throw new \Exception("Unable to find the class");
        }

        return $output;
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