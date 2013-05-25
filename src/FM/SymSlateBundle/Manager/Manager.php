<?php

namespace FM\SymSlateBundle\Manager;

class Manager extends \FM\Bundle\SlowShowBundle\Manager\Manager
{
	protected $validator;
	protected $submitter;

	public function __construct($em, $security_context, $logger, $validator, $submitter, $max_concurrent_jobs = 1)
	{
		parent::__construct($em, $security_context, $logger, $max_concurrent_jobs);
		$this->validator = $validator;
		$this->submitter = $submitter;
	}

	public function initWorker($class, $job_id)
	{
		//throw new \Exception("InitWorker! ($class, $job_id) " . get_parent_class($class));
		return new $class($this->em, $this->security_context, $this->logger, $this->validator, $this->submitter, $job_id);
	}

}