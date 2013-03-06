<?php

namespace Rax\Mvc;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use Rax\Data\Config;

/**
 * @property Autoload   $autoload
 * @property Cfs        $cfs
 * @property ServerMode $serverMode
 * @property Config     $config
 */
class Container
{
    /**
     * @var array
     */
    protected $services = array();

    /**
     * @var array
     */
    protected $alias = array();

    /**
     * @var object[]
     */
    protected $objects = array();

    /**
     * @var Closure[]
     */
    protected $closures = array();

    /**
     * Returns a chainable instance.
     *
     * @return Container
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Sets the objects.
     *
     * @param object[] $objects
     *
     * @return Container
     */
    public function setObjects($objects)
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * Returns the objects.
     *
     * @return object[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Adds an object.
     *
     * @param string $id
     * @param object $object
     *
     * @return Container
     */
    public function addObject($id, $object)
    {
        $this->objects[$id] = $object;
        $this->alias[get_class($object)] = $id;

        return $this;
    }

    /**
     * Returns an object.
     *
     * @param string $id
     * @param mixed  $default
     *
     * @return object
     */
    public function getObject($id, $default = null)
    {
        return isset($this->objects[$id]) ? $this->objects[$id] : $default;
    }

    /**
     * Removes an object.
     *
     * @param string $id
     *
     * @return Container
     */
    public function removeObject($id)
    {
        unset($this->objects[$id]);

        return $this;
    }

    /**
     * Checks if an object exists.
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasObject($id)
    {
        return isset($this->objects[$id]);
    }

    /**
     * Sets the closures.
     *
     * @param Closure[] $closures
     *
     * @return Container
     */
    public function setClosures($closures)
    {
        $this->closures = $closures;

        return $this;
    }

    /**
     * Returns the closures.
     *
     * @return Closure[]
     */
    public function getClosures()
    {
        return $this->closures;
    }

    /**
     * Adds a closure.
     *
     * @param string $id
     * @param Closure $closure
     *
     * @return Container
     */
    public function addClosure($id, $closure)
    {
        $this->closures[$id] = $closure;

        return $this;
    }

    /**
     * Returns a closure.
     *
     * @param string $id
     * @param mixed  $default
     *
     * @return Closure
     */
    public function getClosure($id, $default = null)
    {
        return isset($this->closures[$id]) ? $this->closures[$id] : $default;
    }

    /**
     * Removes a closure.
     *
     * @param string $id
     *
     * @return Container
     */
    public function removeClosure($id)
    {
        unset($this->closures[$id]);

        return $this;
    }

    /**
     * Checks if a closure exists.
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasClosure($id)
    {
        return isset($this->closures[$id]);
    }

    /**
     * Sets a service.
     *
     * @param string         $id
     * @param object|Closure $service
     *
     * @return Container
     */
    public function add($id, $service = null)
    {
        if (is_array($id)) {
            foreach ($id as $tmpId => $tmpService) {
                $this->add($tmpId, $tmpService);
            }
        } else {
            if (is_object($service)) {
                $this->addObject($id, $service);
            } elseif ($service instanceof Closure) {
                $this->addClosure($id, $service);
            }
        }

        return $this;
    }

    public function get($id)
    {
        if (!isset($this->objects[$id])) {
            if (isset($this->closures[$id])) {
                $this->add($id, $this->closures[$id]());
            } elseif (isset($this->alias[$id])) {
                $id = $this->alias[$id];
                return $this->get($id);
            } else {
                $this->add($id, $this->build($id));
            }
        }

        return $this->objects[$id];
    }

    public function getSingleton($id)
    {

    }

    public function getInstance($id)
    {

    }

    /**
     * Removes a service.
     *
     * @param string $id
     *
     * @return Container
     */
    public function remove($id)
    {
        unset($this->alias[$id]);
        unset($this->closures[$id]);
        unset($this->closures[$id]);

        return $this;
    }

    /**
     * Checks if service exists.
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->objects[$id]) || $this->closures[$id];
    }

    /**
     * Returns a service.
     *
     * @param string $class
     *
     * @return object
     */
    public function build($class)
    {
        $reflection = new ReflectionClass($class);

        if (!$constructor = $reflection->getConstructor()) {
            return new $class();
        }

        $parameters   = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Resolve the dependencies.
     *
     * @param ReflectionParameter[] $parameters
     *
     * @return array
     */
    public function resolveDependencies(array $parameters)
    {
        $dependencies = array();
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if (null === $dependency) {
                //                xdebug_break();
            }

            $dependencies[] = $this->get($dependency->getName());
        }

        return $dependencies;
    }



}
