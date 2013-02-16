<?php

namespace FM\SymSlateBundle\Manager;

class Manager extends \FM\Bundle\SlowShowBundle\Manager\Manager
{
	protected $validator;

	public function __construct($em, $security_context, $logger, $validator, $max_concurrent_jobs = 1)
	{
		parent::__construct($em, $security_context, $logger, $max_concurrent_jobs);
		$this->validator = $validator;
	}

	public function initWorker($class, $job_id)
	{
		//throw new \Exception("InitWorker! ($class, $job_id) " . get_parent_class($class));
		return new $class($this->em, $this->security_context, $this->logger, $this->validator, $job_id);
	}

}