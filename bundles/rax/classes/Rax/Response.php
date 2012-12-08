<?php

class Rax_Response
{
    protected $content;

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function send()
    {
        echo $this->content;
    }
}
