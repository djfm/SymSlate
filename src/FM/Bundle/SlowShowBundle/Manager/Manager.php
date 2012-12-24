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
		//$this->doProcessNextJob();
		register_shutdown_function(array($this, 'doProcessNextJob'));
	}
	
	public function doProcessNextJob()
	{
		//the TaskInfo entity has an optimist lock : should prevent us from starting too many parallel tasks!
		if($running = $this->em->getRepository('FMSlowShowBundle:TaskInfo')->findOneByField('running'))
		{
			
		}
		else 
		{
			$running = new \FM\Bundle\SlowShowBundle\Entity\TaskInfo();
			$running->setField('running');
			$running->setValue(0);
			$this->em->persist($running);
			$this->em->flush();	
		}
		
				
		
		if($running->getValue() < $this->max_concurrent_jobs)
		{
			if($job = $this->em->getRepository('FMSlowShowBundle:Task')->findOneBy(array('started' => false, 'completed' => false)))
			{
				$job_id = $job->getId();
				$job->setStarted(true);
				$running->setValue($running->getValue() + 1);
				$this->em->persist($running);	
				$this->em->persist($job);
				$this->em->flush();//this should release the lock
				
				
				//this will take a long time
				$class  = $job->getClass();
				$worker = new $class($this->em);
				$worker->perform(json_decode($job->getArguments(),true));
				
				//reload the job entity from the DB because the em was probably cleared in the mean time
				$job = $this->em->getRepository('FMSlowShowBundle:Task')->find($job_id);
				$job->setCompleted(true);
				//decrement counter
				$running = $this->em->getRepository('FMSlowShowBundle:TaskInfo')->findOneByField('running');
				$running->setValue($running->getValue() - 1);
				$this->em->persist($running);
				$this->em->persist($job);
				$this->em->flush();
				
			}
		}

		//run again if needed
		$this->processNextJob();

	}
	
}
