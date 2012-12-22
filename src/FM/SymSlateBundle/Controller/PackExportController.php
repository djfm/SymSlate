<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\PackExport;
use FM\SymSlateBundle\Form\PackExportType;

/**
 * PackExport controller.
 *
 * @Route("/export")
 */
class PackExportController extends Controller
{
    /**
     * Lists all PackExport entities.
     *
     * @Route("/", name="export")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:PackExport')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a PackExport entity.
     *
     * @Route("/{id}/show", name="export_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:PackExport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PackExport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new PackExport entity.
     *
     * @Route("/new", name="export_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PackExport();
        $form   = $this->createForm(new PackExportType(), $entity, array(
        	'packs' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Pack')->getPackNames(),
        	'languages' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Language')->getLanguageNames()
			)
		);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new PackExport entity.
     *
     * @Route("/create", name="export_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:PackExport:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new PackExport();
        $form = $this->createForm(new PackExportType(), $entity, array(
			'packs' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Pack')->getPackNames(),
        	'languages' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Language')->getLanguageNames()
		));
		
		$entity->setCreator($this->get('security.context')->getToken()->getUser());
		
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
			
			$this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:PackExport')->performExport($entity->getId());

            return $this->redirect($this->generateUrl('export_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PackExport entity.
     *
     * @Route("/{id}/edit", name="export_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:PackExport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PackExport entity.');
        }

        $editForm = $this->createForm(new PackExportType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing PackExport entity.
     *
     * @Route("/{id}/update", name="export_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:PackExport:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:PackExport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PackExport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PackExportType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('export_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a PackExport entity.
     *
     * @Route("/{id}/delete", name="export_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:PackExport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PackExport entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('export'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
