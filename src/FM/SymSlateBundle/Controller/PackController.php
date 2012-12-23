<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\Pack;
use FM\SymSlateBundle\Form\PackType;

/**
 * Pack controller.
 *
 * @Route("/packs")
 */
class PackController extends Controller
{
    /**
     * Lists all Pack entities.
     *
     * @Route("/", name="packs")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:Pack')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Pack entity.
     *
     * @Route("/{id}/show", name="packs_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Pack')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pack entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
		
		$stats = array();
		$cats  = null;
		foreach($em->getRepository('FMSymSlateBundle:Language')->findAll() as $language)
		{
			$st   = $em->getRepository('FMSymSlateBundle:Pack')->computeStatistics($entity->getId(), $language->getId());
			$cats = $st['categories'];
			$stats[$language->getName()] = array('code' => $language->getCode(), 'statistics' => $st['statistics']);
		}

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'stats' => array('categories' => $cats, 'statistics' => $stats)
        );
    }

    /**
     * Displays a form to create a new Pack entity.
     *
     * @Route("/new", name="packs_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pack();
        $form   = $this->createForm(new PackType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Pack entity.
     *
     * @Route("/create", name="packs_create")
     * @Method("POST")
     * @Template("FMSymSlateBundle:Pack:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Pack();
        $form = $this->createForm(new PackType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$entity->setCreatedBy($this->get("security.context")->getToken()->getUser());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('packs_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
	
	/**
     * Autocompletes a Pack entity with translations as good as it can.
     *
     * @Route("/{pack_id}/autocomplete", name="pack_autocomplete", requirements={"pack_id" = "\d+"})
     * @Method("POST")
     * @Template("FMSymSlateBundle:Pack:show.html.twig")
     */
    public function autocompleteAction($pack_id)
    {
    	$this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Pack')->performAutoCompletion($pack_id);
		$this->getDoctrine()->getManager()->flush();
    	return $this->showAction($pack_id);
	}
    	

    /**
     * Displays a form to edit an existing Pack entity.
     *
     * @Route("/{id}/edit", name="packs_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Pack')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pack entity.');
        }

        $editForm = $this->createForm(new PackType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Pack entity.
     *
     * @Route("/{id}/update", name="packs_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:Pack:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Pack')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pack entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PackType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('packs_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Pack entity.
     *
     * @Route("/{id}/delete", name="packs_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:Pack')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pack entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('packs'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
