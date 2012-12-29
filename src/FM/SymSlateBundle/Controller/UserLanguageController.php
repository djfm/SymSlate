<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\UserLanguage;
use FM\SymSlateBundle\Form\UserLanguageType;

/**
 * UserLanguage controller.
 *
 * @Route("/userlanguages")
 */
class UserLanguageController extends Controller
{
    /**
     * Lists all UserLanguage entities.
     *
     * @Route("/", name="userlanguages")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:UserLanguage')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a UserLanguage entity.
     *
     * @Route("/{id}/show", name="userlanguages_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:UserLanguage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserLanguage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new UserLanguage entity.
     *
     * @Route("/new", name="userlanguages_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new UserLanguage();
        $form   = $this->createForm(new UserLanguageType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new UserLanguage entity.
     *
     * @Route("/create", name="userlanguages_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:UserLanguage:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new UserLanguage();
        $form = $this->createForm(new UserLanguageType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('userlanguages_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserLanguage entity.
     *
     * @Route("/{id}/edit", name="userlanguages_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:UserLanguage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserLanguage entity.');
        }

        $editForm = $this->createForm(new UserLanguageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing UserLanguage entity.
     *
     * @Route("/{id}/update", name="userlanguages_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:UserLanguage:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:UserLanguage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserLanguage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserLanguageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('userlanguages_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a UserLanguage entity.
     *
     * @Route("/{id}/delete", name="userlanguages_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:UserLanguage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserLanguage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('userlanguages'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
