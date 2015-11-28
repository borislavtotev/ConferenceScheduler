<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Repositories;

use SoftUni\FrameworkCore\Database;
use SoftUni\Models\IdentityUser;
use SoftUni\FrameworkCore\Collections\IdentityUserCollection;
use SoftUni\Config\UserConfig;

class IdentityUsersRepository
{
    private $query;
    private $where = " WHERE 1";
    private $placeholders = [];
    private $order = '';
    private static $selectedObjectPool = [];
    private static $insertObjectPool = [];

    /**
     * @var IdentityUsersRepository
     */
    private static $inst = null;

    private function __construct() { }

    public static function create()
    {
        if (self::$inst == null) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * @param $id
     * @return $this
     */
    public function filterById($id)
    {
        $this->where .= " AND id = ?";
        $this->placeholders[] = $id;
        return $this;
    }

    /**
     * @param $username
     * @return $this
     */
    public function filterByUsername($username)
    {
        $this->where .= " AND username = ?";
        $this->placeholders[] = $username;
        return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function filterByPassword($password)
    {
        $this->where .= " AND password = ?";
        $this->placeholders[] = $password;
        return $this;
    }

    /**
     * @param $column
     * @return $this
     * @throws \Exception
     */
    public function orderBy($column)
    {
        if (!$this->isColumnAllowed($column)) {
            throw new \Exception("Column not found");
        }
        if (!empty($this->order)) {
            throw new \Exception("Cannot do primary order, because you already have a primary order");
        }
        $this->order .= " ORDER BY $column";
        return $this;
    }

    /**
     * @param $column
     * @return $this
     * @throws \Exception
     */
    public function orderByDescending($column)
    {
        if (!$this->isColumnAllowed($column)) {
            throw new \Exception("Column not found");
        }
        if (!empty($this->order)) {
            throw new \Exception("Cannot do primary order, because you already have a primary order");
        }
        $this->order .= " ORDER BY $column DESC";
        return $this;
    }

    /**
     * @param $column
     * @return $this
     * @throws \Exception
     */
    public function thenBy($column)
    {
        if (empty($this->order)) {
            throw new \Exception("Cannot do secondary order, because you don't have a primary order");
        }
        if (!$this->isColumnAllowed($column)) {
            throw new \Exception("Column not found");
        }
        $this->order .= ", $column ASC";
        return $this;
    }

    /**
     * @param $column
     * @return $this
     * @throws \Exception
     */
    public function thenByDescending($column)
    {
        if (empty($this->order)) {
            throw new \Exception("Cannot do secondary order, because you don't have a primary order");
        }
        if (!$this->isColumnAllowed($column)) {
            throw new \Exception("Column not found");
        }
        $this->order .= ", $column DESC";
        return $this;
    }

    /**
     * @return IdentityUserCollection
     * @throws \Exception
     */
    public function findAll()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM users" . $this->where . $this->order;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entities = $result->fetchAll();
        $collection = [];
        foreach ($entities as $entityInfo) {
            $userClassName = UserConfig::UserIdentityClassName;
            $entity = new $userClassName($entityInfo['username'],
                $entityInfo['password'],
                $entityInfo['id']);
            $collection[] = $entity;
            self::$selectedObjectPool[] = $entity;
        }

        return new IdentityUserCollection($collection);
    }

    /**
     * @return IdentityUser
     * @throws \Exception
     */
    public function findOne()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM users" . $this->where . $this->order . " LIMIT 1";
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entityInfo = $result->fetch();
        if ($result->rowCount() > 0) {
            $userClassName = UserConfig::UserIdentityClassName;
            $entity = new $userClassName($entityInfo['username'],
                $entityInfo['password'],
                (int)$entityInfo['id']);
            self::$selectedObjectPool[] = $entity;
            return $entity;
        }

        return null;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $db = Database::getInstance('app');
        $this->query = "DELETE FROM users" . $this->where;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        return $result->rowCount() > 0;
    }

    public static function add(IdentityUser $model)
    {
        if ($model->getId()) {
            throw new \Exception('This entity is not new');
        }
        self::$insertObjectPool[] = $model;
    }

    public static function save()
    {
        foreach (self::$selectedObjectPool as $entity) {
            self::update($entity);
        }

        foreach (self::$insertObjectPool as $entity) {
            self::insert($entity);
        }

        return true;
    }

    private static function update(IdentityUser $model)
    {
        $db = Database::getInstance('app');
        $query = "UPDATE users SET username= :username, password= :password WHERE id = :id";
        $result = $db->prepare($query);
        $result->execute(
            [
                ':id' => $model->getId(),
                ':username' => $model->getUsername(),
                ':password' => $model->getPassword()
            ]
        );
    }

    private static function insert(IdentityUser $model)
    {
        $db = Database::getInstance('app');
        $query = "INSERT INTO users (username,password) VALUES ('". $model->getUsername() ."', '".$model->getPassword()."')";

        $result = $db->prepare($query);
        $result->execute([]);

        $model->setId((int)$db->lastId());
    }

    private function isColumnAllowed($column)
    {
        $userClassName = UserConfig::UserIdentityClassName;
        $refc = new \ReflectionClass('\SoftUni\Models\\'.$userClassName);
        $consts = $refc->getConstants();
        return in_array($column, $consts);
    }
}