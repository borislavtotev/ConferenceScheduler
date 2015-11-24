<?php

namespace SoftUni\FrameworkCore\Collections;

use SoftUni\Models\IdentityUser;

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/24/2015
 * Time: 10:23 AM
 */
class IdentityUserCollection
{
    /**
     * @var IdentityUser[];
     */
    private $collection = [];

    public function __construct($models = [])
    {
        $this->collection = $models;
    }

    /**
     * @param callable $callback
     */
    public function each(Callable $callback)
    {
        foreach ($this->collection as $model) {
            $callback($model);
        }
    }
}