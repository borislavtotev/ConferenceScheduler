<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Drivers;

class MySQLDriver extends DriverAbstract
{
    public function getDsn()
    {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName;

        return $dsn;
    }
}

