<?php

/**
 *
 */
class Rax_View
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @param Request  $request
     * @param Response $response
     * @param Kernel   $kernel
     */
    public function __construct(Request $request, Response $response, Kernel $kernel)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->kernel   = $kernel;
    }

    /**
     * @throws Error
     *
     * @param array|string $name
     * @param mixed        $value
     *
     * @return View
     */
    public function set($name, $value = null)
    {
        if (Arr::isArray($name)) {
            foreach ($name as $key => $value) {
                $this->$key = $value;
            }
        } else {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->kernel->getTwig()->render($this->request->getMatchedRoute()->getTwigTemplateName(), array('view' => $this));
    }

    /**
     * Returns a string of css classes for the body element.
     *
     * @param array|string $append
     *
     * @return string
     */
    public function getBodyCssClasses($append = array())
    {
        $classes   = array();
        $classes[] = $this->request->getController();
        $classes[] = $this->request->getAction();

        return implode(' ', array_merge((array) $append, $classes));
    }
}
