<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/29/2015
 * Time: 8:58 AM
 */

namespace SoftUni\FrameworkCore\Collections;

use SoftUni\Models\Lecture;

class LecturesCollection
{
    /**
     * @var Lecture[];
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