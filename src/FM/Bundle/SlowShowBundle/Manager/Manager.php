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
		//$this->em->getConnection()->exec('LOCK TABLES Task WRITE;'); //make sure we don't go over the job limit by harassing the server
		if($this->em->createQuery("SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = true AND t.completed = false")->getSingleScalarResult() < $this->max_concurrent_jobs)
		{
			if($job = $this->em->getRepository('FMSlowShowBundle:Task')->findOneBy(array('started' => false, 'completed' => false)))
			{
				
				$job->setStarted(true);
				$this->em->persist($job);
				$this->em->flush();
				//$this->em->getConnection()->exec('UNLOCK TABLES;');
				
				$class  = $job->getClass();
				$worker = new $class($this->em);
				$worker->perform(json_decode($job->getArguments(),true));
				
				$job->setCompleted(true);
				$this->em->persist($job);
				$this->em->flush();
				
			}
			//else $this->em->getConnection()->exec('UNLOCK TABLES;');
		}
		//else $this->em->getConnection()->exec('UNLOCK TABLES;');
	}
	
}
