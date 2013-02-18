<?php

namespace FM\SymSlateBundle\TranslationValidator;

class TranslationValidator
{
	protected $em;

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function normalize($str)
	{
		$nz = strtolower($str);
		$nz = preg_replace('/[:\.;!-]+/',' ',$str);
		$nz = preg_replace('/\s+/',' ',$str);
		return $nz;
	}

	public function validate($message, $translation, $language, $category)
	{

		if($category == 'Tabs' && strlen($translation) > 32)
		{
			return array('success' => false, 'error_message' => 'Tab translation cannot exceed 32 chars!');
		}

		$matches = array();
		$counts  = array();
		preg_match_all('/%(?:\d+\$)?[dfsu]/', $message, $matches);
		foreach($matches[0] as $match)
		{
			if(isset($counts[$match]))$counts[$match] += 1;
			else $counts[$match] = 1;
		}

		foreach($counts as $str => $n)
		{
			if(substr_count($translation, $str) != $n)
			{
				return array('success' => false, 'error_message' => "The special string $str needs to be present $n time(s) in the translation!");
			}
		}

		if(strpos($translation, "\n") and !strpos($message, "\n"))
		{
			return array('success' => false, 'error_message' => "Translation cannot contain line breaks! (because original text has none).");
		}

		if($this->normalize($message) == $this->normalize($translation) and $language->getCode() != 'en')
		{
			return array('success' => true, 'warning_message' => 'Translation looks a lot like the original English!');
		}

		$punctuation = ".:!?";
		$lm = $message[strlen($message) - 1];
		$lt = $translation[strlen($translation) - 1];
		if((strpos($punctuation, $lm) !== false or strpos($punctuation, $lt) !== false) and $lm != $lt)
		{
			return array('success' => true, 'warning_message' => 'The punctuation mark at the end of the message is different of that at the end of the translation!');
		}

		$fm = mb_substr($message     , 0,  1 , "UTF-8");
		$ft = mb_substr($translation , 0,  1 , "UTF-8");

		if( (strtolower($fm) == $fm) != (strtolower($ft) == $ft) and $language->getCode() != 'zh' and $language->getCode() != 'tw')
		{
			return array('success' => true, 'warning_message' => 'The message and the translation start with a letter of different case!');
		}

		return array('success' => true);
	}
};