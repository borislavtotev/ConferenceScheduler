<?php
namespace SoftUni\Models;

use SoftUni\Core\Database;

class User
{
    const GOLD_DEFAULT = 1500;
    const FOOD_DEFAULT = 1500;

    public function register($username, $password)
    {
        $db = Database::getInstance('app');

        if ($this->exists($username)) {
            throw new \Exception("User already registered");
        }

        $result = $db->prepare("
            INSERT INTO users (username, password, gold, food)
            VALUES (?, ?, ?, ?);
        ");

        $result->execute(
            [
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                self::GOLD_DEFAULT,
                self::FOOD_DEFAULT
            ]
        );

        if ($result->rowCount() > 0) {
            $userId = $db->lastId();

            $db->query("
                INSERT INTO user_buildings (user_id, building_id, level_id)
                SELECT $userId, id, 0 FROM buildings
            ");

            return true;
        }

        throw new \Exception('Cannot register user');
    }

    public function exists($username)
    {
        $db = Database::getInstance('app');

        $result = $db->prepare("SELECT id FROM users WHERE username = ?");
        $result->execute([ $username ]);

        return $result->rowCount() > 0;
    }

    public function login($username, $password)
    {
        $db = Database::getInstance('app');
        
        $result = $db->prepare("
            SELECT
                id, username, password, gold, food
            FROM
                users
            WHERE username = ?
        ");

        $result->execute([$username]);

        if ($result->rowCount() <= 0) {
            throw new \Exception('Invalid username');
        }

        $userRow = $result->fetch();

        if (password_verify($password, $userRow['password'])) {
            return $userRow['id'];
        }

        throw new \Exception('Invalid credentials');
    }

    public function getInfo($id)
    {
        $db = Database::getInstance('app');

        $result = $db->prepare("
            SELECT
                id, username, password, gold, food
            FROM
                users
            WHERE id = ?
        ");

        $result->execute([$id]);

        return $result->fetch();
    }

    public function edit($user, $pass, $id)
    {
        $db = Database::getInstance('app');

        $result = $db->prepare("UPDATE users SET password = ?, username = ? WHERE id = ?");
        $result->execute([
            $pass,
            $user,
            $id
        ]);

        return $result->rowCount() > 0;
    }
}