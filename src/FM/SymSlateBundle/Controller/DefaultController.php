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
    	$args = array();

    	if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
    	{
    		$latest_submission_on = $this->getDoctrine()->getManager()->createQuery("SELECT MAX(h.created) FROM FMSymSlateBundle:History h")->getSingleScalarResult();
    		$time  = time() - strtotime($latest_submission_on);

    		$text  = '';

    		$tokens = array(
		        31536000 => 'year',
		        2592000 => 'month',
		        604800 => 'week',
		        86400 => 'day',
		        3600 => 'hour',
		        60 => 'minute',
		        1 => 'second'
		    );

    		$parts = array();

		    foreach ($tokens as $n => $unit)
		    {
		        if ($time < $n) continue;
		        $numberOfUnits = floor($time / $n);
		        $time -= $numberOfUnits * $n;
		        $parts[] = $numberOfUnits.' '.$unit.(($numberOfUnits>1)?'s':'');
		    }

		    $parts[count($parts) - 1] = 'and '.end($parts);
		    $text = implode(', ', $parts);
		    
		    $args['latest_submission_date'] = $text;


		    $q = $this->getDoctrine()->getManager()->createQuery(
		    "	SELECT h.created, l.name as language, u.username, m.text as message, t.text as translation  
		     	FROM FMSymSlateBundle:History h
		     	INNER JOIN h.language l
		     	INNER JOIN h.message m
		     	INNER JOIN h.translation t
		     	INNER JOIN h.user u
		     	ORDER BY h.id DESC
		    ");

		    $q->setMaxResults(100);
		    
		    $args['latest'] = $q->getResult();

    	}

    	$em    = $this->getDoctrine()->getManager();

    	$em->transactional(function($em){
    		$cache_date = $em->createQuery('SELECT MAX(tc.date_updated) FROM FMSymSlateBundle:TopContrib tc')->getSingleScalarResult();

	    	$diff = date_diff(new \DateTime('now'), new \DateTime($cache_date));

	    	if($cache_date == null || $diff->d > 0 || $diff->m > 0 || $diff->y > 0)
	    	{
    			$em->createQuery("DELETE FROM FMSymSlateBundle:TopContrib tc")->execute();

	    		foreach($em->getRepository('FMSymSlateBundle:User')->findAll(15) as $user)
	    		{
	    			$tc = new \FM\SymSlateBundle\Entity\TopContrib();
	    			$tc->setUser($user);
	    			$tc->setDateUpdated(new \DateTime('now'));
	    			$tc->setTranslated($user->getTranslationsCount());
	    			$em->persist($tc);
	    		}
	    	}

    	});

    	

    	$args['topusers'] = $em->getRepository('FMSymSlateBundle:TopContrib')->findBy(array(), array('translated' => 'desc'));

        return $args;
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
