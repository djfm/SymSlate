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
     * @Route("/{pack_id}", requirements={"pack_id" = "\d+"}, name="translate")
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
			if($s = $request->query->get('section'))
			{
				$query_options['section'] = $s;
			}
			if($ml = $request->query->get('message_like'))
			{
				$query_options['message_like'] = $ml;
			}
			if($tl = $request->query->get('translation_like'))
			{
				$query_options['translation_like'] = $tl;
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
			if($author_id = $request->query->get('translate_author','*') and $author_id != '*')
			{
				$query_options['author_id'] = $author_id;
			}
			if($v = $request->query->get('translate_validation'))
			{
				if($v == 'has_error')$query_options['has_error'] = true;
				else if($v == 'has_warning')$query_options['has_warning'] = true;
				else if($v == 'is_clean')
				{
					$query_options['has_error'] = false;
					$query_options['has_warning'] = false;
				}
			}
			$query_options['show_context'] = ($request->query->get('context','YES') == 'YES');
			
			$pagination_options = array();
			if($p = $request->query->get('page'))
			{
				$pagination_options['page'] = $p;
			}
			
			$result = $pr->getMessagesWithTranslations($pack_id,$language->getId(),$query_options, $pagination_options);
						
		}
		
		$categories = $em->createQuery('SELECT DISTINCT c.category FROM FMSymSlateBundle:Classification c WHERE c.pack_id = :pack_id')
					     ->setParameter('pack_id', $pack_id)
					     ->getResult();

		$sections_qb = $em->createQueryBuilder();
		$sections_qb->select('DISTINCT c.section')
					 ->from ('FMSymSlateBundle:Classification','c')
					 ->where('c.pack_id = :pack_id')
					 ->andWhere('c.section != \'\'')
					 ->orderBy("c.section","ASC");

		if($request->query->get('category'))
		{
			$sections_qb->andWhere('c.category = :category');
		}

		$sections_q = $sections_qb->getQuery();
		$sections_q->setParameter('pack_id', $pack_id);

		if($c = $request->query->get('category'))
		{
			$sections_q->setParameter(':category', $c);
		}

		$sections   = array_map('current',$sections_q->getResult());
		

		$authors = $em->createQuery('SELECT DISTINCT a
									 FROM FMSymSlateBundle:User a
									 INNER JOIN a.authored_translations t
									 INNER JOIN t.current_translations ct
									 INNER JOIN ct.message m
									 INNER JOIN m.classifications c
									 WHERE c.pack_id = :pack_id AND t.language_id = :language_id 
									 AND a.roles NOT LIKE \'%ROLE_SUPER_ADMIN%\'
									 AND (t.translation_submission_id IS NOT NULL OR t.mass_imported = true)')
					     ->setParameter('pack_id', $pack_id)
					     ->setParameter('language_id', $language->getId())
					     ->getResult();

		/*
		'total_count' => count($paginator),
			'page' => $pagination_options['page'],
			'page_size' => $pagination_options['page_size'],
			'page_count' */

		$pagination = null;
		if(isset($result['pagination']))
		{
			if($result['pagination']['total_count'] > 0 and $request->query->get('page') <= $result['pagination']['page_count'] and $result['pagination']['page_count'] > 1)
			{

				$pagination = array();

				if($result['pagination']['page'] > 1)$pagination[$result['pagination']['page'] - 1] = "<<";
				if($result['pagination']['page'] < $result['pagination']['page_count'])$pagination[$result['pagination']['page'] + 1] = ">>";

				$window = 3;
				for($i = $result['pagination']['page'] - $window; $i <= $result['pagination']['page'] + $window; $i+=1)
				{
					if($i > 0 and $i < $result['pagination']['page_count'])
					{
						$pagination[$i] = $i;
					}
				}

				$pagination[1] = 'First';
				$pagination[$result['pagination']['page']] = '['.$result['pagination']['page'].']';
				$pagination[$result['pagination']['page_count']] = 'Last ('.$result['pagination']['page_count'].')';

				

				

				ksort($pagination);

			}
		}

        return array(
        	'pack'       => $pack,
        	'language'   => $language,
        	'messages'   => isset($result)?$result['messages']:null,
        	'pagination' => $pagination,
        	'categories' => $categories,
        	'sections'   => $sections,
        	'languages'  => $lr->findAll(),
        	'authors'    => $authors
		);
	}

	/**
     * 
     *
     * @Route("/{notfound}", requirements={"notfound" = ".shop_logo."})
     * 
     */
    public function defaultImageAction($notfound)
    {
    	return $this->redirect($this->container->get('templating.helper.assets')->getUrl('bundles/fmsymslate/images/prestashop_logo.png'));
	}

}
