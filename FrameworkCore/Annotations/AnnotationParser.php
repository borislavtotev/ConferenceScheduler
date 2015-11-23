<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 10/2/2015
 * Time: 12:48 PM
 */

namespace SoftUni\FrameworkCore\Annotations;

use SoftUni\Config;
use SoftUni\Controllers;
use SoftUni\FrameworkCore\CommonFunction;
use SoftUni\FrameworkCore\Annotations;

class AnnotationParser
{
    public static $allAnnotations;

    /**
     * Returns array with annotations for controllers in Areas part of the project
     * Each array for controller contains classAnnotations and methodAnnotations
     * Method annotations contains annotations for all methods in the class
     * All Route annotations are grouped under "Routes" in annotations. Route annotations can be set for the class and
     * on methods. If there is annotation only on the class, it is ignored.
    */
    public static function getAnnotations() {
        $controllersFilePaths = CommonFunction::getDirContents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Controllers');
        $annotations = [];
        foreach ($controllersFilePaths as $controllersFilePath) {
            if (preg_match('/Controllers\\'.DIRECTORY_SEPARATOR.'(.*?).php/',
                            $controllersFilePath, $match)) {

                $className = $match[1];
                $fileName = $className.'.php';
                require_once 'Controllers'.DIRECTORY_SEPARATOR.$fileName;

                //echo "class:".$className;
                if (class_exists('SoftUni\\Controllers\\'.$className)) {
                    $annotations[$className] = [];
                    $classAnnotations = [];
                    $reflectionClass = new \ReflectionClass('SoftUni\\Controllers\\'.$className);
                    $doc = $reflectionClass->getDocComment();
                    echo "test:".json_encode($doc, JSON_PRETTY_PRINT);
                    $annotationRows = explode("\n", $doc);

                    foreach ($annotationRows as $annotationRow) {
                        echo $annotationRow."<br/>";
                        if (preg_match_all("#@([^\\(\\)]*)\((.*)\)?#", $annotationRow, $newAnnotation)) {
                            echo "machna class annotation:" . json_encode($newAnnotation, JSON_PRETTY_PRINT). "<br/>";
                            echo "new class with:".$newAnnotation[1][0]."with parameters".$newAnnotation[2][0]."<br/>";
                            $annotationClassName = __NAMESPACE__."\\".$newAnnotation[1][0]."Annotation";
                            try {
                                $annotation = new $annotationClassName();
                                $fullAnnotation = $annotation->onInitialize($newAnnotation[0][0]);
                                echo "full annotation:".$fullAnnotation."<br/>";
                            }
                            catch (\Exception $e) {
                                echo $e;
                                echo "There is no annotation class with this name: ".$newAnnotation[1][0]."<br/>";
                            }


                            //syzdai class s imeto i params


                            //foreach ($newAnnotations[0] as $newAnnotation) {
                                // echo (json_encode($newAnnotation, JSON_PRETTY_PRINT). "<br />");
                                //                            if (preg_match('/Route\((.*?)\)/', $newAnnotation, $matches)) {
                                //                                $classRouteAnnotation = $matches[1];
                                //                            }
                                //
                                //                            $userRoles = UserRoles::getAllRoles();
                                //                            $pattern = join("|", $userRoles);
                                //                            if (preg_match('/'.$pattern.'/', $newAnnotation, $matches)) {
                                //                                $classAccessAnnotation = $matches[0];
                                //                            }
                            //}
                        } else {
                            if (preg_match("#@(\\w*)#", $annotationRow, $annotationMatch)) {
                                echo "new class with:".$annotationMatch[1]."<br/>";
                            } else {
                                //throw new \Exception("Invlaid Annotation: ".$annotationRow);
                            }
                        }
                    }
//                    $methods = $reflectionClass->getMethods();
//                    foreach ($methods as $method) {
//                        $methodName = $method->getName();
//                        $methodAccessAnnotation = '';
//                        $methodDoc = $method->getDocComment();
//                        if (preg_match_all('#@(.*?)\n#s', $methodDoc, $newMethodAnnotations)) {
//                            foreach ($newMethodAnnotations[1] as $newMethodAnnotation) {
//                                $annotations['Routes'][$fullRouteAnnotation] = [$className, $methodName];
//
//
//
//                                // Get Route Annotation
//                                if (preg_match('/Route\((.*?)\)/', $newMethodAnnotation, $matches1)) {
//                                    $fullRouteAnnotation = $classRouteAnnotation.'/'.$matches1[1];
//                                    $fullRouteAnnotation = str_replace('"', '', $fullRouteAnnotation);
//                                    $fullRouteAnnotation = str_replace("'", "", $fullRouteAnnotation);
//                                    $annotations['Routes'][$fullRouteAnnotation] = [$className, $methodName];
//                                }

                                // Get Authorization Annotation
//                                $userRoles = \SoftUni\Config\UserRoles::getAllRoles();
//                                $pattern = join("|", $userRoles);
//                                if (preg_match('/'.$pattern.'/', $newMethodAnnotation, $matches)) {
//                                    if (UserRoles::getRoleNumber($classAccessAnnotation) > $matches[0]) {
//                                        $methodAccessAnnotation = $classAccessAnnotation;
//                                    } else {
//                                        $methodAccessAnnotation = $matches[0];
//                                    }
//
//                                    $annotations[$className][$methodName][] = array('Authorization' => $methodAccessAnnotation);
//                                }
//
//                                // Get HTTP Request Annotation
//                                $pattern = "/GET|POST|PUT|DELETE/";
//                                if (preg_match($pattern, $newMethodAnnotation, $matches2)) {
//                                    $annotations[$className][$methodName][] = array('HttpRequest' => $matches2[0]);
//                                }
//                            }
//                        }
//                    }
                }
            }
            //echo(json_encode($annotations, JSON_PRETTY_PRINT));
        }

        self::$allAnnotations = $annotations;
    }
}
?>