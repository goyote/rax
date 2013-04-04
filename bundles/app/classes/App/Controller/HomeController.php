<?php

namespace App\Controller;

use Rax\Http\ClientSniffer;
use Rax\Mvc\MatchedRoute;
use Rax\Twig\TwigController;

/**
 * {@inheritdoc}
 */
class HomeController extends TwigController
{
    /**
     * @param MatchedRoute  $matchedRoute
     * @param ClientSniffer $sniffer
     */
    public function indexAction(MatchedRoute $matchedRoute)
    {
//        throw new \Exception('lol'.(2));
//        \Rax\Mvc\Debug::dump($sniffer->getDevicelol());

//        \Rax\Mvc\Debug::dump($matchedRoute->getTemplateName());
//        $view->form = $form;
        //        foreach ($em->getConnection()->getSchemaManager()->listTables() as $table) {
        //            echo $table->getName();
        //        }

        //        foreach ($this->getEntityManager()->getConnection()->getSchemaManager()->listTables() as $table) {
        //            echo $table->getName()."\n";
        //        }

        //        $entity = new Entity_Product();
        //        $form   = new Form_Test($entity);

        //        $sm = $this->getEntityManager()->getConnection()->getSchemaManager();
        //
        //        $tables = $sm->listTables();
        //
        //        foreach ($tables as $table) {
        //            echo $table->getName()."\n";
        //        }
        //        exit();

        //        echo '<pre>';
        //        print_r($this->request->getPost());
        //        echo '</pre>';

        //        if ($this->request->isPost() && $form->isValid($this->request)) {
        //            Debug::dump($entity);
        //        }

        //        $this->view->form = $form;

        //        $em = $this->getManager();
        //
        //        $cmf = new DisconnectedClassMetadataFactory();
        //        $cmf->setEntityManager($em);
        //        $metadata = $cmf->getAllMetadata();
        //
        //        $generator = new RaxEntityGenerator();
        //        $generator->setRegenerateEntityIfExists(true);
        //        $generator->setGenerateAnnotations(false);
        //        $generator->setGenerateStubMethods(true);
        //        $generator->setNumSpaces(4);
        //        $generator->setFieldVisibility(EntityGenerator::FIELD_VISIBLE_PROTECTED);
        //        $generator->setBackupExisting(false);
        //        $generator->setClassToExtend('Entity_Base');
        //        $generator->generate($metadata, APP_DIR.'classes/Entity');
        //
        //        $generator = new RaxEntityRepositoryGenerator();
        //        $generator->generate($metadata, APP_DIR.'classes/Repository');

        //        $tool = new SchemaTool($em);
        //        $sql = $tool->getUpdateSchemaSql($metadata);
        //        Debug::dump($sql);
        //
        //        $tool->updateSchema($metadata);
    }
}
