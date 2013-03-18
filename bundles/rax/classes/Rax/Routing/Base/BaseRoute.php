<?php

namespace Rax\Routing\Base;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseRoute
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $segments = array();

    /**
     * @var bool
     */
    protected $endsInSlash;

    /**
     * @var string
     */
    protected $regex;

    /**
     * @param string $id
     * @param string $path
     * @param string $controller
     * @param array  $defaults
     * @param array  $rules
     * @param array  $filters
     */
    public function __construct($id, $path, $controller, array $defaults = array(), array $rules = array(), array $filters = array())
    {
        $this->id          = $id;
        $this->path        = $path;
        $this->controller  = $controller;
        $this->defaults    = $defaults;
        $this->rules       = $rules;
        $this->filters     = $filters;
        $this->endsInSlash = ('/' === substr($path, -1));
    }

    /**
     * Gets the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets the controller.
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Gets the defaults.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Gets a default.
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
     * Checks if a default exists.
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
     * Gets the rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Gets a rule.
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
     * Gets the filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Gets a filter.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getFilter($key, $default = null)
    {
        return isset($this->filters[$key]) ? $this->filters[$key] : $default;
    }

    /**
     * Checks if filter exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasFilter($key)
    {
        return isset($this->filters[$key]);
    }

    /**
     * Gets the segments.
     *
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * Checks if the segment exists.
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
     * Checks if the path ends in slash.
     *
     * @return boolean
     */
    public function getEndsInSlash()
    {
        return $this->endsInSlash;
    }

    /**
     * Gets the regex.
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
     * Compiles the route.
     *
     * @return string
     */
    public function compile()
    {
        $path = rtrim($this->path, '/').'/';

        preg_match_all('#\<(\w+)\>.?#', $path, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

        $lastPosition = 0;
        $segments     = array();

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

            $this->segments[] = $name;
            $segments[]     = array(
                'type' => 'dynamic',
                'name' => $name,
                'rule' => $rule,
                'text' => $path[$currPosition],
            );
        }

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
