<?php

namespace FM\Bundle\SlowShowBundle\Manager;

class Manager
{
	protected $em;
	protected $security_context;
	protected $max_concurrent_jobs;
	
	public function __construct($em, $security_context, $logger, $max_concurrent_jobs = 1)
	{
		$this->em = $em;
		$this->security_context = $security_context;
		$this->logger = $logger;
		$this->max_concurrent_jobs = $max_concurrent_jobs;
	}
	
	public function runNow($job, $arguments)
	{
		$instance = $this->initWorker($job, null);
		return $instance->run($arguments);
	}

	public function enqueueJob($job,$arguments=array(), $later=true)
	{
		$task = new \FM\Bundle\SlowShowBundle\Entity\Task();
		$task->setUserId($this->security_context->getToken()->getUser()->getId());
		$task->setService($job);
		$task->setArguments(json_encode($arguments));
		$task->setStarted(false);
		$task->setCompleted(false);
		$task->setFailed(false);
		$this->em->persist($task);
		$this->em->flush();

		return $this->processNextJob($later, $task->getId());
	}
	
	public function processNextJob($later=true, $id=null)
	{
		//procrastinate
		if($later)
		{
			//echo "Registered shutdown function at: " . date("d/m/Y H:i:s") . "<BR/>\n";
			register_shutdown_function(array($this, 'doProcessNextJob'), true);
			return false;
		}
		else //process now, intended for debugging
		{
			return $this->doProcessNextJob($id);
		}
		
	}
	
	public function initWorker($class, $job_id)
	{
		return new $class($this->em, $this->security_context, $this->logger, $job_id);
	}

	public function doProcessNextJob($called_after_shutdown, $id=null)
	{
		$this->logger->info("Trying to process a job...");
		if($called_after_shutdown)
		{
			
		}

		//echo "\nStarted shutdown function at: " . date("d/m/Y H:i:s") . "<BR/>\n";
		//TODO: warning: table will probably stay locked if something bad happens during the next part!!!
						
		//Lock the table until we are certain we have marked our job as started
		$this->em->getConnection()->exec('LOCK TABLES Task as t0 WRITE, Task as t0_ WRITE;'); //t0 and t0_ are the aliases Doctrine is gonna use.
		
		$tasks_remaining = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = false AND t.completed = false and t.failed = false')->getSingleScalarResult();
		$tasks_running   = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = true  AND t.completed = false and t.failed = false')->getSingleScalarResult();
		
		if($tasks_running < $this->max_concurrent_jobs and $tasks_remaining > 0)
		{
			$conditions = array('started' => false, 'completed' => false, 'failed' => false);
			if($id)
			{
				$conditions['id'] = $id;
			}
			$job = $this->em->getRepository('FMSlowShowBundle:Task')->findOneBy($conditions);
			
			$job_id = $job->getId();
			$job->setStarted(true);		
			$this->em->persist($job);
			$this->em->flush();
			//the job is safely marked as started, unlock the table
			$this->em->getConnection()->exec('UNLOCK TABLES;');
			
			try
			{
				$class  = $job->getService();
				$this->logger->info("Started job $class!");
				$worker = $this->initWorker($class, $job_id);
				//echo "Started job at: " . date("d/m/Y H:i:s") . "<BR/>\n";
				$worker->run(json_decode($job->getArguments(),true));
				$this->logger->info("Finished job $class!");
				//echo "Finished job at: " . date("d/m/Y H:i:s") . "<BR/>\n";
			}
			catch(\Exception $e)
			{
				$job = $this->em->getRepository('FMSlowShowBundle:Task')->find($job_id);
				$job->setFailed(true);
				$this->em->persist($job);
				$this->em->flush();
				throw $e;
			}
			
			$job = $this->em->getRepository('FMSlowShowBundle:Task')->find($job_id);
			$job->setCompleted(true);
			$job->setProgress(100);
			$this->em->persist($job);
			$this->em->flush();
			
			$tasks_remaining = $this->em->createQuery('SELECT COUNT(t.id) FROM FMSlowShowBundle:Task t WHERE t.started = false AND t.completed = false and t.failed = false')->getSingleScalarResult();
			
			//run again if needed
			if($tasks_remaining > 0)
			{
				$this->processNextJob();
			}

			return true;
		}
		else
		{
			$this->logger->info("No slot available.");
			$this->em->getConnection()->exec('UNLOCK TABLES;');
			return false;
		}
	}

}
