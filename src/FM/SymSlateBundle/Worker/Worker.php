<?php

namespace FM\SymSlateBundle\Worker;

class Worker extends \FM\Bundle\SlowShowBundle\Worker\Worker
{
	protected $validator;
	protected $submitter;

	public function __construct($em, $security_context, $logger, $validator, $submitter, $job_id)
	{
		parent::__construct($em, $security_context, $logger, $job_id);
		$this->validator = $validator;
		$this->submitter = $submitter;
	}
}