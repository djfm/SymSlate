<?php

namespace FM\SymSlateBundle\TranslationValidator;

class TranslationValidator
{
	public function validate($message, $translation, $language, $category)
	{

		if($category == 'Tabs' && strlen($translation) >= 32)
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

		return array('success' => true);
	}
};