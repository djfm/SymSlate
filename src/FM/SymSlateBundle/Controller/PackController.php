<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $entities = $em->getRepository('FMSymSlateBundle:Pack')->findBy(array(), array('is_current' => 'desc'));

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
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:Pack')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pack entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
	
	    $cheat = $request->query->get('cheat','true') == 'true';
	
        $stats = $em->getRepository('FMSymSlateBundle:Pack')->computeAllStatistics($entity->getId(), $this->getRequest()->query->get('refresh_stats','false') == 'true', $cheat);

        $sections_qb = $em->createQueryBuilder();
        $sections_qb->select('DISTINCT c.section')
                     ->from ('FMSymSlateBundle:Classification','c')
                     ->where('c.pack_id = :pack_id')
                     ->andWhere('c.section != \'\'')
                     ->orderBy("c.section","ASC");

        $sections_q = $sections_qb->getQuery();
        $sections_q->setParameter('pack_id', $id);

        $sections   = array_map('current',$sections_q->getResult());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'stats' => $stats,
            'languages'  => $em->getRepository('FMSymSlateBundle:Language')->findAll(),
            'categories' => $stats['categories'],
            'sections' => $sections
        );
    }

    /**
     * Finds and displays stats for pack and language.
     *
     * @Route("/{id}/{language_code}/show", name="packs_show_language_stats")
     * @Template()
     */
    public function showLanguageStatsAction($id, $language_code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity   = $em->getRepository('FMSymSlateBundle:Pack')->find($id);
        $language = $em->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code);

        $stats = $em->getRepository('FMSymSlateBundle:Pack')->computeDetailedStatistics($entity->getId(), $language->getId());

        return array('stats' => $stats, 'entity' => $entity, 'language_code' => $language_code, 'language' => $language);
    }

      /**
     * Edits an existing Pack entity.
     *
     * @Route("/{id}/{language_code}/po", name="pack_export_po")
     * @Method("GET")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function exportPoAction($id, $language_code)
    {
        $em         = $this->getDoctrine()->getManager();
        $pack       = $em->getRepository('FMSymSlateBundle:Pack')->find($id);
        $language   = $em->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code);

        $path = tempnam(null, null);
        $file = fopen($path, 'w');

$header = <<<'NOW'
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"


NOW;
        fputs($file, $header);

        $storages   = $em->getRepository('FMSymSlateBundle:Pack')->getStoragesWithTranslations($id, $language->getId());
        foreach($storages as $storage)
        {
            $msgid   = str_replace('"', "\\\"", $storage->getMessage()->getText());
            $cts     = $storage->getMessage()->getCurrentTranslations();
            $msgstr  = str_replace('"', "\\\"", count($cts) == 1 ? $cts[0]->getTranslation()->getText() : '');
            $msgctxt = $storage->getMessage()->getMkey();
            fputs($file, "msgctxt \"$msgctxt\"\n");
            fputs($file, "msgid \"$msgid\"\n");
            fputs($file, "msgstr \"$msgstr\"\n");
            fputs($file, "\n");
        }

        fclose($file);

        return new Response(
                file_get_contents($path),
                200,
                array(
                     'Content-Type' => 'text/x-gettext-translation',
                     'Content-Disposition' => 'attachment; filename="'.$language_code.'.po"'
                )
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
     * Download all latest gzips for this pack
     *
     * @Route("/{pack_id}/download_all", name="download_all_gzips")
     * @Method("GET")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function downloadAllAction($pack_id)
    {
        $query = $this->getDoctrine()->getManager()->createQuery(
            "SELECT p FROM FMSymSlateBundle:PackExport p
             WHERE p.pack_id = :pack_id
             AND p.id IN (SELECT MAX(q.id) FROM FMSymSlateBundle:PackExport q WHERE q.pack_id = :pack_id and q.language_id = p.language_id)
            ");

        $query->setParameter('pack_id', $pack_id);

        $file = tempnam(null, null);
        $archive  = new \Archive_Tar($file, 'gz');

        foreach($query->getResult() as $export)
        {
            $language = $this->getDoctrine()->getManager()->getRepository("FMSymSlateBundle:Language")->findOneById($export->getLanguageId());
            $path     = $export->getAbsolutePath();
            if(file_exists($path))
            {
                $archive->addString($language->getCode().".gzip", file_get_contents($path));
            }
        }

        $headers = array(
            'Content-Type' => 'application/x-gzip',
            'Content-Disposition' => 'attachment; filename="all_'.$this->getDoctrine()->getManager()->getRepository("FMSymSlateBundle:Pack")->findOneById($pack_id)->getVersion().'.tar.gz"'
        );  

         return new Response(file_get_contents($file), 200, $headers);

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

    /**
     * Deletes Messages a Pack entity.
     *
     * @Route("/{id}/deleteMessages", name="pack_delete_messages")
     * @Method("POST")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function deleteMessagesAction(Request $request, $id)
    {
        $category = $request->request->get('category');
        $section  = $request->request->get('section');

        if($category != '' or $section != '')
        {
            $em = $this->getDoctrine()->getManager();

            $qb = $em->createQueryBuilder();
            $qb->select('c')->from('FMSymSlateBundle:Classification', 'c')->where('c.pack_id = :pack_id');
            if($category != '')
            {
                $qb->andWhere('c.category = :category');
            }
            if($section != '')
            {
                $qb->andWhere('c.section = :section');
            }

            $q = $qb->getQuery();

            $q->setParameter('pack_id', $id);

            if($category != '')
            {
                $q->setParameter('category', $category);
            }
            if($section != '')
            {
                $q->setParameter('section', $section);
            }

            $killed = 0;
            foreach($q->getResult() as $classification)
            {
                $killed += 1;
                $message_id = $classification->getMessageId();

                $em->remove($em->getRepository('FMSymSlateBundle:Storage')->findOneBy(array('message_id' => $message_id, 'pack_id' => $id)));
                $em->remove($classification);
            }

            $em->flush();

            $this->get('session')->setFlash('notice', "Successfully killed $killed messages!");
        }
        else
        {
            $this->get('session')->setFlash('notice', "Not killed nothing!");
        }       

        return $this->redirect($this->generateUrl('packs_show', array('id' => $id)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
