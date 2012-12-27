<?php

namespace FM\Bundle\SlowShowBundle\Job;

class Job
{
	protected $em;
	
	public function __construct($em)
	{
		$this->em = $em;
	}
		
	public function perform($arguments)
	{
		
	}
}
