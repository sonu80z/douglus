<?php
	/**
	 * @author Jesse Chrestler
	 * @name Metadata 
	 * 
	 * Modified: 07-08-2009
	 * 
	 */
	class DynamicObject{
		public $attributes = null;
		public function __construct($attributes = array()){
			$this->attributes = $attributes;
			if (!$attributes)
				return;
			foreach($attributes as $key => $item)
				if (is_array($item))
					$this->attributes[$key] = new DynamicObject($item);
		}
		public function __get($prop_name){
			if (isset($this->attributes[$prop_name]))
				return $this->attributes[$prop_name];
		}
	}
?>