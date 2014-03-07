<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The cache helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class CacheHelper extends AppHelper {

	/**
	 * Creates an AppCache instance
	 *
	 * @param string $file Path to cache file
	 * @param boolean $hash Wether the key should be hashed
	 * @param int $lifetime The values lifetime
	 *
	 * @return AppCache
	 *
	 * @since 2.0
	 */
	public function create($file, $hash = true, $lifetime = null, $type = 'file') {

		if ($type == 'apc' && extension_loaded('apc') && class_exists('APCIterator')) {
			$cache = $this->app->object->create('AppApcCache', array(md5($file), $lifetime));
		} else {
			$cache = $this->app->object->create('AppCache', array($file, $hash, $lifetime));
			$this->app->zoo->putIndexFile(dirname($file));
		}
		return $cache;
	}

}

/**
 * The cache class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class AppCache {

	/**
	 * Path to cache file
	 *
	 * @var string
	 * @since 2.0
	 */
	protected $_file = 'config.txt';

	/**
	 * Path to cache file
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_items = array();

	/**
	 * marks cache dirty
	 *
	 * @var boolean
	 * @since 2.0
	 */
	protected $_dirty = false;

	/**
	 * The cached items
	 *
	 * @var boolean
	 * @since 2.0
	 */
	protected $_hash = true;

	/**
	 * Class constructor
	 *
	 * @param string $file Path to cache file
	 * @param boolean $hash Wether the key should be hashed
	 * @param int $lifetime The values lifetime
	 * @since 2.0
	 */
	public function __construct($file, $hash = true, $lifetime = null) {

		// if cache file doesn't exist, create it
		if (!JFile::exists($file)) {
			JFolder::create(dirname($file));
			$buffer = '';
			JFile::write($file, $buffer);
		}

		// set file and parse it
		$this->_file = $file;
		$this->_hash = $hash;
		$this->_parse();

		// clear out of date values
		if ($lifetime) {
			$lifetime = (int) $lifetime;
			$remove = array();
			foreach ($this->_items as $key => $value) {
				if ((time() - $value['timestamp']) > $lifetime) {
					$remove[] = $key;
				}
			}
			foreach ($remove as $key) {
				unset($this->_items[$key]);
			}
		}
	}

	/**
	 * Check if the cache file is writable and readable
	 *
	 * @return boolean If the cache can be used
	 *
	 * @since 2.0
	 */
	public function check() {
		return is_readable($this->_file) && is_writable($this->_file);
	}

	/**
	 * Get a cache content
	 *
	 * @param  string $key The key
	 *
	 * @return mixed      The cache content
	 *
	 * @since 2.0
	 */
	public function get($key) {
		if ($this->_hash)
			$key = md5($key);
		if (!array_key_exists($key, $this->_items))
			return null;

		return $this->_items[$key]['value'];
	}

	/**
	 * Set a cache content
	 *
	 * @param string $key   The key
	 * @param mixed $value The value
	 *
	 * @return AppCache $this for chaining support
	 *
	 * @since 2.0
	 */
	public function set($key, $value) {
		if ($this->_hash)
			$key = md5($key);
		if (array_key_exists($key, $this->_items) && @$this->_items[$key]['value'] == $value)
			return $this;

		$this->_items[$key]['value'] = $value;
		$this->_items[$key]['timestamp'] = time();
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Parse the cache file
	 *
	 * @return AppCache $this for chaining support
	 *
	 * @since 2.0
	 */
	protected function _parse() {
		$content = file_get_contents($this->_file);
		if (!empty($content)) {
			$items = json_decode($content, true);
			if (is_array($items)) {
				$this->_items = $items;
			}
		}
		return $this;
	}

	/**
	 * Save the cache file if it was changed
	 *
	 * @return AppCache $this for chaining support
	 *
	 * @since 2.0
	 */
	public function save() {
		if ($this->_dirty) {
			$data = json_encode($this->_items);
			JFile::write($this->_file, $data);
		}
		return $this;
	}

	/**
	 * Clear the cache file
	 *
	 * @return AppCache $this for chaining support
	 */
	public function clear() {
		$this->_items = array();
		$this->_dirty = true;
		return $this;
	}

}

/**
 * Cache class with alternative PHP cache (APC) storage.
 *
 * @author
 */
class AppApcCache {

    /**
     * @var string $_prefix
     */
    protected $_prefix;

	protected $_lifetime = false;

    /**
     * Constructor.
     *
     * @param string $prefix The prefix for cache index keys.
     */
    public function __construct($prefix = null, $lifetime = null) {
        $this->_prefix = $prefix === null ? md5(__FILE__) : $prefix;
		$this->_lifetime = $lifetime;
    }

    public function get($id) {

        if ($data = apc_fetch(sprintf('%s-%s', $this->_prefix, $id))) {
            if ($entry = @unserialize($data) and is_array($entry)) {
				if ($this->_lifetime && (time() - $entry['time']) > $this->_lifetime) {
					return null;
				}
				return $entry['data'];
            }
        }

        return null;
    }

    public function set($id, $data) {
        apc_store(sprintf('%s-%s', $this->_prefix, $id), serialize(array('data' => $data, 'time' => time())));
		return $this;
    }

    public function clear() {
        $cache = new APCIterator('user', '/^'.preg_quote($this->_prefix, '/').'-/');

        foreach ($cache as $entry) {
			apc_delete($entry['key']);
        }

        return $this;
    }

	public function check() {
		return true;
	}

	public function save() {
		return $this;
	}

}

/**
 * AppCacheException identifies an Exception in the AppCache class
 * @see AppCache
 */
class AppCacheException extends AppException {}