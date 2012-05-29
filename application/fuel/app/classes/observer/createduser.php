<?php

class Observer_CreatedUser extends \Orm\Observer
{
 
        /**
         * @var  string  property to set the user id on
         */
        public static $property = 'user_id';
 
        protected $_property;
 
        public function __construct($class)
        {
                $props = $class::observers(get_class($this));
                $this->_property         = isset($props['property']) ? $props['property'] : static::$property;
        }
 
        public function before_insert(\Orm\Model $obj)
        {
                list($driver, $id) = Auth::get_user_id();
                $obj->{$this->_property} = $id;
        }
}
