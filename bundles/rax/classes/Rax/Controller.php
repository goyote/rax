<?php

/**
 *
 */
class Rax_Controller
{
    /**
     * @var bool
     */
    protected $autoRender = true;

    /**
     * @var array
     */
    protected $viewMap = array();

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function before()
    {
    }

    public function after()
    {
    }
}
