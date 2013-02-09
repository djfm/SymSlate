<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * MassImportController controller.
 *
 * @Route("/massimport")
 * 
 */
class MassImportController extends Controller
{
	/**
     * 
     *
     * @Route("/", name="mass_import")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function indexAction()
    {
    	return array();
    }

    /**
     * 
     *
     * @Route("/", name="post_mass_import")
     * @Method("POST")
     * @Template("FMSymSlateBundle:MassImport:index.html.twig")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function importAction()
    {
    	//SQL logger will kill memory limit if not disabled
    	$this->getDoctrine()->getManager()->getconfiguration()->setSQLLogger(null);

    	$file = $this->getRequest()->files->get('csv');
    	$file->move('uploads/massimports/',$file->getClientOriginalName());
    	
    	$fd   = fopen('uploads/massimports/' . $file->getClientOriginalName(), 'r');

    	$headers = fgetcsv($fd);
    	//print_r($headers);

    	//bench
    	$new=0;
    	$old=0;
    	$dt =time();

    	while($row = fgetcsv($fd))
    	{
    		$row = array_combine($headers,$row);

    		//1 Get or Create the USER
    		$userManager = $this->get('fos_user.user_manager');

    		if($user = $userManager->findUserByEmail($row['Email']))
    		{
    			//OK
    		}
    		else
    		{
    			$user = $userManager->createUser();
		    	$user->setUsername(current(explode("@", $row['Email'])));
			    if(!$row['Email'])continue;
		    	$user->setEmail($row['Email']);
		    	$user->setPassword(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,32);
		    	$userManager->updateUser($user);
		    	$this->getDoctrine()->getManager()->flush();
    		}

    		//2 Create the language if necessary
    		if($language = $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Language')->findOneByCode($row['Language Code']))
    		{
    			//OK
    		}
    		else
    		{
    			$language = new \FM\SymSlateBundle\Entity\Language();
    			$language->setCode($row['Language Code']);
    			$this->getDoctrine()->getManager()->persist($language);
    			$this->getDoctrine()->getManager()->flush();
    		}
	    	
	    	//3 Create the permissions if needed
	    	if(!$user->canTranslateInto($language))
	    	{
	    		$userLanguage = new \FM\SymSlateBundle\Entity\UserLanguage();
	    		$userLanguage->setUser($user);
	    		$userLanguage->setLanguage($language);
	    		$user->addUserLanguage($userLanguage);
	    		$this->getDoctrine()->getManager()->persist($user);
	    		$this->getDoctrine()->getManager()->flush();
	    	}

	    	//4 Create the translation if needed
	    	if($translation = $this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:Translation')->findOneBy(array(
	    		"language_id" => $language->getId(),
	    		"mkey" => $row['Key'],
	    		"text" => $row['Translation'],
	    		"created_by" => $user->getId()
	    	)))
    		{
    			//OK
    			$old+=1;
    		}
    		else
    		{
    			$translation = new \FM\SymSlateBundle\Entity\Translation();
    			$translation->setLanguage($language);
    			$translation->setMkey($row['Key']);
    			$translation->setText($row['Translation']);
    			$translation->setAuthor($user);
    			$translation->setMassImported(true);
    			$this->getDoctrine()->getManager()->persist($translation);
    			$this->getDoctrine()->getManager()->getRepository('FMSymSlateBundle:CurrentTranslation')->actualizeWith($translation);
	    		$this->getDoctrine()->getManager()->flush();
	    		$new+=1;
    		}

    		if($old >= 50 or $new >= 50)
    		{
    			$this->get('logger')->info("[UOW size: " . $this->getDoctrine()->getManager()->getUnitOfWork()->size() . "]");
    			$this->get('logger')->info("New: " . sprintf("%4.1f/s",$new/(time()-$dt+0.00001)) . " - Old: " . sprintf("%4.1f/s",$old/(time()-$dt+0.00001)));
    			$old=0;
    			$new=0;
    			$dt =time();
    		}
    		

    		$this->getDoctrine()->getManager()->clear();

    	}

    	fclose($fd); 

    	return array();
    }
}
