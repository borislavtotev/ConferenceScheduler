<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 10/2/2015
 * Time: 12:48 PM
 */
declare(strict_types=1);

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
        $annotations['byType'] = [];
        $annotations['byController'] = [];

        foreach ($controllersFilePaths as $controllersFilePath) {
            if (preg_match('/Controllers\\'.DIRECTORY_SEPARATOR.'(.*?).php/',
                            $controllersFilePath, $match)) {

                $className = $match[1];
                $fileName = $className.'.php';
                require_once 'Controllers'.DIRECTORY_SEPARATOR.$fileName;

                //echo "class:".$className;

                // Get  Class Annotations
                if (class_exists('SoftUni\\Controllers\\'.$className)) {
                    $classAnnotations = [];
                    $reflectionClass = new \ReflectionClass('SoftUni\\Controllers\\'.$className);
                    $doc = $reflectionClass->getDocComment();
                    if ($doc) {
                        $classAnnotations = self::extractAnnotations($doc, $classAnnotations);
                        echo "<br/><br/>".json_encode($classAnnotations, JSON_PRETTY_PRINT)."<br/>";
                    }

                    // Get Method Annotations
                    $methods = $reflectionClass->getMethods();
                    foreach ($methods as $method) {
                        $methodName = $method->getName();
                        $methodAccessAnnotation = '';
                        $methodDoc = $method->getDocComment();

                        if ($methodDoc) {
                            $methodAnnotations = self::extractAnnotations($methodDoc, $classAnnotations);
                            echo $methodName.": ".json_encode($methodAnnotations, JSON_PRETTY_PRINT)."<br/>";
                        } else {
                            $methodAnnotations = $classAnnotations;
                        }

                        // Add extracted annotaions to all Annotations
                        foreach ($methodAnnotations as $methodAnnotationType => $methodAnnotationProperty) {
                            $annotations['byType'][$methodAnnotationType][] = array(
                                "property" => $methodAnnotationProperty,
                                "controller" => $className,
                                "action" => $methodName
                            );
                            $annotations['byController'][$className][$methodName][$methodAnnotationType] = $methodAnnotationProperty;
                        }

                        echo "<br/>All Annotations:<br/>".json_encode($annotations, JSON_PRETTY_PRINT)."<br/>";
                    }
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

    private static function extractAnnotations(string $comments, $classAnnotations) :array {
        $extractedAnnotations = [];
        echo "test:".json_encode($comments, JSON_PRETTY_PRINT);
        $annotationRows = explode("\n", $comments);

        foreach ($annotationRows as $annotationRow) {
            echo $annotationRow."<br/>";
            if (preg_match_all("#@([^\\(\\)]*)\((.*)\)?#", $annotationRow, $newAnnotation)) {
                echo "machna class annotation:" . json_encode($newAnnotation, JSON_PRETTY_PRINT). "<br/>";
                echo "new class with:".$newAnnotation[1][0]."with parameters".$newAnnotation[2][0]."<br/>";
                $annotationType = $newAnnotation[1][0]; // Route, Authorization, etc.
                $annotationParams = $newAnnotation[0][0];
            } else if (preg_match("#@(\\w*)#", $annotationRow, $annotationMatch)) {
                $annotationType = $annotationMatch[1]; // Route, Authorization, etc.
                $annotationParams = null;
                echo "new class with:".$annotationMatch[1]."<br/>";
            } else {
                continue;
            }

            $annotationClassName = __NAMESPACE__."\\".$annotationType."Annotation";

            if (class_exists($annotationClassName)) {
                $annotation = new $annotationClassName();
                if (array_key_exists($annotationType, $classAnnotations)) {
                    $fullAnnotation = $annotation->onInitialize($annotationParams, $classAnnotations[$annotationType]);
                } else {
                    $fullAnnotation = $annotation->onInitialize($annotationParams, null);
                }

                $extractedAnnotations[$annotationType] = $fullAnnotation;
                echo "full annotation:".$fullAnnotation."<br/>";
            } else {
                echo "There is no annotation class with this name: ".$annotationType."<br/>";
            }
        }

        return $extractedAnnotations;
    }
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
?>



