<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/29/2015
 * Time: 8:41 AM
 */

namespace SoftUni\FrameworkCore\Collections;

use \SoftUni\Models\Conference;

class ConferencesCollection
{
    /**
     * @var Conference[];
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