<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The export helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ExportHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:exporter'), 'exporter');
	}

	/**
	 * Creates an AppExporter instance
	 *
	 * @param string $type Type of AppExporter to create
	 *
	 * @return AppExporter
	 *
	 * @since 2.0
	 */
	public function create($type) {

		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

		// load renderer class
		$class = 'AppExporter'.$type;
		$this->app->loader->register($class, 'exporter:'.strtolower($type).'.php');

		return $this->app->object->create($class);
	}

	/**
	 * Get all exporters
	 *
	 * @param array $ignore Exporters to ignore
	 *
	 * @return array All AppExporters
	 *
	 * @since 2.0
	 */
	public function getExporters($ignore = array()) {
		$ignore = (array) $ignore;
		$exporters = array();
		foreach ($this->app->path->files('exporter:', false, '/\.php$/') as $file) {
			if ($instance = $this->create(basename($file, '.php'))) {
				if (!in_array($instance->getName(), $ignore)) {
					$exporters[] = $instance;
				}
			}
		}
		return $exporters;
	}

	/**
	 * Exports items of a type to a csv file
	 *
	 * @param Type $type The type object
	 *
	 * @return string File path, false if no items were found
	 *
	 * @since 2.0
	 */
	public function toCSV($type) {

		$item_table = $this->app->table->item;
		$type->getApplication()->getCategoryTree();
		$data = array();

		$i = 1;
		$maxima = array();
		foreach ($item_table->getByType($type->id, $type->getApplication()->id) as $item) {

			// item properties
			$data[$i]['Id'] = $item->id;
			$data[$i]['Name'] = $item->name;
			$data[$i]['Alias'] = $item->alias;
			$data[$i]['Author Alias'] = $item->getAuthor();
			$data[$i]['Created Date'] = $item->created;

			// categories
			$data[$i]['Category'] = array();
			foreach ($item->getRelatedCategories() as $category) {
				$name = $category->name.'|||'.$category->alias;
				while (($category = $category->getParent()) && $category->id) {
					$name = $category->name.'|||'.$category->alias."///$name";
				}
				$data[$i]['Category'][] = $name;
			}

			// tags
			$data[$i]['Tag'] = $item->getTags();

			// elements
			foreach ($type->getElements() as $identifier => $element) {
				if (!isset($item->elements[$identifier])) {
					continue;
				}

				$name = $element->config->get('name') ? $element->config->get('name') : $element->getElementType();
				switch ($element->getElementType()) {
					case 'text':
					case 'textarea':
					case 'link':
					case 'email':
					case 'date':
						$data[$i][$name] = array();
						foreach ($item->elements[$identifier] as $self) {
							$data[$i][$name][] = @$self['value'];
						}
						break;
					case 'select':
					case 'radio':
					case 'checkbox':
						$data[$i][$name] = isset($item->elements[$identifier]['option']) ? $item->elements[$identifier]['option'] : array();
						break;
					case 'country':
						$data[$i][$name] = isset($item->elements[$identifier]['country']) ? $item->elements[$identifier]['country'] : array();
						break;
					case 'gallery':
						$data[$i][$name] = @$item->elements[$identifier]['value'];
						break;
					case 'image':
					case 'download':
						$data[$i][$name] = @$item->elements[$identifier]['file'];
						break;
					case 'googlemaps':
						$data[$i][$name] = @$item->elements[$identifier]['location'];
						break;
				}
			}

			foreach ($data[$i] as $key => $value) {
				if (is_array($value)) {
					$maxima[$key] = max(1, @$maxima[$key], count($value));
				}
			}

			$item_table->unsetObject($item->id);

			$i++;
		}

		if (empty($data)) {
			return false;
		}

		// use maxima to pad arrays
		foreach ($maxima as $key => $num) {
			foreach (array_keys($data) as $i) {
				$data[$i][$key] = array_pad($data[$i][$key], $num, '');
			}
		}

		// set header
		array_unshift($data, array());
		foreach ($data[1] as $key => $value) {
			$num = is_array($value) ? count($value) : 1;
			$data[0] = array_merge($data[0], array_fill(0, max(1, $num), $key));
		}

		$file = rtrim($this->app->system->config->get('tmp_path'), '\/')."/$type->id.csv";
		if (($handle = fopen($file, "w")) !== false) {
			foreach ($data as $row) {
				fputcsv($handle, $this->app->data->create($row)->flattenRecursive());
			}
			fclose($handle);
		} else {
			throw new AppExporterException(sprintf('Unable to write to file %s.', $file));
		}

		return $file;
	}

}

/**
 * The AppExporter base class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
abstract class AppExporter {

	/**
	 * App instance
	 *
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * The data created during export
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_data;

	/**
	 * The exporter name
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_name;

	/**
	 * The category attributes that are being exported
	 *
	 * @var array
	 * @since 2.0
	 */
	public $category_attributes = array('parent', 'published', 'description', 'ordering');

	/**
	 * The item attributes that are being exported
	 *
	 * @var array
	 * @since 2.0
	 */
	public $item_attributes = array('searchable', 'state', 'created', 'modified', 'hits', 'author', 'access', 'priority', 'publish_up', 'publish_down');

	/**
	 * The comment attributes that are being exported
	 *
	 * @var array
	 * @since 2.0
	 */
	public $comment_attributes = array('parent_id', 'user_id', 'user_type', 'author', 'email', 'url', 'ip', 'created', 'content', 'state', 'username');

	/**
	 * Class constructor
	 *
	 * @since 2.0
	 */
	public function __construct() {
		$this->app = App::getInstance('zoo');
		$this->_data = $this->app->data->create(array('categories' => array(), 'items' => array()));
	}

	/**
	 * Get exporter name
	 *
	 * @return string name
	 *
	 * @since 2.0
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get exporter type
	 *
	 * @return string type
	 *
	 * @since 2.0
	 */
	public function getType() {
		return strtolower(str_replace('AppExporter', '', get_class($this)));
	}

	/**
	 * Check if exporter is enabled
	 * May be overloaded by the child class.
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function isEnabled() {
		return true;
	}

	/**
	 * Do the export.
	 * Must be overloaded by the child class.
	 *
	 * @return string The export xml
	 *
	 * @since 2.0
	 */
	public function export() {
		return (string) $this->_data;
	}

	/**
	 * Adds a category to the exporters data
	 *
	 * @param string $name Category name
	 * @param string $id Category id
	 * @param array $data Category data
	 *
	 * @return self
	 *
	 * @since 2.0
	 */
	protected function _addCategory($name, $id = '', $data = array()) {

		if (empty($id)) {
			$id = JFilterOutput::stringURLSafe($name);
		}

		while (isset($this->_data['categories'][$id])) {
			$id .= '-2';
		}

		$data['name'] = $name;

		$this->_data['categories'][$id] = $data;

		return $this;
	}

	/**
	 * Adds an item to the exporters data
	 *
	 * @param string $name Item name
	 * @param string $id Item id
	 * @param array $group Item group
	 * @param array $data Item data
	 *
	 * @return self
	 *
	 * @since 2.0
	 */
	protected function _addItem($name, $id = '', $group = 'default', $data = array()) {

		if (empty($id)) {
			$id = JFilterOutput::stringURLSafe($name);
		}

		while (isset($this->_data['items'][$id])) {
			$id .= '-2';
		}

		$data['group'] = $group;
		$data['name'] = $name;

		$this->_data['items'][$id] = $data;

		return $this;
	}

}

/**
 * AppExporterException identifies an Exception in the AppExporter class
 * @see AppExporter
 */
class AppExporterException extends AppException {}