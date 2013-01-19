<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\Language;
use FM\SymSlateBundle\Form\LanguageType;

/**
 * Language controller.
 *
 * @Route("/languages")
 */
class LanguageController extends Controller
{
    /**
     * Lists all Language entities.
     *
     * @Route("/", name="languages")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:Language')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Language entity.
     *
     * @Route("/{id}/show", name="languages_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Autocompletes a language
     *
     * @Route("/{id}/autocomplete", name="languages_autocomplete")
     * @Method("POST")
     */
    public function autocompleteAction($id)
    {
	    $manager = $this->get('queue_manager');
	    $manager->enqueueJob('FM\SymSlateBundle\Service\AutocompleteService', array('language_ids' => array($id)));
	    return $this->redirect($this->generateUrl("languages"));
    }
    /**
     * Displays a form to create a new Language entity.
     *
     * @Route("/new", name="languages_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Language();
        $form   = $this->createForm(new LanguageType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Language entity.
     *
     * @Route("/create", name="languages_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:Language:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Language();
        $form = $this->createForm(new LanguageType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('languages_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Language entity.
     *
     * @Route("/{id}/edit", name="languages_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $editForm = $this->createForm(new LanguageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Language entity.
     *
     * @Route("/{id}/update", name="languages_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:Language:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LanguageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('languages_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Language entity.
     *
     * @Route("/{id}/delete", name="languages_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:Language')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Language entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('languages'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
