<?php

namespace Rax\Mvc\Base;

use Rax\Helper\Inflector;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseSymbol
{
    /**
     * @param string $entity
     *
     * @return string
     */
    public static function buildEntityClassName($entity)
    {
        return 'Entity_'.static::buildClassName($entity);
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    public static function buildRepositoryClassName($entity)
    {
        return 'Repository_'.static::buildClassName($entity);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function buildTypeClassName($type)
    {
        return 'Form_Type_'.static::buildClassName($type);
    }

    /**
     * @param array|string $controller
     *
     * @return string
     */
    public static function buildControllerClassName($controller)
    {
        return 'Controller_'.static::buildClassName($controller);
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function buildViewClassName($controller, $action)
    {
        return 'View_'.static::buildClassName($controller).'_'.Inflector::toCamel($action, true);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public static function buildActionMethodName($action)
    {
        return Inflector::toCamel($action).'Action';
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function buildTwigTemplateName($controller, $action)
    {
        return Inflector::to('/', strtolower($controller)).'/'.static::buildId($action).'.twig';
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public static function buildId($id)
    {
        return Inflector::toUnderscore(strtolower($id));
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public static function buildClassName($class)
    {
        return Inflector::ucWords(Inflector::toUnderscore($class));
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    public static function buildEntityClassNames(array $entities)
    {
        $tmp = array();
        foreach ($entities as $entity) {
            $tmp[] = static::buildEntityClassName($entity);
        }

        return $tmp;
    }

    /**
     * @param array $controllers
     *
     * @return array
     */
    public static function buildControllerClassNames(array $controllers)
    {
        $tmp = array();
        foreach ($controllers as $controller) {
            $tmp[] = static::buildControllerClassName($controller);
        }

        return $tmp;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function buildViewClassNames(array $arr)
    {
        $tmp = array();
        foreach ($arr as $controller => $action) {
            $tmp[] = static::buildViewClassName($controller, $action);
        }

        return $tmp;
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public static function buildActionMethodNames(array $actions)
    {
        $tmp = array();
        foreach ($actions as $action) {
            $tmp[] = static::buildActionMethodName($action);
        }

        return $tmp;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function buildTwigTemplateNames(array $arr)
    {
        $tmp = array();
        foreach ($arr as $controller => $action) {
            $tmp[] = static::buildTwigTemplateName($controller, $action);
        }

        return $tmp;
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public static function buildIds(array $ids)
    {
        $tmp = array();
        foreach ($ids as $id) {
            $tmp[] = static::buildId($id);
        }

        return $tmp;
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    public static function buildClassNames(array $classes)
    {
        $tmp = array();
        foreach ($classes as $class) {
            $tmp[] = static::buildClassName($class);
        }

        return $tmp;
    }
}
