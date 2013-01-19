<?php

use Rax\Helper\ArrHelper;
use Rax\Helper\PhpHelper;

/**
 * @package   Rax\Form\Type
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * todo globally add colon : ?to labels
 */
abstract class Rax_Form_Type
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var Filter[]
     */
    protected $filters = array();

    /**
     * @var Validator[]
     */
    protected $validators = array();

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var array
     */
    protected $inlineErrors = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $options
     * @param Form   $form
     */
    public function __construct($name, array $options = array(), Form $form)
    {
        $this->name    = $name;
        $this->options = $options;
        $this->form    = $form;

        if (isset($options['label'])) {
            $this->label = $options['label'];
        }

        if (isset($options['value'])) {
            $this->value = $options['value'];
        }

        if (isset($options['placeholder'])) {
            $this->placeholder = $options['placeholder'];
        }

        if (isset($options['attributes'])) {
            $this->attributes = $options['attributes'];
        }

        if (isset($options['filters'])) {
            $this->setFilters($options['filters']);
        }

        if (isset($options['validators'])) {
            $this->setValidators($options['validators']);
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        foreach ($this->filters as $filter) {
            $value = $filter->filter($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        $this->value = $value;

        $errors       = array();
        $inlineErrors = array();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value)) {
                $errors[]       = $validator->getError();
                $inlineErrors[] = $validator->getInlineError();
            }
        }

        $this->errors       = $errors;
        $this->inlineErrors = $inlineErrors;

        return empty($errors);
    }

    /**
     * @param string $name
     *
     * @return $this
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
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            $filters = array($filters);
        }

        foreach ($filters as $filter => $options) {
            if (is_int($filter)) {
                $filter  = $options;
                $options = null;
            }

            $this->addFilter($filter, $options);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @throws Error
     *
     * @param string|Filter $filter
     * @param mixed         $options
     *
     * @return $this
     */
    public function addFilter($filter, $options)
    {
        if (is_string($filter)) {
            $class  = 'Filter_'.$filter;
            $filter = new $class($options);
        }

        if (!$filter instanceof Filter) {
            throw new Error('Invalid filter, %s must be an instance of Filter', PhpHelper::getType($this));
        }

        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param array $validators
     *
     * @return $this
     */
    public function setValidators($validators)
    {
        if (!is_array($validators)) {
            $validators = array($validators);
        }

        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @throws Error
     *
     * @param string|Validator $validator
     *
     * @return $this
     */
    public function addValidator($validator)
    {
        if (is_string($validator)) {
            $class     = 'Validator_'.$validator;
            $validator = new $class($this);
        }

        if (!$validator instanceof Validator) {
            throw new Error('Invalid validator, %s must be an instance of Validator', PhpHelper::getType($this));
        }

        $this->validators[] = $validator;

        return $this;
    }

    /**
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getInlineErrors()
    {
        return $this->inlineErrors;
    }

    /**
     * @param array $inlineErrors
     *
     * @return $this
     */
    public function setInlineErrors($inlineErrors)
    {
        $this->inlineErrors = $inlineErrors;

        return $this;
    }

    /**
     * todo normalize to getName as validator?
     *
     * @return string
     */
    public function getType()
    {
        return strtolower(Inflector::unCamelcase(substr(get_class($this), 10), '-'));
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'form-'.$this->form->getName().'-'.$this->getName();
    }

    /**
     * @return string
     */
    public function getCssClasses()
    {
        $cssClasses = array(
            'type',
            'type-'.$this->getType(),
            'type-'.$this->getType().'-'.$this->getName(),
            'type-'.$this->getType().'-'.$this->form->getName().'-'.$this->getName(),
        );

        $customCssClasses = array();

        if ($cssClassStr = ArrHelper::get($this->getAttributes(), 'class')) {
            $customCssClasses = explode(' ', $cssClassStr);
        }

        return array_merge($cssClasses, $customCssClasses);
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = array(
            'type'        => 'text',
            'name'        => $this->getName(),
            'id'          => $this->getId(),
            'class'       => implode(' ', $this->getCssClasses()),
            'value'       => $this->getValue(),
            'placeholder' => $this->getPlaceholder(),
        );

        $attributes = $attributes + $this->getAttributes();

        return array_filter($attributes, function ($value) {
            if (empty($value) && '0' !== $value) {
                return false;
            }

            return true;
        });
    }
}
