<?php

namespace FM\SymSlateBundle\Twig;

class FMExtension extends \Twig_Extension
{
	public function __construct()
	{
		$this->last_color_shade = "rgba(255,255,255,0)";
	}

	public function getFunctions()
	{
		return array(
			'color_shade' => new \Twig_Function_Method($this, 'colorShade'),
			'last_color_shade' => new \Twig_Function_Method($this, 'lastColorShade')
		);
	}

	public function getFilters()
	{
		return array(
			'dbkslsh' => new \Twig_Filter_Method($this, 'dbkslsh'),
			'ago' => new \Twig_Filter_Method($this, 'ago')
		);
	}

	public function dbkslsh($str)
	{
		return preg_replace("/\\\\+('|\")/",'$1',$str);
	}

	//THX: http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
	public function html2rgb($color)
	{
	    if ($color[0] == '#')
	        $color = substr($color, 1);

	    if (strlen($color) == 6)
	        list($r, $g, $b) = array($color[0].$color[1],
	                                 $color[2].$color[3],
	                                 $color[4].$color[5]);
	    elseif (strlen($color) == 3)
	        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
	    else
	        return false;

	    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

	    return array($r, $g, $b);
	}

	public function colorShade($from, $to, $intensity, $alpha=1.0)
	{
		$from = $this->html2rgb($from);
		$to   = $this->html2rgb($to);

		$factor = floatval($intensity);

		$mix = array();

		for($i=0; $i < 3; $i+=1)
		{
			$mix[] = intval($from[$i] + $factor * ($to[$i] - $from[$i]));
		}

		$this->last_color_shade = 'rgba(' . implode(', ', $mix) . ", $alpha)";
		return $this->last_color_shade;
	}

	public function lastColorShade()
	{
		return $this->last_color_shade;
	}

	public function getName()
	{
		return 'fm_extension';
	}

	public function ago($date)
	{
		$time  = time() - $date->getTimestamp();

		$text  = '';

		$tokens = array(
	        31536000 => 'y',
	        2592000 => 'm',
	        604800 => 'w',
	        86400 => 'd',
	        3600 => 'h',
	        60 => 'm',
	        1 => 's'
	    );

		$parts = array();

	    foreach ($tokens as $n => $unit)
	    {
	        if ($time < $n) continue;
	        $numberOfUnits = floor($time / $n);
	        $time -= $numberOfUnits * $n;
	        $parts[] = $numberOfUnits.' '.$unit;
	    }

	   	$last = array_pop($parts);
	    $text = implode(', ', $parts);

	    return $text;
	}

}