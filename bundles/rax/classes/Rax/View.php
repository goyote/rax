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
     * @var Kernel
     */
    protected $kernel;

    /**
     * @param Request $request
     * @param Kernel  $kernel
     */
    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->kernel  = $kernel;
    }

    /**
     * @param string|array $name
     * @param mixed        $value
     *
     * @return View
     */
    public function set($name, $value = null)
    {
        if (Arr::isArray($name)) {
            foreach ($name as $tempName => $tempValue) {
                $this->set($tempName, $tempValue);
            }
        } elseif (!in_array($name, array('request', 'kernel'))) {
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
}
