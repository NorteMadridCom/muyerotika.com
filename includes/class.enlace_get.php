<?php

class Enlace_get
{

	public function enlace($link) 
	{
		$matriz = explode('+', $link);
		return implode('%2B', $matriz);
	}	
	
}


