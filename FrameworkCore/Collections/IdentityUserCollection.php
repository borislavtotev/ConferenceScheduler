<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Collections;

use SoftUni\Models\IdentityUser;

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