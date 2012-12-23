<?php

namespace FM\Bundle\SlowShowBundle\Manager;

class Manager
{
	private $em;
	private $security_context;
	private $max_concurrent_jobs;
	
	public function __construct($em, $security_context, $max_concurrent_jobs = 1)
	{
		$this->em = $em;
		$this->security_context = $security_context;
		$this->max_concurrent_jobs = $max_concurrent_jobs;
	}
	
	public function enqueueJob($job,$arguments)
	{
		$task = new \FM\Bundle\SlowShowBundle\Entity\Task();
		$task->setUserId($this->security_context->getToken()->getUser()->getId());
		$task->setClass(str_replace(":",'\\',$job));
		$task->setArguments(json_encode($arguments));
		$task->setStarted(false);
		$task->setCompleted(false);
		$task->setFailed(false);
		$this->em->persist($task);
		$this->em->flush();
	}
	
	public function processNextJob()
	{
		if($job = $this->em->getRepository('FMSlowShowBundle:Task')->findOneBy(array('started' => false, 'completed' => false)))
		{
			$job->setStarted(true);
			$this->em->persist($job);
			$this->em->flush();
			
			$class  = $job->getClass();
			$worker = new $class($this->em);
			$worker->perform(json_decode($job->getArguments(),true));
			
			$job->setCompleted(true);
			$this->em->persist($job);
			$this->em->flush();
			
		}
	}
	
}
