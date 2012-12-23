<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Template()
 * @Route("/")
 */

class DefaultController extends Controller
{
	
	/**
     *
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        return array();
    }
	
	/**
     *
     * @Route("/countrystats", name="getcountrystats")
     * @Method("POST")
     */
    public function getCountryStatisticsAction(Request $request)
    {
    	$data = array();	
		$request = Request::createFromGlobals();
		
		$map_codes = $request->request->get('map_codes');
		
		$em = $this->getDoctrine()->getManager();
		
		foreach($map_codes as $map_code)
		{
			$stat = $em->getRepository('FMSymSlateBundle:Country')->computeStatistics(array('map_code' => $map_code));
			$data[$map_code] = $stat;
		}
		
		$em->flush();
		
    	$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
        
    }
	
}
