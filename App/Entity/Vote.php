<?php
namespace App\Entity;
class Vote {
	
	function __construct(array $data) {
		foreach ($data as $key => $value) {
			if (method_exists($this, 'set' . $key)) {
				$this->{'set' . $key}($value);
			} elseif (property_exists($this, '_' . $key)) {
				$this->{'_' . $key} = $value;
			}
		}
	}
	
	function getTalkID() {
		
	}
	
	function getDirection() {
		
	}
}