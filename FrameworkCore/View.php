<?php
namespace SoftUni\FrameworkCore;

class View
{
    public static $controllerName;
    public static $actionName;
    public static $area;

    const PARAMS_COUNT_MODEL_AND_VIEW = 2;
    const PARAMS_COUNT_MODEL_ONLY = 1;

    const VIEW_FOLDER = 'Views';
    const VIEW_EXTENSION = '.php';

    public function __construct()
    {
        $params = func_get_args();
        //var_dump($params);

        if (count($params) == self::PARAMS_COUNT_MODEL_AND_VIEW) {
            $view = $params[0];
            $model = $params[1];

            $this->initModelView($view, $model);
        } else {
            $model = isset($params[0]) ? $params[0] : null;

            $this->initModelOnly($model);
        }
    }

    private function initModelOnly($model)
    {
        require 'Views'
                . DIRECTORY_SEPARATOR
                . self::$actionName
                . self::VIEW_EXTENSION;
    }

    private function initModelView($view, $model)
    {
        require 'Views'
                . DIRECTORY_SEPARATOR
                . $view
                . self::VIEW_EXTENSION;
    }


}