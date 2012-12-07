<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Route
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $pattern;

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
     * Constructor.
     *
     * @param string $name
     * @param string $pattern
     * @param array  $defaults
     * @param array  $rules
     */
    public function __construct($name, $pattern, array $defaults, array $rules = array())
    {
        $this->name        = $name;
        $this->pattern     = $pattern;
        $this->defaults    = $defaults;
        $this->rules       = $rules;
        $this->endsInSlash = ('/' === substr($pattern, -1));
    }

    /**
     * @param array|ArrayAccess $config
     *
     * @return array
     */
    public static function parse($config = array())
    {
        $routes = array();
        foreach ($config as $name => $route) {
            $routes[$name] = new static($name, $route['pattern'], $route['defaults'], Arr::get($route, 'rules', array()));
        }

        return $routes;
    }

    /**
     * @return string
     */
    public function compile()
    {
        $pattern = sprintf('/%s/', trim($this->getPattern(), '/'));

        preg_match_all('#\<(.+?)\>.?#', $pattern, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

        $lastPosition = 0;
        $segments     = array();
        foreach ($matches as $match) {
            $currPosition = $match[0][1] - 1;
            if ($lastPosition < $currPosition) {
                $segments[] = array(
                    'type' => 'static',
                    'text' => substr($pattern, $lastPosition, $currPosition - $lastPosition)
                );
            }
            $lastPosition = $currPosition + strlen($match[0][0]);

            $name = $match[1][0];
            $rule = $this->getRule($name, '[^'.substr($match[0][0], -1).']+');

            $segments[] = array(
                'type' => 'dynamic',
                'name' => $name,
                'rule' => $rule,
                'text' => $pattern[$currPosition],
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
        $regex = '';
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

    /**
     * Sets the route name.
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
     * Returns the route name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the raw route pattern.
     *
     * @param string $pattern
     *
     * @return Route
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Returns the raw route pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Sets the route defaults.
     *
     * @param array $defaults
     *
     * @return Route
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Returns the route defaults.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the default value for the given segment.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getDefault($key, $default = null)
    {
        return array_key_exists($key, $this->defaults) ? $this->defaults[$key] : $default;
    }

    /**
     * Checks if the segment has a default value.
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
     * Sets the route rules.
     *
     * @param array $rules
     *
     * @return Route
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Returns the route rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Returns the regex rule for the given segment.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getRule($key, $default = null)
    {
        return array_key_exists($key, $this->rules) ? $this->rules[$key] : $default;
    }

    /**
     * Checks if the segment has a regex rule.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasRule($key)
    {
        return array_key_exists($key, $this->rules);
    }

    /**
     * Sets the route regex.
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
     * @param bool $endsInSlash
     */
    public function setEndsInSlash($endsInSlash)
    {
        $this->endsInSlash = (bool) $endsInSlash;
    }

    /**
     * @return bool
     */
    public function getEndsInSlash()
    {
        return $this->endsInSlash;
    }
}
