<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\TranslationsImport;
use FM\SymSlateBundle\Form\TranslationsImportType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * VariousOperations controller.
 *
 * @Route("/work")
 */
class VariousOperationsController extends Controller
{	
    /**
     * 
     *
     * @Route("/clean", name="clean_operation")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Template()
     */
    public function cleanAction()
    {

    	$em = $this->getDoctrine()->getManager();

    	$q  = $em->createQuery("SELECT t FROM FMSymSlateBundle:Translation t WHERE t.text LIKE '%transla.shop.tm%'");

$exp = <<<'NOW'
/http:\/\/transla\.shop\.tm\/[^%]+/
NOW;

    	$replaced = 0;

    	foreach($q->getResult() as $translation)
    	{
    		$text = preg_replace($exp, '', $translation->getText());
    		$translation->setText($text);
    		$em->persist($translation);
    		$replaced += 1;
    	}

    	$em->flush();

    	return array('replaced' => $replaced);
    }

}
