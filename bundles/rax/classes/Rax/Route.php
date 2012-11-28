<?php

/**
 *
 */
class Rax_Route
{
    const REGEX_DELIMITER = '#';

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
     * @param string $name
     * @param string $pattern
     * @param array  $defaults
     * @param array  $rules
     */
    public function __construct($name, $pattern, array $defaults, array $rules = array())
    {
        $this->name     = $name;
        $this->pattern  = $pattern;
        $this->defaults = $defaults;
        $this->rules    = $rules;
    }

    /**
     * @param ArrObj|array $routes
     *
     * @return array
     */
    public static function parse($routes = array())
    {
        $temp = array();
        foreach ($routes as $name => $route) {
            $temp[$name] = new static($name, $route['pattern'], $route['defaults'], Arr::get($route, 'rules', array()));
        }

        return $temp;
    }

    /**
     *
     */
    public function compile()
    {
        $tokens    = array();
        $placeholders = array();
        $pattern   = $this->getPattern();
        $pos       = 0;
        $len       = strlen($pattern);

        preg_match_all('#.\{(\w+)\}#', $pattern, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            if ($text = substr($pattern, $pos, $match[0][1] - $pos)) {
                $tokens[] = array('text', $text);
            }

            $pos = $match[0][1] + strlen($match[0][0]);
            $placeholder = $match[1][0];

            if ($req = $this->getRule($placeholder)) {
                $regexp = $req;
            } else {
                // Use the character preceding the variable as a separator
                $separators = array($match[0][0][0]);

                if ($pos !== $len) {
                    // Use the character following the variable as the separator when available
                    $separators[] = $pattern[$pos];
                }
                $regexp = sprintf('[^%s]+', preg_quote(implode('', array_unique($separators)), self::REGEX_DELIMITER));
            }

            $tokens[] = array('variable', $match[0][0][0], $regexp, $placeholder);

            if (in_array($placeholder, $placeholders)) {
                throw new LogicException(sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $this->getPattern(), $placeholder));
            }

            $placeholders[] = $placeholder;
        }



        if ($pos < $len) {
            $tokens[] = array('text', substr($pattern, $pos));
        }




        // find the first optional token
        $firstOptional = INF;
        for ($i = count($tokens) - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if ('variable' === $token[0] && $this->hasDefault($token[3])) {
                $firstOptional = $i;
            } else {
                break;
            }
        }

        print_r($firstOptional);
        print_r($tokens);
        die;

        // compute the matching regexp
        $regexp = '';
        for ($i = 0, $nbToken = count($tokens); $i < $nbToken; $i++) {
            $regexp .= $this->computeRegex($tokens, $i, $firstOptional);
        }

        return array(
            $this,
            'text' === $tokens[0][0] ? $tokens[0][1] : '',
            self::REGEX_DELIMITER.'^'.$regexp.'$'.self::REGEX_DELIMITER.'s',
            array_reverse($tokens),
            $placeholders
        );
    }

    /**
     * Computes the regexp used to match a specific token. It can be static text or a subpattern.
     *
     * @param array   $tokens        The route tokens
     * @param integer $index         The index of the current token
     * @param integer $firstOptional The index of the first optional token
     *
     * @return string The regexp pattern for a single token
     */
    private function computeRegex(array $tokens, $index, $firstOptional)
    {
        $token = $tokens[$index];
        if ('text' === $token[0]) {
            // Text tokens
            return preg_quote($token[1], self::REGEX_DELIMITER);
        } else {
            // Variable tokens
            if (0 === $index && 0 === $firstOptional) {
                // When the only token is an optional variable token, the separator is required
                return preg_quote($token[1], self::REGEX_DELIMITER).'(?<'.$token[3].'>'.$token[2].')?';
            } else {
                $regexp = preg_quote($token[1], self::REGEX_DELIMITER).'(?<'.$token[3].'>'.$token[2].')';

                if ($index >= $firstOptional) {
                    // Enclose each optional token in a subpattern to make it optional.
                    // "?:" means it is non-capturing, i.e. the portion of the subject string that
                    // matched the optional subpattern is not passed back.
                    $regexp = "(?:$regexp";
                    $nbTokens = count($tokens);
                    if ($nbTokens - 1 == $index) {
                        // Close the optional subpatterns
                        $regexp .= str_repeat(")?", $nbTokens - $firstOptional - (0 === $firstOptional ? 1 : 0));
                    }
                }

                return $regexp;
            }
        }
    }

    /**
     * @param string $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
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
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
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
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasDefault($key)
    {
        return array_key_exists($key, $this->defaults);
    }

    /**
     * @param string $key
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getDefault($key, $default = null)
    {
        return array_key_exists($key, $this->defaults) ? $this->defaults[$key] : $default;
    }

    /**
     * @param array $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasRule($key)
    {
        return array_key_exists($key, $this->rules);
    }

    /**
     * @param string $key
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getRule($key, $default = null)
    {
        return array_key_exists($key, $this->rules) ? $this->rules[$key] : $default;
    }
}
