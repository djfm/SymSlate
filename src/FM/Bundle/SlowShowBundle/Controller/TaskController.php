<?php

namespace FM\Bundle\SlowShowBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\Bundle\SlowShowBundle\Entity\Task;
use FM\Bundle\SlowShowBundle\Form\TaskType;

/**
 * Task controller.
 *
 * @Route("/tasks")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task entities.
     *
     * @Route("/", name="tasks")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSlowShowBundle:Task')->findAll();

        return array(
            'entities' => $entities,
        );
    }
	
	
	/**
     * Starts a task if there are any pending
     *
     * @Route("/dequeue", name="tasks_dequeue")
     * 
     */
    public function dequeueAction()
    {
        $this->get("queue_manager")->processNextJob();       

        return $this->redirect($this->generateUrl('tasks'));
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}/show", name="tasks_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSlowShowBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="tasks_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Task();
        $form   = $this->createForm(new TaskType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/create", name="tasks_create")
     * @Method("POST")
     * @Template("FMSlowShowBundle:Task:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Task();
        $form = $this->createForm(new TaskType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tasks_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="tasks_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSlowShowBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createForm(new TaskType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}/update", name="tasks_update")
     * @Method("POST")
     * @Template("FMSlowShowBundle:Task:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSlowShowBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TaskType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tasks_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}/delete", name="tasks_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSlowShowBundle:Task')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Task entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tasks'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
