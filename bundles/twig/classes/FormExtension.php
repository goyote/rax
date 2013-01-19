<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * FormExtension extends Twig with form capabilities.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FormExtension extends Twig_Extension
{
    public $renderer;

    public function __construct(TwigRendererInterface $renderer = null) // todo remove null
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->renderer->setEnvironment($environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'form_enctype' => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_widget'  => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_errors'  => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_label'   => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_row'     => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_rest'    => new \Twig_Function_Node('SearchAndRenderBlockNode', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form';
    }
}
