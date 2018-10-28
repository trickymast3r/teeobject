<?php

namespace Tee;

/**
 * Tee Data Object
 *
 * @category   Tee
 * @package    DataObject
 * @author     Tricky <tricky@gvr.vn>
 */
class DataObject implements ArrayAccess {
    /**
     * Static Instance
     *
     */
    protected static $_instance;
    /**
     * Data Store Array
     *
     * @var array
     */
    protected $_data = [];

     /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = [];

    /**
     * Data Object Constructor
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->_data = $data;
    }
    /**
     * Check if the specified data is set. If $key is empty, check object data is set 
     *
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')  {
        if (empty($key) || !is_string($key)) {
            return !empty($this->_data);
        }
        return array_key_exists($key, $this->_data);
    }
     /**
     * Overwrite data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null)    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }
    /**
     * Object data getter
     *
     * @param string    $key
     * @param mixed     $default
     * @return mixed
     */
    public function getData($key = '', $default = null) {
        if ('' === $key) return $this->_data;
        if (isset($this->_data[$key])) return $this->_data[$key];
        return $default;
    }
    /**
     * Unset data from the object.
     *
     * @param null|string|array $key
     * @return $this
     */
    public function unsetData($key = null)  {
        if ($key === null) {
            $this->setData([]);
        } elseif (is_string($key)) {
            if (isset($this->_data[$key]) || array_key_exists($key, $this->_data)) {
                unset($this->_data[$key]);
            }
        } elseif ($key === (array)$key) {
            foreach ($key as $element) {
                unset($this->_data[$element]);
            }
        }
        return $this;
    }
    /**
     * Converts field names for setters and getters
     *
     * @param   string $name
     * @return  string
     */
    protected function _underscore($name)   {
        if (isset(self::$_underscoreCache[$name])) return self::$_underscoreCache[$name];
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }
    
    /**
     * Data Object magic methods
     *
     * @param mixed $method
     * @param mixed $args
     * @return void
     */
    public function __call($method, $args)  {
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->_underscore(substr($method, 3));
                $index = isset($args[0]) ? $args[0] : null;
                return $this->getData($key);
            case 'set':
                $key = $this->_underscore(substr($method, 3));
                $value = isset($args[0]) ? $args[0] : null;
                return $this->setData($key, $value);
            case 'uns':
                $key = $this->_underscore(substr($method, 3));
                return $this->unsetData($key);
            case 'has':
                $key = $this->_underscore(substr($method, 3));
                return $this->hasData($key);
        }
        throw new Exception(sprintf('Invalid method %s::%s', get_class($this), $method));
    }
    /**
     * Checks whether the object is empty
     *
     * @return bool
     */    
    public function isEmpty() {
        return empty($this->_data);
    }
    /**
     * instance
     *
     * @return void
     */
    final public static function instance() {
        if (!isset(self::$_instance)) self::$_instance = new self(func_get_args());
        return self::$_instance;
    }
    /**
     * Implementation of ArrayAccess::offsetSet()
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }
    /**
     * Implementation of ArrayAccess::offsetExists()
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    /**
     * Implementation of ArrayAccess::offsetUnset()
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }
    /**
     * Implementation of ArrayAccess::offsetGet()
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}