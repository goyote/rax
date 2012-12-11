<?php

class Controller_Default extends Controller
{
    public function indexAction()
    {
        $this->response->setContent(new View_Default_Index());
    }
}
