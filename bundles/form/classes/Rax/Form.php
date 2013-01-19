<?php

/**
 * @package   Rax\Form
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * todo add global filters validation rules?
 * todo data transformers? views
 * todo normalize return $this vs return Class
 */
class Rax_Form
{
    /**
     * @var Form_Type[]
     */
    protected $children = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $extraData = array();

    /**
     * All external errors messages.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * All inline error messages.
     *
     * @var array
     */
    protected $inlineErrors = array();

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $method = 'post';

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @var object
     */
    protected $model;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Constructor.
     *
     * @param array|object $data
     */
    public function __construct($data = null)
    {
        $this->build();

        if (is_array($data)) {
            $this->setData($data);
        } elseif (is_object($data)) {
            $this->bind($data);
        }
    }

    /**
     * @param object $model
     */
    public function bind($model)
    {
        $this->reflection = new ReflectionClass($model);
        $this->model      = $model;
    }

    /**
     * @return $this
     */
    public function updateModel()
    {
        if (null !== $this->model) {
            $this->model->update($this->data);
        }

        return $this;
    }

    /**
     * Builds the form.
     */
    public function build()
    {
    }

    /**
     * @throws Error
     *
     * @param array|Request $newData
     *
     * @return bool
     */
    public function isValid($newData)
    {
        if ($newData instanceof Request) {
            $newData = $newData->getPost() + $newData->getQuery();
        }

        if (!is_array($newData)) {
            throw new Error('data todo');
        }

        $errors       = array();
        $inlineErrors = array();
        $data         = array();
        $extraData    = array();

        foreach ($newData as $name => $value) {
            if (isset($this->children[$name])) {
                $value = $this->children[$name]->filter($value);

                if (!$this->children[$name]->isValid($value)) {
                    $errors[$name]       = $this->children[$name]->getErrors();
                    $inlineErrors[$name] = $this->children[$name]->getInlineErrors();
                }

                $data[$name] = $value;
            } else {
                $extraData[$name] = $value;
            }
        }

        $this->data         = $data + $this->data;
        $this->extraData    = $extraData + $this->extraData;
        $this->errors       = $errors;
        $this->inlineErrors = $inlineErrors;

        $this->updateModel();

        return empty($errors);
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = array(
            'action' => $this->getAction(),
            'method' => $this->getMethod(),
            'class'  => 'form form-'.$this->getName(),
        );

        return $attributes;
    }

    /**
     * @throws Error
     *
     * @param string|Form_Type $name
     * @param string           $type
     * @param array            $options
     *
     * @return Form
     */
    public function add($name, $type = null, array $options = array())
    {
        if ($name instanceof Form_Type) {
            $this->children[$name->getName()] = $name;
        } elseif (is_string($name)) {
            $class                 = Symbol::buildTypeClassName($type);
            $this->children[$name] = new $class($name, $options, $this);
        } else {
            throw new Error('Form::add() expects parameter 1 to be a string or descendant of Form_Type, %s given', PhpHelper::getType($name));
        }

        return $this;
    }

    /**
     * @param Form_Type[] $children
     *
     * @return $this
     */
    public function set($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Form_Type
     */
    public function get($name = null)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    public function getFields()
    {
        return $this->children;
    }

    /**
     * @param string $name
     */
    public function delete($name)
    {
        unset($this->children[$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * @param array $newData
     *
     * @return $this
     */
    public function setData(array $newData)
    {
        $data      = array();
        $extraData = array();

        foreach ($newData as $key => $value) {
            if (isset($this->children[$key])) {
                $data[$key] = $value;
            } else {
                $extraData[$key] = $value;
            }
        }

        $this->data      = $data;
        $this->extraData = $extraData;
        $this->updateModel();

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function updateData(array $data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->children[$key])) {
                $this->data[$key] = $value;
            } else {
                $this->extraData[$key] = $value;
            }
        }

        $this->updateModel();

        return $this;
    }

    /**
     * @param array $extraData
     *
     * @return $this
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->data + $this->extraData;
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
     * @param string $name
     *
     * @return string
     */
    public function getError($name)
    {
        return isset($this->errors[$name]) ? $this->errors[$name] : null;
    }

    /**
     * Returns all external error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasError($name)
    {
        return isset($this->errors[$name]);
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return int
     */
    public function countErrors()
    {
        return count($this->errors);
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
     * @param string $name
     *
     * @return string
     */
    public function getInlineError($name)
    {
        return isset($this->inlineErrors[$name]) ? $this->inlineErrors[$name] : null;
    }

    /**
     * Returns all internal error messages.
     *
     * @return array
     */
    public function getInlineErrors()
    {
        return $this->inlineErrors;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasInlineError($name)
    {
        return isset($this->inlineErrors[$name]);
    }

    /**
     * @return bool
     */
    public function hasInlineErrors()
    {
        return !empty($this->inlineErrors);
    }

    /**
     * @return int
     */
    public function countInlineErrors()
    {
        return count($this->inlineErrors);
    }

    /**
     * @return array
     */
    public function getAllErrors()
    {
        return array(
            'external' => $this->getErrors(),
            'inline'   => $this->getInlineErrors(),
        );
    }

    /**
     * @param object $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return object
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return strtolower(Inflector::unCamelcase(substr(get_class($this), 5), '-'));
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}
