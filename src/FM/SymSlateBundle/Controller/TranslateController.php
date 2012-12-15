<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\TranslationsImport;
use FM\SymSlateBundle\Form\TranslationsImportType;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * @Route("/{pack_id}/{language_code}", requirements={"pack_id" = "\d+", "language_code" = "[a-z]{2}"})
     * @Template()
     */
    public function translateAction($pack_id, $language_code)
    {	
        $em = $this->getDoctrine()->getManager();
		
		$pr = $em->getRepository("FMSymSlateBundle:Pack");
		$lr = $em->getRepository("FMSymSlateBundle:Language");
		
		$pack = $pr->findOneById($pack_id);
		$language = $lr->findOneByCode($language_code);
		
		$errors = array();
		$warnings = array();
		
		if($pack and $language)
		{
			$query = $em->createQuery("SELECT m, c, ct, t FROM FMSymSlateBundle:Message m 
										JOIN m.classifications c 
										LEFT JOIN c.current_translations ct 
										LEFT JOIN ct.translation t
										WITH ct.language_id = :language_id 
										WHERE c.pack_id = :pack_id AND t.id IS NOT NULL ");
									   
			$query->setParameter('pack_id', $pack_id);
			$query->setParameter('language_id', $language->getId());
			
			$query->setFirstResult(0);
			$query->setMaxResults(10);
			
			$paginator = new Paginator($query, true);
			
			$messages = array();
			
			foreach($paginator as $message)
			{
				$classifications = $message->getClassifications();
				if(count($classifications) !== 1)
				{
					$warnings[] = "Message appears in several classifications: ".$message->getid();
				}
				foreach($classifications as $classification)
				{
					$translation = '';
					
					if(count($classification->getCurrentTranslations()) > 1)
					{
						$errors[] = "Message has multiple current translations (this is a DB bug): ".$message->getid();
					}
					else if(count($classification->getCurrentTranslations()) == 1)
					{
						$cts = $classification->getCurrentTranslations();
						$translation = $cts[0]->getTranslation()->getText();
					}
					
					$messages[$classification->getCategory()][$classification->getSection()][$classification->getSubSection()][] = array(
						"text" => $message->getText(),
						"translation" => $translation
					);
					
					//echo "<p>".$message->getText()."</p>";
				}
			}
		}
		
        return array(
        	'errors' => $errors,
        	'warnings' => $warnings,
        	'pack' => $pack,
        	'language' => $language,
        	'messages' => isset($messages)?$messages:null
		);
	}
}
