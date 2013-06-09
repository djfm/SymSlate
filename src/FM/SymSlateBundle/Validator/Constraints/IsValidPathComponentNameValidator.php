<?php

namespace FM\SymSlateBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* @Annotation
*/

class IsValidPathComponentNameValidator extends ConstraintValidator
{
	public $message = 'The string "%string%" is not a valid path name component!';

	public function isValid($value, Constraint $constraint)
	{
		
		if(!preg_match('/[a-zA-Z0-9 _\.]+/', $value) || (strpos($value, '..') !== false))
		{
			$this->setMessage($constraint->message, array('%string%' => $value));
			return false;
		}

		return true;
	}

}