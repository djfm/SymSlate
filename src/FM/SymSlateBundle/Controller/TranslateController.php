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
 * Translate controller.
 *
 * @Route("/translate")
 */
class TranslateController extends Controller
{
    /**
     * 
     *
     * @Route("/{pack_id}", requirements={"pack_id" = "\d+"})
     * @Template()
     */
    public function translateAction($pack_id)
    {
    	$request = Request::createFromGlobals();
		
		$language_code = $request->query->get('language_code');
			
        $em = $this->getDoctrine()->getManager();
		
		$pr = $em->getRepository("FMSymSlateBundle:Pack");
		$lr = $em->getRepository("FMSymSlateBundle:Language");
		
		$pack = $pr->findOneById($pack_id);
		$language = $lr->findOneByCode($language_code);
		
		$errors = array();
		$warnings = array();
		
		if($pack and $language)
		{
			$query_options = array();
			if($c = $request->query->get('category'))
			{
				$query_options['category'] = $c;
			}
			if($e = $request->query->get('empty'))
			{
				$query_options['empty'] = $e;
			}
			if($sc = $request->query->get('source_language_code'))
			{
				if($source_language = $lr->findOneByCode($sc))
				{
					$query_options['source_language_id'] = $source_language->getId();
				}
			}
			
			$result = $pr->getMessagesWithTranslations($pack_id,$language->getId(),$query_options);
			
			if($e = $request->query->get('source_language_id'))
			
			$messages = $result['messages'];
			
			echo "<p>Results total: ".$result['pagination']['total_count']."</p>";
			
			
		}
		
		$categories = $em->createQuery('SELECT DISTINCT c.category FROM FMSymSlateBundle:Classification c WHERE c.pack_id = :pack_id')
					     ->setParameter('pack_id', $pack_id)
					     ->getResult();
		
        return array(
        	'pack'       => $pack,
        	'language'   => $language,
        	'messages'   => isset($result)?$result['messages']:null,
        	'count'      => isset($result)?$result['pagination']['total_count']:0,
        	'categories' => $categories,
        	'languages'  => $lr->findAll()
		);
	}
}