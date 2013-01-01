<?php

/**
 * @package   Rax\Generator
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Symbol
{
    /**
     * @param string $entity
     *
     * @return string
     */
    public static function buildEntityClassName($entity)
    {
        return 'Entity_'.Inflector::ucWords(Inflector::toUnderscore($entity));
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    public static function buildRepositoryClassName($entity)
    {
        return 'Repository_'.Inflector::ucWords(Inflector::toUnderscore($entity));
    }

    /**
     * @param array|string $controller
     *
     * @return string
     */
    public static function buildControllerClassName($controller)
    {
        return 'Controller_'.Inflector::ucWords(Inflector::toUnderscore($controller));
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function buildViewClassName($controller, $action)
    {
        return 'View_'.Inflector::ucWords(Inflector::toUnderscore($controller)).'_'.Inflector::toCamelcase($action, true);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public static function buildActionMethodName($action)
    {
        return Inflector::toCamelcase($action).'Action';
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function buildTwigTemplateName($controller, $action)
    {
        return Inflector::to('/', strtolower($controller)).'/'.Inflector::toHyphen(strtolower($action)).'.twig';
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public static function buildId($id)
    {
        return Inflector::toHyphen(strtolower($id));
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
}
