<?php

namespace hypeJunction\Categories\Exceptions;

class Exception extends \Exception{

	public function __construct($message, $code, $previous) {
		parent::__construct($message, $code, $previous);
		elgg_log($message, 'ERROR');
	}
}
