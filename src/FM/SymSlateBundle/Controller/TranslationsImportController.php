<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\TranslationsImport;
use FM\SymSlateBundle\Form\TranslationsImportType;

/**
 * TranslationsImport controller.
 *
 * @Route("/import")
 */
class TranslationsImportController extends Controller
{
    /**
     * Lists all TranslationsImport entities.
     *
     * @Route("/", name="translationsimports")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:TranslationsImport')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a TranslationsImport entity.
     *
     * @Route("/{id}/show", name="translationsimports_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationsImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationsImport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new TranslationsImport entity.
     *
     * @Route("/new", name="translationsimports_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TranslationsImport();
        $form   = $this->createForm(new TranslationsImportType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new TranslationsImport entity.
     *
     * @Route("/create", name="translationsimports_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:TranslationsImport:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	/*
    	$session = $this->get('session');
    	$session->save();
    	session_write_close();*/
	
        $entity  = new TranslationsImport();
        $form = $this->createForm(new TranslationsImportType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			$entity->setCreator($this->get("security.context")->getToken()->getUser());
			$entity->upload();
			
            $em->persist($entity);
            $em->flush();
            
			if(!$entity->getCreator())
			{
				echo "<p><b>Creator Not Set!!!</b></p>";
			}
			else
			{
				$manager = $this->get("queue_manager");
				//$em->getRepository('FMSymSlateBundle:TranslationsImport')->saveTranslations($entity->getId(), $this->get('logger'));
				$manager->enqueueJob('FM:SymSlateBundle:Job:TranslationsImportJob', array('translations_import_id' => $entity->getId()));
				$manager->processNextJob();
			}
            	
			
            return $this->redirect($this->generateUrl('translationsimports_show', array('id' => $entity->getId(), 'backgroundprocess' => true)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TranslationsImport entity.
     *
     * @Route("/{id}/edit", name="translationsimports_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationsImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationsImport entity.');
        }

        $editForm = $this->createForm(new TranslationsImportType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TranslationsImport entity.
     *
     * @Route("/{id}/update", name="translationsimports_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:TranslationsImport:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationsImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationsImport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TranslationsImportType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('translationsimports_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a TranslationsImport entity.
     *
     * @Route("/{id}/delete", name="translationsimports_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:TranslationsImport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TranslationsImport entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('translationsimports'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
