<?php

namespace FM\SymSlateBundle\TranslationSubmitter;

class TranslationSubmitter
{
	protected $em;
	protected $validator;

	public function __construct($em, $validator)
	{
		$this->em = $em;
		$this->validator = $validator;
	}


	/*
	 * Submits a translation
	 *
	 * $args is an array with the following values:
	 *
	 * user              : the User who is submitting the translation 				[required]
	 * language          : the Language of the translation            				[required]
	 * translation_text  : a string representing the translation      				[required]
	 *
	 * classification    : the Classification the translation is intended for 		[optional if mkey specified, should be specified if possible]
	 * mkey              : the key of the message corresponding to the translation  [required unless classification specified]
	 *
	 * overwrite_current : boolean (false if unspecified), determines whether   	[optional]
	 *                     the current translation should be updated in case
	 *                     a message is associated with the translation and
	 *                     the message already has a translation for this
	 *                     language
	 *
	 * translations_import : the TranslationsImport object if the translation came from one [optional]
	 * 
     */
	public function submit($args)
	{
		$translation_text    = isset($args['translation_text']) ? $args['translation_text'] : false;

		$translation = false;
		if(isset($args['translation_id']))
		{
			$translation = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneById($args['translation_id']);
			$translation_text = $translation->getText();
		}

		if(isset($args['user']))
		{
			$user = $args['user'];
		}
		else if(is_object($translation))
		{
			$user = $translation->getAuthor();
		}
		else
		{
			return array('success' => false, 'error_message' => "You must specify a user.");
		}

		$language    = false;
		if(isset($args['language']))
		{
			$language = $args['language'];
		}
		else if(isset($args['language_id']))
		{
			$language = $this->em->getRepository('FMSymSlateBundle:Language')->findOneById($args['language_id']);
		}
		else
		{
			return array('success' => false, 'error_message' => "You must specify a language.");
		}

		/* First, check that the User is allowed to submit the translation */
		if(!$user->canTranslateInto($language))
		{
			return array('success' => false, 'error_message' => "You are not allowed to translate into this language.");
		}
		
		
		/* Prepare the necessary variables */
		$message      = null;
		$message_text = null;
		$category     = null;

		if(isset($args['classification']))
		{
			$message      = $args['classification']->getMessage();
			$message_text = $message->getText();
			$mkey         = $message->getMkey();
			$category     = $args['classification']->getCategory();
		}
		else if(isset($args['mkey']))
		{
			$mkey         = $args['mkey'];
			$message      = $this->em->getRepository('FMSymSlateBundle:Message')->findOneByMkey($mkey);
			if($message)
			{
				$message_text = $message->getText();
			}
		}

		$overwrite_current   = (isset($args['overwrite_current']) && $args['overwrite_current']);
		$translations_import = isset($args['translations_import']) ? $args['translations_import'] : null;

		/* Then validate the translation */
		$validation = $this->validator->validate($message_text, $translation_text, $language, $category);
		
		/* Stop if it does not validate */
		if(!$validation['success'])return $validation;

		/* See if we need to create a translation or if it already exists */
		if(is_object($translation) or ($translation = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneBy(array(
			'mkey' => $mkey,
			'language_id' => $language->getId(),
			'text' => $translation_text
		))))
		{
			/* The translation is already there, submitting it is equivalent to reviewing it */
			$operation = "review";
		}
		else
		{
			$operation   = "creation";
			$translation = new \FM\SymSlateBundle\Entity\Translation();
			$translation->setMkey($mkey);
			$translation->setAuthor($user);
			$translation->setLanguage($language);
			$translation->setText($translation_text);

			if($translations_import)$translation->setTranslationsImport($translations_import);

			$this->em->persist($translation);
		}

		/* Check wheter a CurrentTranslation exists for this message (if we have one) */
		if($message)
		{
			if($ct = $this->em->getRepository('FMSymSlateBundle:CurrentTranslation')->findOneBy(array(
				'message_id'  => $message->getId(),
				'language_id' => $language->getId()
			)))
			{
				/* We have a current translation, only overwrite it if explicitly asked for */
				if($overwrite_current)
				{
					$ct->setTranslation($translation);
					/* Since we changed the current translation, a history change is needed */
					$history_changed = true;
				}
			}
			else
			{
				$ct = new \FM\SymSlateBundle\Entity\CurrentTranslation();
				$ct->setMessage($message);
				$ct->setLanguage($language);
				$ct->setTranslation($translation);
				$history_changed = true;
			}

			if(isset($args['operation']))
			{
				$operation = $args['operation'];
			}

			/* Finally, create the history entry! */
			if($history_changed)
			{
				$history = new \FM\SymSlateBundle\Entity\History();
				$history->setUser($user);
				$history->setMessage($message);
				$history->setLanguage($language);
				$history->setTranslation($translation);
				$history->setOperation($operation);

				$this->em->persist($ct);
				$this->em->persist($history);
			}

		}

		$this->em->flush();

		/* Return the output of the validation (it could have warnings) */
		return $validation;
	}
}