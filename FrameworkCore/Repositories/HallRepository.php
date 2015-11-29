<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Repositories;

use SoftUni\FrameworkCore\Database;
use SoftUni\Models\Conference;
use SoftUni\Models\IdentityUser;
use SoftUni\FrameworkCore\Collections\IdentityUserCollection;
use SoftUni\Config\UserConfig;
use SoftUni\Models\Hall;

class HallRepository
{
    private $query;
    private $where = " WHERE 1";
    private $placeholders = [];
    private $order = '';
    private static $selectedObjectPool = [];
    private static $insertObjectPool = [];

    /**
     * @var  HallRepository
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
     * @param $name
     * @return $this
     */
    public function filterByName($name)
    {
        $this->where .= " AND name = ?";
        $this->placeholders[] = $name;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function filterByVenueId($id)
    {
        $this->where .= " AND venueid = ?";
        $this->placeholders[] = $id;
        return $this;
    }

    public function clearFilters() {
        $this->where = " WHERE 1";
        $this->placeholders = [];
    }

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
     * @return HallCollection
     * @throws \Exception
     */
    public function findAll()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM halls" . $this->where . $this->order;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entities = $result->fetchAll();
        $collection = [];
        foreach ($entities as $entityInfo) {
            $entity = new \SoftUni\Models\Hall(
                (int)$entityInfo['id'],
                $entityInfo['name'],
                $entityInfo['venueid'],
                $entityInfo['userlimit']
            );

            $collection[] = $entity;
            self::$selectedObjectPool[] = $entity;
        }

        return new HallCollection($collection);
    }

    /**
     * @return \SoftUni\Models\Hall
     * @throws \Exception
     */
    public function findOne()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM halls" . $this->where . $this->order . " LIMIT 1";
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entityInfo = $result->fetch();
        if ($result->rowCount() > 0) {
            $entity = new \SoftUni\Models\Hall(
                (int)$entityInfo['id'],
                $entityInfo['name'],
                $entityInfo['venueid'],
                $entityInfo['userlimit']
            );
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
        $this->query = "DELETE FROM halls" . $this->where;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        return $result->rowCount() > 0;
    }

    public static function add(Hall $model)
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

    private static function update(Hall $model)
    {
        $db = Database::getInstance('app');
        $query = "UPDATE halls SET
                      id= :id,
                      name= :name,
                      venueid= :venueid,
                      userlimit= :userlimit

                  WHERE id = :id";
        $result = $db->prepare($query);
        $result->execute(
            [
                ':id' => $model->getId(),
                ':name' => $model->getName(),
                ':venueid' => $model->getVenueId(),
                ':userlimit' => $model->getUserLimit()
            ]
        );
    }

    private static function insert(Hall $model)
    {
        $db = Database::getInstance('app');
        $query = "INSERT INTO halls (id,name,venueid,userlimit)
                    VALUES (':id', ':name', ':venueid','userlimit')";

        $result = $db->prepare($query);
        $result->execute([
            ':id' => $model->getId(),
            ':name' => $model->getName(),
            ':venueid' => $model->getVenueId(),
            ':userlimit' => $model->getUserLimit()
        ]);

        $model->setId((int)$db->lastId());
    }

    private function isColumnAllowed($column)
    {
        $userClassName = 'Hall';
        $refc = new \ReflectionClass('\SoftUni\Models\\'.$userClassName);
        $consts = $refc->getConstants();
        return in_array($column, $consts);
    }
}