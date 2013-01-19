<?php

namespace FM\Bundle\SlowShowBundle\Worker;

class Worker
{
	private $job_id;
	private $expected_steps  = false;
	private $steps_performed = 0;
	private $progress        = 0;

	public function __construct($em, $security_context, $logger, $job_id)
	{
		$this->em = $em;
		$this->security_context = $security_context;
		$this->logger = $logger;
		$this->job_id = $job_id;
	}

	public function setStatus($status)
	{
		$task = $this->em->getRepository('FMSlowShowBundle:Task')->find($this->job_id);
		$task->setStatus($status);
		$this->em->persist($task);
		$this->em->flush();
	}

	public function setExpectedSteps($steps)
	{
		$this->expected_steps = $steps;
	}

	public function step()
	{
		$this->steps_performed += 1;

		if($this->expected_steps !== false)
		{
			$progress = 100 * $this->steps_performed / $this->expected_steps;
			if($progress - $this->progress >= 5)
			{
				$task = $this->em->getRepository('FMSlowShowBundle:Task')->find($this->job_id);
				$task->setProgress($progress);
				$this->progress = $progress;
				$this->em->persist($task);
				$this->em->flush();
			}
		}

	}

}