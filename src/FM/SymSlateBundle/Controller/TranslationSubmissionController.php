<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * TranslationSubmission controller.
 *
 * @Route("/submissions")
 */
class TranslationSubmissionController extends Controller
{
    /**
     * Creates a new TranslationSubmission entity.
     *
     * @Route("/create", name="translationsubmission_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {	
		$request = Request::createFromGlobals();
		
		$user = $this->get("security.context")->getToken()->getUser();
		
		$em = $this->getDoctrine()->getManager();
		
        $classification_id = $request->request->get('classification_id');
        $text              = $request->request->get('text');
        $language_id       = $request->request->get('language_id');
        $reason            = $request->request->get('reason');

        $language          = $em->getRepository('FMSymSlateBundle:Language')->findOneById($language_id);
        $classification    = $em->getRepository('FMSymSlateBundle:Classification')->findOneById($classification_id);

        $data = $this->get('translation_submitter')->submit(array(
            'user' => $user,
            'classification' => $classification,
            'language' => $language,
            'translation_text' => $text,
            'overwrite_current' => true,
            'reason'    => $reason
        ));
		
    	$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
    }
}
