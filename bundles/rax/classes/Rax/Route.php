<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @method Route  setName(string $name)                          Sets the route name.
 * @method string getName()                                      Returns the route name.
 * @method Route  setPattern(string $pattern)                    Sets the raw route pattern.
 * @method string getPattern()                                   Returns the raw route pattern.
 * @method Route  setDefaults(array $defaults)                   Sets the route defaults.
 * @method array  getDefaults()                                  Returns the route defaults.
 * @method mixed  getDefault(string $key, mixed $default = null) Returns the default value for the given segment.
 * @method bool   hasDefault(string $key)                        Checks if the segment has a default value.
 * @method Route  setRegex(string $regex)                        Sets the route regex.
 * @method Route  setRules(array $rules)                         Sets the route rules.
 * @method array  getRules()                                     Returns the route rules.
 * @method mixed  getRule(string $key, mixed $default = null)    Returns the regex rule for the given segment.
 * @method bool   hasRule(string $key)                           Checks if the segment has a regex rule.
 * @method bool   getEndsInSlash()                               Checks if the pattern ends in slash.
 */
class Rax_Route extends Object
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
     * Sets whether the pattern ends slash.
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
}
