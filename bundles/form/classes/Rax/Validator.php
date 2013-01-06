<?php

/**
 * @package   Rax\Validator
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @todo      add validation class for validating data that doesnt need a form
 *            possibly form types extend Form_Validator?
 */
abstract class Rax_Validator
{
    /**
     * The external error message.
     *
     * @var string
     */
    protected $error;

    /**
     * The inline error message.
     *
     * @var string
     */
    protected $inlineError;

    /**
     * @var Form_Type
     */
    protected $type;

    /**
     * @param Form_Type $type
     */
    public function __construct(Form_Type $type)
    {
        $this->type = $type;
    }

    /**
     * @throws Error
     *
     * @param string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        if (!static::validate($value)) {
            if (!$error = Message::get('errors.'.$this->getName().'.external')) {
                throw new Error('Missing "external.%s" error message in messages/errors.php', $this->getName());
            }

            if (!$inlineError = Message::get('errors.'.$this->getName().'.inline')) {
                throw new Error('Missing "inline.%s" error message in messages/errors.php', $this->getName());
            }

            $this->error       = Text::embedValues($error, $this->getEmbedValues());
            $this->inlineError = Text::embedValues($inlineError, $this->getEmbedValues());

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return strtolower(Inflector::unCamelcase(substr(get_class($this), 10)));
    }

    /**
     * @return array
     */
    public function getEmbedValues()
    {
        return array(
            '<form_type>'  => $this->type->getType(),
            '<name>'       => $this->type->getName(),
            '<label>'      => $this->type->getLabel(),
            '<value>'      => $this->type->getValue(),
            '<value_type>' => Php::getType($this->type->getValue()),
        );
    }

    /**
     * @param string $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $inlineError
     */
    public function setInlineError($inlineError)
    {
        $this->inlineError = $inlineError;
    }

    /**
     * @return string
     */
    public function getInlineError()
    {
        return $this->inlineError;
    }
}
