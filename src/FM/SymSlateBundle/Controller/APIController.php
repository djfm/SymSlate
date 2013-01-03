<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\TranslationSubmission;
use FM\SymSlateBundle\Form\TranslationSubmissionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * TranslationSubmission controller.
 *
 * @Route("/api")
 */
class APIController extends Controller
{
	/**
     * 
     *
     * @Route("/", name="api_list_functions")
     */
    public function indexAction()
    {
    	$methods = array();

    	if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
    	{
    		$methods['hi'] = "ho";
    	}
    	return new JsonResponse($methods);
    }

    /**
     * @Route("/users/create", name="api_create_users")
     * @Method("POST")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function createUsersAction()
    {
    	$data = array();

    	return new JsonResponse(json_decode($this->getRequest()->getContent(), true));
    	$params      = json_decode($this->getRequest()->getContent(), true);
    	$username    = $params['username'];
    	$email       = $params['email'];
    	$password    = $params['password'];

    	return new JsonResponse(array($username,$email,$password));

    	$userManager = $this->get('fos_user.user_manager');

    	$user = $userManager->createUser();
    	$user->setUsername($username);
    	$user->setEmail($email);
    	$user->setPassword($password);

    	$userManager->updateUser($user);
    	$this->getDoctrine()->getManager()->flush();

    	return new JsonResponse(array('success' => true, 'id' => $user->getId()));
    }

}