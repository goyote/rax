<?php

class View_Default_Index
{
    protected $title = 'lol';

    public function __toString()
    {
        try {
            require_once VENDOR_DIR.'Twig-1.11.1/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(BUNDLES_DIR.'app/views');
            $twig = new Twig_Environment($loader, array(
                'cache' => BUNDLES_DIR.'app/views/cache',
            ));

            return $twig->render('default/index.twig', $this);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
