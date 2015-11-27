<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Annotations;

use SoftUni\Config;
use SoftUni\Controllers;
use SoftUni\FrameworkCore\CommonFunction;
use SoftUni\FrameworkCore\Annotations;

class AnnotationParser
{
    public static $allAnnotations;

    public static function getAnnotations()
    {
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
                        //echo "<br/><br/>".json_encode($classAnnotations, JSON_PRETTY_PRINT)."<br/>";
                    }

                    // Get Method Annotations
                    $methods = $reflectionClass->getMethods();
                    foreach ($methods as $method) {
                        $methodName = $method->getName();
                        $methodAccessAnnotation = '';
                        $methodDoc = $method->getDocComment();

                        if ($methodDoc != null) {
                            $methodAnnotations = self::extractAnnotations($methodDoc, $classAnnotations);
                            //echo $methodName.": ".json_encode($methodAnnotations, JSON_PRETTY_PRINT)."<br/>";
                        } else {
                            $methodAnnotations = [];
                            foreach ($classAnnotations as $annotationType => $value) {
                                if ($annotationType != 'Route') {
                                    $methodAnnotations[$annotationType] = $value;
                                }
                            }
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

                        // Add GET annotation if no Get, Post, Put or Delete annotation is available
                        if (isset($annotations['byController'][$className][$methodName])) {
                            //var_dump($annotations['byController'][$className][$methodName]);
                            $httpRequestAnnotation = array_filter(array_keys($annotations['byController'][$className][$methodName]), function ($annotationType) {
                                //var_dump($annotationType);
                                if (preg_match('#(Get|Post|Delete|Put)#i', $annotationType)) {
                                    return true;
                                }

                                return false;
                            });
                        } else {
                            $httpRequestAnnotation = [];
                        }

                        if (count($httpRequestAnnotation) == 0) {
                            $getAnnotation = new GetAnnotation();
                            $getAnnotationProperty = $getAnnotation->onInitialize('GET');
                            $annotations['byController'][$className][$methodName]['GET'] = $getAnnotationProperty;
                        }
                    }
                }
            }
        }

        self::$allAnnotations = $annotations;
    }

    private static function extractAnnotations(string $comments, $classAnnotations) :array
    {
        $extractedAnnotations = [];
        //echo "test:".json_encode($comments, JSON_PRETTY_PRINT);
        $annotationRows = explode("\n", $comments);

        foreach ($annotationRows as $annotationRow) {
            //echo $annotationRow."<br/>";
            if (preg_match_all("#@([^\\(\\)]*)\((.*)\)?#", $annotationRow, $newAnnotation)) {
                //echo "machna class annotation:" . json_encode($newAnnotation, JSON_PRETTY_PRINT). "<br/>";
                //echo "new class with:".$newAnnotation[1][0]."with parameters".$newAnnotation[2][0]."<br/>";
                $annotationType = $newAnnotation[1][0]; // Route, Authorization, etc.
                $annotationParams = $newAnnotation[0][0];
            } else if (preg_match("#@(\\w*)#", $annotationRow, $annotationMatch)) {
                $annotationType = $annotationMatch[1]; // Route, Authorization, etc.
                $annotationParams = $annotationType;
                //echo "new class with:".$annotationMatch[1]."<br/>";
            } else {
                continue;
            }

            $annotationClassName = ucwords(strtolower($annotationType))."Annotation";

            $annotationFullClassName = __NAMESPACE__."\\".$annotationClassName;

            if (class_exists($annotationFullClassName)) {
                $annotation = new $annotationFullClassName();
                if (array_key_exists($annotationType, $classAnnotations)) {
                    $fullAnnotation = $annotation->onInitialize($annotationParams, $classAnnotations[$annotationType]);
                } else {
                    $fullAnnotation = $annotation->onInitialize($annotationParams, null);
                }

                $extractedAnnotations[$annotationType] = $fullAnnotation;
                //echo "full annotation:".$fullAnnotation."<br/>";
            } else {
                //echo "There is no annotation class with this name: ".$annotationType."<br/>";
            }
        }

        return $extractedAnnotations;
    }
}