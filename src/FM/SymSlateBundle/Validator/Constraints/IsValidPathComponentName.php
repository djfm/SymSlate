<?php

namespace FM\SymSlateBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/

class IsValidPathComponentName extends Constraint
{
	public $message = 'The string "%string%" is not a valid path name component!';
}