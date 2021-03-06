<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore;

use SoftUni\FrameworkCore\Drivers;
use SoftUni\Config;

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
    public static function getInstance($instanceName = 'default')
    {
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
    )
    {
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

    public static function updateUserRolesTable()
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

    public static function updateManyToManyTable(string $table_name, string $column_one_name, string $column_two_name)
    {
        $db = self::getInstance('app');

        $result = $db->prepare("
                            CREATE TABLE if not EXISTS ".$table_name." (
                              ".$column_one_name." INT(6) NOT NULL,
                              ".$column_two_name." INT(6) NOT NULL
                            )
                    ");

        $result->execute([]);

        if ($result->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public static function updateRolesTable()
    {
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
        $query = 'Delete from user_roles where role_id not in (:roleIdsStr)';
        $result = $db->prepare($query);
        $result->execute([
            ':roleIdsStr' => $roleIdsStr
        ]);

        if ($result->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public static function updateUserTable()
    {
        $db = self::getInstance('app');

        //check whether the table roles exists
        $result = $db->prepare("SELECT 1 FROM users LIMIT 1");
        $result->execute([]);

        // truncate the table roles
        if ($result->rowCount() > 0) {
            $dbUserColumns = self::getColumnNames("users");
            $userModelPropertiesWithTypes = CommonFunction::getUserProperties();
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

                $result = $db->prepare("
                            Truncate Table user_lectures
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

    public static function updateModelTable($className)
    {
        $db = self::getInstance('app');
        //var_dump($className);
        if (preg_match_all("#\\\(\\w+?)$#", $className, $match)) {
            $table = $match[1][0] . 's';
            $table = strtolower($table);
            //var_dump($table);

            //check whether the table roles exists
            $result = $db->prepare("SELECT 1 FROM ".$table." LIMIT 1");
            $result->execute([]);

            // truncate the table roles
            if ($result->rowCount() > 0) {
                $dbUserColumns = self::getColumnNames($table);
                //var_dump($dbUserColumns);
                //echo "<br/><br/>";
                $userModelPropertiesWithTypes = CommonFunction::getClassProperties($className);
                $userModelProperties = array_keys($userModelPropertiesWithTypes);
                //var_dump($userModelProperties);
                //die;

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

                //var_dump($hasSameProperties);
                //die;
                if ($hasSameProperties) {
                    return true;
                } else {
                    $result = $db->prepare("Drop TABLE ".$table);

                    $result->execute([]);

                    self::createModelTable($className);

                    return true;
                }
            } else {
                self::createModelTable($className);

                return true;
            }
        }
    }

    public static function getUserRoles (int $userId)
    {
        $sql = "Select r.name
                from user_roles as ur
                join roles as r
                on ur.role_id = r.id
                where ur.user_id = :userId";
        try {
            $db = self::getInstance('app');
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':userId' => $userId
            ]);
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

    public static function getUserLectures (int $userId)
    {
        $sql = "Select
	l.name as Lecture,
	l.startdatetime as StartTime, l.enddatetime as EndTime, h.name as Hall, u.username as Speaker
	from user_lectures as ul
	join lectures as l
	on ul.lecture_id = l.id
	join halls as h
	on h.id = l.hallid
	join users as u
	on u.id = l.speakerid
	where ul.user_id = :userId";
        try {
            $db = self::getInstance('app');
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':userId' => $userId
            ]);
            $output = $row = $stmt->fetchAll();
            return $output;
        }
        catch(\Exception $pe) {
            throw new \Exception('Could not connect to MySQL database. ' . $pe->getMessage());
        }
    }

    public static function getUserByUsernameAndPassword(string $username,string $password) {
        $db = Database::getInstance('app');

        $result = $db->prepare("
            SELECT
                *
            FROM
                users
            WHERE username = ?
        ");

        $result->execute([$username]);

        if ($result->rowCount() <= 0) {
            throw new \Exception('Invalid username');
        }

        $userRow = $result->fetch();

        return $userRow;
    }

    public static function addRoleToUser (int $userId, int $roleId) {
        $db = Database::getInstance('app');

        $result = $db->prepare("
            insert into user_roles values(:userId, :roleId);
        ");

        $result->execute([
            ':userId' => $userId,
            ':roleId' => $roleId
        ]);
    }

    private function initializeDb($host, $user, $pass, $dbName)
    {
        $connection = mysqli_connect($host, $user, $pass);
        if (!mysqli_select_db($connection, $dbName)) {
            mysqli_query($connection, "CREATE DATABASE if not EXISTS $dbName");
            mysqli_close($connection);
        }
    }

    private static function createUserTable()
    {
        $db = self::getInstance('app');

        $userProperties = CommonFunction::getUserProperties();

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

    private static function createModelTable($className)
    {
        $db = self::getInstance('app');

        $modelProperties = CommonFunction::getClassProperties($className);

        if (preg_match_all("#\\\(\\w+?)$#", $className, $match)) {
            $table = $match[1][0].'s';
            $table = strtolower($table);

            $propertySQL = [];

            foreach ($modelProperties as $property => $propertyType) {
                if (strpos(strtolower($property), 'datetime')) {
                    $dataType = 'DATETIME';
                } else {
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
                }

                $property = strtolower($property);
                $propertySQL[] = "$property $dataType";
            }

            $propertyQuery = implode(', ', $propertySQL);

            $query = "CREATE TABLE if not EXISTS ".$table." (".$propertyQuery.")";

            $result = $db->prepare($query);

            $result->execute([
                ':propertyQuery' => $propertyQuery,
                ':table' => $table
            ]);
//
//            echo "CREATE TABLE if not EXISTS ".$table."  (".$propertyQuery.")";
//            die;

            if ($result->rowCount() > 0) {
                return true;
            }
        }
    }

    private static function createRolesTable()
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

    private static function getColumnNames(string $table) :array
    {
        $sql = "SHOW columns FROM :table";
//        var_dump($table);
//        var_dump($sql.$table);
        try {
            $db = self::getInstance('app');
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':table' => $table
            ]);
            $output = array();
            while($row = $stmt->fetch()){
                $output[] = $row['Field'];
            }
//            var_dump($row);
//            var_dump($output);
//            die;

            return $output;
        }
        catch(\Exception $pe) {
            throw new \Exception('Could not connect to MySQL database. ' . $pe->getMessage());
        }
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