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

	public function submit($args)
	{

		if(!$args['user']->canTranslateInto($args['language']))
		{
			return array('success' => false, 'error_message' => "You are not allowed to translate into this language.");
		}
		
        $validation = $this->validator->validate($args['message']->getText(), $args['translation_text'], $args['language'], $args['classification']->getCategory());
		
		if(!$validation['success'])return $validation;


		$operation = "";

		if($translation = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneBy(array(
			'mkey' => $args['message']->getMkey(),
			'language_id' => $args['language']->getId(),
			'text' => $args['translation_text']
		)))
		{
			$operation = "review";
		}
		else
		{
			$operation   = "creation";
			$translation = new \FM\SymSlateBundle\Entity\Translation();
			$translation->setMkey($args['message']->getMkey());
			$translation->setAuthor($args['user']);
			$translation->setLanguage($args['language']);
			$translation->setText($args['translation_text']);
		}

		if($ct = $this->em->getRepository('FMSymSlateBundle:CurrentTranslation')->findOneBy(array(
			'message_id' => $args['message']->getId(),
			'language_id' => $args['language']->getId()
		)))
		{
			
			$ct->setTranslation($translation);
		}
		else
		{
			$ct = new \FM\SymSlateBundle\Entity\CurrentTranslation();
			$ct->setMessage($args['message']);
			$ct->setLanguage($args['language']);
			$ct->setTranslation($translation);
		}

		$history = new \FM\SymSlateBundle\Entity\History();
		$history->setUser($args['user']);
		$history->setMessage($args['message']);
		$history->setLanguage($args['language']);
		$history->setTranslation($translation);
		$history->setOperation($operation);

		//return array('success' => false, 'error_message' => $history->getMessageId());

		$this->em->persist($translation);
		$this->em->persist($ct);
		$this->em->persist($history);

		$this->em->flush();

		return $validation;
	}
}