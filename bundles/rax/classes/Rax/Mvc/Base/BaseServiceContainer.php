<?php

namespace Rax\Mvc\Base;

use Closure;
use Rax\Helper\Arr;
use Rax\Mvc\Exception;
use Rax\Mvc\Kernel;
use Rax\Mvc\ServiceContainer;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionFunctionAbstract;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseServiceContainer
{
    /**
     * @var string[]
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
     * @var ServiceContainer
     */
    protected static $shared;

    /**
     * Returns a shared instance.
     *
     * @return ServiceContainer
     */
    public static function getShared()
    {
        return static::$shared ?: static::$shared = new static();
    }

    /**
     * Sets a service.
     *
     * @throws Exception
     *
     * @param string|array   $id
     * @param object|Closure $service
     *
     * @return ServiceContainer
     */
    public function set($id, $service = null)
    {
        if (is_array($id)) {
            foreach ($id as $tmpId => $service) {
                $this->set($tmpId, $service);
            }
        } else {
            if ($service instanceof Closure) {
                $this->closures[$id] = $service;
            } elseif (is_object($service)) {
                $this->alias[get_class($service)] = $id;
                $this->objects[$id]               = $service;
            } else {
                throw new Exception('Service must be an object or closure, got %s', gettype($service));
            }
        }

        return $this;
    }

    /**
     * Returns all registered services.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->closures + $this->objects;
    }

    /**
     * @return array
     */
    public function listClasses()
    {
        $tmp = array_flip($this->alias);

        foreach ($this->closures as $id => $closure) {
            try {
                $object = $this->callFunction($closure);
            } catch (\Exception $e) {
                continue;
            }
            $tmp[$id] = get_class($object);
        }

        return $tmp;
    }

    /**
     * Returns a service.
     *
     * @throws Exception
     *
     * @param string $id
     * @param string $class
     *
     * @return object
     */
    public function get($id, $class = null)
    {
        if ($class && isset($this->alias[$class])) {
            $id = $this->alias[$class];
        }

        if (!isset($this->objects[$id])) {
            if (isset($this->closures[$id])) {
                $service = $this->callFunction($this->closures[$id]);

                if ($class && !$service instanceof $class) {
                    throw new Exception('Registered closure with id "%s" must return an object of class %s, got %s', array($id, $class, get_class($service)));
                }
            } elseif ($class) {
                $service = $this->build($class);
            } else {
                throw new Exception('The "%s" service has not been registered yet', $id);
            }

            $this->set($id, $service);
        }

        return $this->objects[$id];
    }

    /**
     * Builds an object by its class name.
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

        $dependencies = $this->resolveDependencies($constructor);

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Calls a function or method.
     *
     * @throws Exception
     *
     * @param string|Closure|object $object
     * @param string                $functionName
     * @param array                 $defaults
     *
     * @return mixed
     */
    public function call($object, $functionName = null, array $defaults = array())
    {
        if ($object instanceof Closure || is_string($object)) {
            return $this->callFunction($object, $defaults);
        } elseif (is_object($object) && strlen($functionName)) {
            return $this->callMethod($object, $functionName, $defaults);
        } else {
            throw new Exception('Objec');
        }
    }

    /**
     * Calls a method.
     *
     * @param string|object $object
     * @param string        $methodName
     * @param array         $defaults
     *
     * @return mixed
     */
    public function callMethod($object, $methodName, array $defaults = array())
    {
        $method       = new ReflectionMethod($object, $methodName);
        $dependencies = $this->resolveDependencies($method, $defaults);

        return call_user_func_array(array($object, $methodName), $dependencies);
    }

    /**
     * @param string|Closure $function
     * @param array          $defaults
     *
     * @return mixed
     */
    public function callFunction($function, array $defaults = array())
    {
        $reflection   = new ReflectionFunction($function);
        $dependencies = $this->resolveDependencies($reflection, $defaults);

        return call_user_func_array($function, $dependencies);
    }

    /**
     * Resolves the dependencies.
     *
     * @param ReflectionFunctionAbstract $method
     * @param array                      $defaults
     *
     * @return array
     */
    public function resolveDependencies($method, array $defaults = array())
    {
        $dependencies = array();
        foreach ($method->getParameters() as $parameter) {
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                $dependencies[] = Arr::get($defaults, $parameter->getName()) ? : $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->get($parameter->getName(), $dependency->getName());
            }
        }

        return $dependencies;
    }

    /**
     * Resolve a non-class hinted dependency.
     *
     * @param ReflectionParameter $parameter
     *
     * @return mixed
     */
    protected function resolveNonClass(ReflectionParameter $parameter)
    {
        return $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
    }

    /**
     * Sets a service through __call().
     *
     * @param string $method
     * @param mixed  $service
     *
     * @return object|ServiceContainer
     */
    public function __call($method, $service)
    {
        $id = lcfirst(substr($method, 3));

        switch (substr($method, 0, 3)) {
            case 'set':
                return $this->set($id, $service[0]);
                break;
            case 'get':
                return $this->get($id);
                break;
        }
    }

    /**
     * Sets a service through __set().
     *
     * @param string         $id
     * @param object|Closure $service
     */
    public function __set($id, $service)
    {
        $this->set($id, $service);
    }

    /**
     * Gets a service through __get().
     *
     * @param string $id
     *
     * @return object
     */
    public function __get($id)
    {
        return $this->get($id);
    }
}
