<?php

/**
 *
 */
class Rax_SymbolGenerator
{
    /**
     * @param string $entity
     *
     * @return string
     */
    public static function getEntityClassName($entity)
    {
        return 'Entity_'.Inflector::ucWords(Inflector::toUnderscore($entity));
    }

    /**
     * @param array|string $controller
     *
     * @return string
     */
    public static function getControllerClassName($controller)
    {
        return 'Controller_'.Inflector::ucWords(Inflector::toUnderscore($controller));
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function getViewClassName($controller, $action)
    {
        return 'View_'.Inflector::ucWords(Inflector::toUnderscore($controller)).'_'.Inflector::toCamelcase($action, true);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public static function getActionMethodName($action)
    {
        return Inflector::toCamelcase($action).'Action';
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return string
     */
    public static function getTwigTemplateName($controller, $action)
    {
        return Inflector::to('/', strtolower($controller)).'/'.Inflector::toHyphen(strtolower($action)).'.twig';
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public static function getId($id)
    {
        return Inflector::toHyphen(strtolower($id));
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    public static function getEntityClassNames(array $entities)
    {
        $tmp = array();
        foreach ($entities as $entity) {
            $tmp[] = static::getEntityClassName($entity);
        }

        return $tmp;
    }

    /**
     * @param array $controllers
     *
     * @return array
     */
    public static function getControllerClassNames(array $controllers)
    {
        $tmp = array();
        foreach ($controllers as $controller) {
            $tmp[] = static::getControllerClassName($controller);
        }

        return $tmp;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function getViewClassNames(array $arr)
    {
        $tmp = array();
        foreach ($arr as $controller => $action) {
            $tmp[] = static::getViewClassName($controller, $action);
        }

        return $tmp;
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public static function getActionMethodNames(array $actions)
    {
        $tmp = array();
        foreach ($actions as $action) {
            $tmp[] = static::getActionMethodName($action);
        }

        return $tmp;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function getTwigTemplateNames(array $arr)
    {
        $tmp = array();
        foreach ($arr as $controller => $action) {
            $tmp[] = static::getTwigTemplateName($controller, $action);
        }

        return $tmp;
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public static function getIds(array $ids)
    {
        $tmp = array();
        foreach ($ids as $id) {
            $tmp[] = static::getId($id);
        }

        return $tmp;
    }
}
