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
	
	public function processNextJob($later=true)
	{
		//$this->doProcessNextJob();
		//procrastinate
		if($later)
		{
			register_shutdown_function(array($this, 'doProcessNextJob'));
		}
		else
		{
			$this->doProcessNextJob();
		}
		
	}
	
	private function doProcessNextJob()
	{
		//TODO: warning: table will probably stay locked if something bad happens during the next part!!!
						
		//Lock the table until we are certain we have marked our job as started
		$this->em->getConnection()->exec('LOCK TABLES Task as t0 WRITE, Task as t0_ WRITE;'); //t0 and t0_ are the aliases Doctrine is gonna use.
		
		$tasks_remaining = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = false AND t.completed = false and t.failed = false')->getSingleScalarResult();
		$tasks_running   = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = true  AND t.completed = false and t.failed = false')->getSingleScalarResult();
		
		if($tasks_running < $this->max_concurrent_jobs and $tasks_remaining > 0)
		{
			$job = $this->em->getRepository('FMSlowShowBundle:Task')->findOneBy(array('started' => false, 'completed' => false, 'failed' => false));
			
			$job_id = $job->getId();
			$job->setStarted(true);			
			$this->em->persist($job);
			$this->em->flush();
			//the job is safely marked as started, unlock the table
			$this->em->getConnection()->exec('UNLOCK TABLES;');
			
			$class  = $job->getClass();
			$worker = new $class($this->em);
			$worker->perform(json_decode($job->getArguments(),true));
			
			$job = $this->em->getRepository('FMSlowShowBundle:Task')->find($job_id);
			$job->setCompleted(true);
			
			$this->em->persist($job);
			$this->em->flush();
			
			$tasks_remaining = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = false AND t.completed = false and t.failed = false')->getSingleScalarResult();
			
			//run again if needed
			if($tasks_remaining > 0)
			{
				$this->processNextJob();
			}
		}
		else
		{
			$this->em->getConnection()->exec('UNLOCK TABLES;');
		}
	}

}
