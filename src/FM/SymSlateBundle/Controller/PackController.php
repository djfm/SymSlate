<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\Pack;
use FM\SymSlateBundle\Form\PackType;
use JMS\SecurityExtraBundle\Annotation\Secure;

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

        $stats = $em->getRepository('FMSymSlateBundle:Pack')->computeAllStatistics($entity->getId(), $this->getRequest()->query->get('refresh_stats','false') == 'true');

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'stats' => $stats,
            'languages'  => $em->getRepository('FMSymSlateBundle:Language')->findAll(),
            'categories' => $stats['categories']
        );
    }

    /**
     * Displays a form to create a new Pack entity.
     *
     * @Route("/new", name="packs_new")
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function autocompleteAction($pack_id)
    {
    	$manager = $this->get('queue_manager');
        $manager->enqueueJob('FM\SymSlateBundle\Service\AutocompleteService', array('language_ids' => array()));
    	return $this->showAction($pack_id);
	}
	
	/**
     * Sets the pack as the current pack
     *
     * @Route("/{pack_id}/setcurrent", name="pack_setcurrent", requirements={"pack_id" = "\d+"})
     * @Method("POST")
     * @Template("FMSymSlateBundle:Pack:show.html.twig")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function setCurrentAction($pack_id)
    {
    	$this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Pack')->setCurrent($pack_id);
		
		$this->getDoctrine()->getManager()->flush();
		
    	return $this->showAction($pack_id);
	}
    	

    /**
     * Displays a form to edit an existing Pack entity.
     *
     * @Route("/{id}/edit", name="packs_edit")
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
     * @Secure(roles="ROLE_SUPER_ADMIN")
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
