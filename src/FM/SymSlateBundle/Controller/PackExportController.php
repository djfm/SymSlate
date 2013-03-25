<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\PackExport;
use FM\SymSlateBundle\Form\PackExportType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
        	'languages' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Language')->findAll()
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
        	'languages' => $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Language')->findAll()
		));
		
		$entity->setCreator($this->get('security.context')->getToken()->getUser());
		
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$entity->setPack($this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Pack')->find($entity->getPackId()));
            $em->persist($entity);
            $em->flush();
			
			$this->get('queue_manager')->enqueueJob('FM\SymSlateBundle\Service\PackExportService', array('pack_export_id' => $entity->getId()), false);
			//$this->get('queue_manager')->enqueueJob('FM\SymSlateBundle\Service\PackExportService', array('pack_export_id' => $entity->getId()));

            //return $this->redirect($this->generateUrl('export_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Gets the latest pack export
     *
     * @Route("/{pack_id}/{language_code}/latest/info", name="latest_export_info", requirements={"pack_id" = "\d+", "language_code" = "[a-z]{2}"})
     * @Method("GET")
     */
    public function getLatestAction($pack_id, $language_code)
    {
        $data = array('success' => false, 'found' => false);

        if($language = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code))
        {
            if($export = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:PackExport')->findLatest($pack_id, $language))
            {
                $data['success']  = true;
                $data['found']    = true;
                $data['web_path'] = $this->generateUrl('latest_export_file', array('pack_id' => $pack_id, 'language_code' => $language_code));
                $data['created']  = (string)$export->getCreated()->format('Y-m-d H:i:s');
            }
            else
            {
                $data['success'] = true;
                $data['found']   = false;
            }

            $data['generate_path'] = $this->generateUrl('generate_gzip', array('pack_id' => $pack_id, 'language_code' => $language_code));
        }
        else
        {
            $data['error_message'] = 'Unknown language.';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Gets the latest pack export
     *
     * @Route("/{pack_id}/{language_code}/latest/file", name="latest_export_file", requirements={"pack_id" = "\d+", "language_code" = "[a-z]{2}"})
     * @Method("GET")
     */
    public function downloadLatestAction($pack_id, $language_code)
    {
        $data = array('success' => false);

        if($language = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code))
        {
            if($export = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:PackExport')->findLatest($pack_id, $language))
            {
                return new Response(
                            file_get_contents($export->getAbsolutePath()),
                            200,
                            array(
                                 'Content-Type' => 'application/gzip',
                                 'Content-Disposition' => 'attachment; filename="'.$language_code.'.gzip"'
                            )
                );
            }
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Export a pack
     *
     * @Route("/{pack_id}/{language_code}/generate", name="generate_gzip", requirements={"pack_id" = "\d+", "language_code" = "[a-z]{2}"})
     * @Method("POST")
     */
    public function generateAction($pack_id, $language_code)
    {

        if($language = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code))
        {
            if($pack = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Pack')->find($pack_id))
            {
                $export = new PackExport();

                $export->setCreator($this->get('security.context')->getToken()->getUser());
                $export->setLanguageId($language->getId());
                $export->setPack($pack);

                $em = $this->getDoctrine()->getManager();
                $em->persist($export);
                $em->flush();
                
                $ran = $this->get('queue_manager')->enqueueJob('FM\SymSlateBundle\Service\PackExportService', array('pack_export_id' => $export->getId()), false);
                
                if($ran)
                {
                    return new JsonResponse(array('success' => true, 'got_file' => true, 'web_path' => $this->generateUrl('latest_export_file', array('pack_id' => $pack_id, 'language_code' => $language_code))));
                }
            }
        }
        
       return $this->redirect($this->generateUrl('latest_export_info', array('pack_id' => $pack_id, 'language_code' => $language_code)));
    }

    /**
     * Export all packs
     *
     * @Route("/{pack_id}/generate_all", name="generate_all_gzips", requirements={"pack_id" = "\d+"})
     * @Method("POST")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function generateAllAction($pack_id)
    {

        foreach($this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findAll() as $language)
        {
            if($pack = $this->getDoctrine()->getEntityManager()->getRepository('FMSymSlateBundle:Pack')->find($pack_id))
            {
                $export = new PackExport();

                $export->setCreator($this->get('security.context')->getToken()->getUser());
                $export->setLanguageId($language->getId());
                $export->setPack($pack);

                $em = $this->getDoctrine()->getManager();
                $em->persist($export);
                $em->flush();
                
                $this->get('queue_manager')->enqueueJob('FM\SymSlateBundle\Service\PackExportService', array('pack_export_id' => $export->getId()));
            }
        }
        
       return $this->redirect($this->generateUrl('packs_show', array('id' => $pack_id)));
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
