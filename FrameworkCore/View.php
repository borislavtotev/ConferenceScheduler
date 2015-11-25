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
            if ($this->checkModelType($model)) {
                $this->initModelOnly($model);
            } else {
                $_SESSION['error'] = "Inproper type model is assign to this view!";

                require 'Views'
                    . DIRECTORY_SEPARATOR
                    . 'error'
                    . self::VIEW_EXTENSION;
            }
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

    private function checkModelType($model) {
        $viewPath = 'Views'
            . DIRECTORY_SEPARATOR
            . self::$actionName
            . self::VIEW_EXTENSION;

        $handle = fopen($viewPath, "r");
        if ($handle) {
            $countLines = 1;
            while (($line = fgets($handle)) !== false || $countLines < 5) {
                // process the line read.
                if (preg_match('#@var\\s+([^\\s]*)\\s+\\$model#', $line, $match)) {
                    if ($model instanceof $match[1]) {
                        return true;
                    } else {
                        return false;
                    }
                }
                $countLines++;
            }

            fclose($handle);
        } else {
            return false;
        }

        return true;
    }


}