<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Repositories;

use SoftUni\FrameworkCore\Database;
use SoftUni\Models\Conference;
use SoftUni\Models\Lecture;
use SoftUni\FrameworkCore\Collections\IdentityUserCollection;
use SoftUni\Config\UserConfig;

class LecturesRepository
{
    private $query;
    private $where = " WHERE 1";
    private $placeholders = [];
    private $order = '';
    private static $selectedObjectPool = [];
    private static $insertObjectPool = [];

    /**
     * @var ConferencesRepository
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
     * @param $id
     * @return $this
     */
    public function filterByHallId($id)
    {
        $this->where .= " AND hallid = ?";
        $this->placeholders[] = $id;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function filterBySpeakerId($id)
    {
        $this->where .= " AND speakerid = ?";
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
     * @param $password
     * @return $this
     */
    public function EndDateTimeGreaterThanNow($datetime)
    {
        $this->where .= " AND enddatetime > $datetime = ?";
        $this->placeholders[] = $datetime;
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
     * @return \SoftUni\FrameworkCore\Collections\ConferencesCollection
     * @throws \Exception
     */
    public function findAll()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM lectures" . $this->where . $this->order;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entities = $result->fetchAll();
        $collection = [];
        foreach ($entities as $entityInfo) {
            $entity = new \SoftUni\Models\Lecture(
                (int)$entityInfo['id'],
                (int)$entityInfo['speakerid'],
                (int)$entityInfo['hallid'],
                $entityInfo['name'],
                $entityInfo['startdatatime'],
                $entityInfo['enddatatime']
            );

            $collection[] = $entity;
            self::$selectedObjectPool[] = $entity;
        }

        return new \SoftUni\FrameworkCore\Collections\ConferencesCollection($collection);
    }

    /**
     * @return \SoftUni\Models\Lecture
     * @throws \Exception
     */
    public function findOne()
    {
        $db = Database::getInstance('app');
        $this->query = "SELECT * FROM lectures" . $this->where . $this->order . " LIMIT 1";
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        $entityInfo = $result->fetch();
        if ($result->rowCount() > 0) {
            $entity = new \SoftUni\Models\Conference(
                (int)$entityInfo['id'],
                (int)$entityInfo['speakerid'],
                (int)$entityInfo['hallid'],
                $entityInfo['name'],
                $entityInfo['startdatatime'],
                $entityInfo['enddatatime']
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
        $this->query = "DELETE FROM lectures" . $this->where;
        $result = $db->prepare($this->query);
        $result->execute($this->placeholders);
        return $result->rowCount() > 0;
    }

    public static function add(Conference $model)
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

    private static function update(Lecture $model)
    {
        $db = Database::getInstance('app');
        $query = "UPDATE lectures SET
                      id= :id,
                      speakerid= :speakerid,
                      hallid= :hallid,
                      name= :name,
                      startdatatime= :startdatatime,
                      enddatatime= :enddatatime
                  WHERE id = :id";
        $result = $db->prepare($query);
        $result->execute(
            [
                ':id' => $model->getId(),
                ':speakerid' => $model->getSpeakerId(),
                ':hallid' => $model->hallId(),
                ':name' => $model->getName(),
                ':startdatatime' => $model->getStartDateTime(),
                ':enddatatime' => $model->getEndDateTime()
            ]
        );
    }

    private static function insert(Lecture $model)
    {
        $db = Database::getInstance('app');
        $query = "INSERT INTO lectures (id,speakerid,hallid,name,startdatatime,enddatatime)
                    VALUES (':id', ':speakerid', ':hallid', ':name', ':startdatatime', ':enddatatime')";

        $result = $db->prepare($query);
        $result->execute([
            ':id' => $model->getId(),
            ':speakerid' => $model->getSpeakerId(),
            ':hallid' => $model->getHallId(),
            ':name' => $model->getName(),
            ':startdatatime' => $model->getStartDateTime(),
            ':enddatatime' => $model->getEndDateTime()
        ]);

        $model->setId((int)$db->lastId());
    }

    private function isColumnAllowed($column)
    {
        $userClassName = 'Lecture';
        $refc = new \ReflectionClass('\SoftUni\Models\\'.$userClassName);
        $consts = $refc->getConstants();
        return in_array($column, $consts);
    }

    public function getLecturesByUserId(int $userId) {

    }
}