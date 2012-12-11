<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\MessagesImport;
use FM\SymSlateBundle\Form\MessagesImportType;

/**
 * MessagesImport controller.
 *
 * @Route("/messagesimports")
 */
class MessagesImportController extends Controller
{
    /**
     * Lists all MessagesImport entities.
     *
     * @Route("/", name="messagesimports")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:MessagesImport')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a MessagesImport entity.
     *
     * @Route("/{id}/show", name="messagesimports_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:MessagesImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MessagesImport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new MessagesImport entity.
     *
     * @Route("/new", name="messagesimports_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MessagesImport();
        $form   = $this->createForm(new MessagesImportType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new MessagesImport entity.
     *
     * @Route("/create", name="messagesimports_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:MessagesImport:new.html.twig")
     */
    public function createAction(Request $request)
    {		
        $entity  = new MessagesImport();
        $form = $this->createForm(new MessagesImportType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			$entity->setPack($em->getRepository('FMSymSlateBundle:Pack')->findOneById($entity->getPackId()));
			$entity->setCreatedBy($this->get("security.context")->getToken()->getUser());
			$entity->upload();
			$em->persist($entity);
			$em->flush();
			
			$em->getRepository('FMSymSlateBundle:MessagesImport')->saveMessages($entity, $entity->buildMessages(), $this->get('logger'));
			
			
			
			
            //return $this->redirect($this->generateUrl('messagesimports_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MessagesImport entity.
     *
     * @Route("/{id}/edit", name="messagesimports_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:MessagesImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MessagesImport entity.');
        }

        $editForm = $this->createForm(new MessagesImportType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MessagesImport entity.
     *
     * @Route("/{id}/update", name="messagesimports_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:MessagesImport:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:MessagesImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MessagesImport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MessagesImportType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('messagesimports_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MessagesImport entity.
     *
     * @Route("/{id}/delete", name="messagesimports_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:MessagesImport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MessagesImport entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('messagesimports'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
