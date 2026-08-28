<?php

namespace App\Exceptions;

use App\Constants\Code;
use Exception;

class CodeException extends Exception
{
	protected $code;
	protected $error_data;

	public function __construct($code, $error_data = '')
	{
		$this->code = $code;
		$this->error_data = $error_data;
		$this->message = $error_data;
	}
	
	public function render()
	{
		return codeRender($this->code,'',$this->error_data);
	}

	public function message()
    {
        return $this->error_data;
    }
}