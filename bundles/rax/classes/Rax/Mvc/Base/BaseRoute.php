<?php

namespace Rax\Mvc\Base;

use Rax\Mvc\Exception;
use Rax\Mvc\Object;
use Rax\Mvc\Route;
use ArrayAccess;
use Rax\Helper\Arr;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseRoute extends Object
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var string
     */
    protected $regex;

    /**
     * @var bool
     */
    protected $endsInSlash;

    /**
     * @var array
     */
    protected $segments;

    /**
     * @var array
     */
    protected $specialRuleKeys = array(
        'ajax',
        'secure',
        'method',
        'clientIp',
        'serverIp',
        'environment',
        'auth',
        'acl',
    );

    /**
     * @param array|ArrayAccess $config
     *
     * @return Route[]
     */
    public static function parse($config = array())
    {
        $routes = array();
        foreach ($config as $name => $route) {
            $routes[$name] = new static($name, $route['path'], $route['defaults'], Arr::get($route, 'rules', array()));
        }

        return $routes;
    }

    /**
     * @throws Exception
     *
     * @param string $name
     * @param string $path
     * @param array  $defaults
     * @param array  $rules
     */
    public function __construct($name, $path, array $defaults, array $rules = array())
    {
        $this->name        = $name;
        $this->path        = $path;
        $this->defaults    = $defaults;
        $this->rules       = $rules;
        $this->endsInSlash = ('/' === substr($path, -1));
    }

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return Route
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the path.
     *
     * @param string $path
     *
     * @return Route
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the defaults.
     *
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Returns the defaults.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Return a default value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return string
     */
    public function getDefault($key, $default = null)
    {
        return array_key_exists($key, $this->defaults) ? $this->defaults[$key] : $default;
    }

    /**
     * Checks if default value exists for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasDefault($key)
    {
        return array_key_exists($key, $this->defaults);
    }

    /**
     * Sets the rules.
     *
     * @param array $rules
     *
     * @return Route
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Returns the rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Returns a rule.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getRule($key, $default = null)
    {
        return isset($this->rules[$key]) ? $this->rules[$key] : $default;
    }

    /**
     * Checks if rule exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasRule($key)
    {
        return isset($this->rules[$key]);
    }

    /**
     * Returns the special rules.
     *
     * @return array
     */
    public function getSpecialRules()
    {
        return array_filter(Arr::get($this->rules, $this->specialRuleKeys));
    }

    /**
     * Sets the special rule keys.
     *
     * @param array $specialRuleKeys
     *
     * @return Route
     */
    public function setSpecialRuleKeys($specialRuleKeys)
    {
        $this->specialRuleKeys = $specialRuleKeys;

        return $this;
    }

    /**
     * Returns the special rule keys.
     *
     * @return array
     */
    public function getSpecialRuleKeys()
    {
        return $this->specialRuleKeys;
    }

    /**
     * Sets the regex.
     *
     * @param string $regex
     *
     * @return Route
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Returns the compiled route regex.
     *
     * @return string
     */
    public function getRegex()
    {
        if (null === $this->regex) {
            $this->regex = $this->compile();
        }

        return $this->regex;
    }

    /**
     * Sets whether the path ends slash.
     *
     * @param bool $endsInSlash
     *
     * @return Route
     */
    public function setEndsInSlash($endsInSlash)
    {
        $this->endsInSlash = (bool) $endsInSlash;

        return $this;
    }

    /**
     * Returns whether the path ends slash.
     *
     * @return boolean
     */
    public function getEndsInSlash()
    {
        return $this->endsInSlash;
    }

    /**
     * Sets the segments.
     *
     * @param array $segments
     *
     * @return Route
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * Returns the segments.
     *
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * Checks if the segment was defined in the path.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasSegment($key)
    {
        return in_array($key, $this->segments);
    }

    /**
     * Compiles the route.
     *
     * @return string
     */
    public function compile()
    {
        $path = rtrim($this->getPath(), '/').'/';

        preg_match_all('#\<(\w+)\>.?#', $path, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

        $lastPosition = 0;
        $segments     = array();
        $segmentNames = array();

        foreach ($matches as $match) {
            $currPosition = $match[0][1] - 1;
            if ($lastPosition < $currPosition) {
                $segments[] = array(
                    'type' => 'static',
                    'text' => substr($path, $lastPosition, $currPosition - $lastPosition),
                );
            }
            $lastPosition = $currPosition + strlen($match[0][0]);

            $name = $match[1][0];
            $rule = $this->getRule($name, '[^'.substr($match[0][0], -1).']+');

            $segmentNames[] = $name;
            $segments[]     = array(
                'type' => 'dynamic',
                'name' => $name,
                'rule' => $rule,
                'text' => $path[$currPosition],
            );
        }

        $this->segments = $segmentNames;

        $length = strlen($path) - 1;
        if ($length > $lastPosition) {
            $segments[] = array(
                'type' => 'static',
                'text' => substr($path, $lastPosition, $length - $lastPosition),
            );
        }

        $firstOptional = INF;
        foreach (array_reverse($segments, true) as $i => $segment) {
            if ('dynamic' !== $segment['type'] || !$this->hasDefault($segment['name'])) {
                break;
            }
            $firstOptional = $i;
        }

        $totalSegments = count($segments);
        $regex         = '';
        foreach ($segments as $i => $segment) {
            $regexChunk = '';
            switch ($segment['type']) {
                case 'static':
                    $regexChunk = preg_quote($segment['text'], '#');
                    break;
                case 'dynamic':
                    $regexChunk = sprintf(
                        '%s(?<%s>%s)',
                        preg_quote($segment['text'], '#'),
                        preg_quote($segment['name'], '#'),
                        $segment['rule']
                    );

                    if ($i >= $firstOptional) {
                        $regexChunk = '(?:'.$regexChunk;
                        if (($totalSegments - 1) === $i) {
                            $regexChunk .= str_repeat(')?', $totalSegments - $firstOptional);
                        }
                    }
                    break;
            }

            $regex .= $regexChunk;
        }

        return '#^'.$regex.'/?$#';
    }
}
