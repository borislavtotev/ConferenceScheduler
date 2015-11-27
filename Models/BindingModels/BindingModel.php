<?php
declare(strict_types=1);

namespace SoftUni\Models\BindingModels;

class BindingModel
{
    public static function expose()
    {
        echo "mamka mu";
        echo get_called_class();
        return get_class_vars(get_called_class());
    }
}
