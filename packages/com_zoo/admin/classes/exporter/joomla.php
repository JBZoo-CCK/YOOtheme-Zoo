<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * Exporter for Joomla 2.5/3.0 articles and categories
 *
 * @package Component.Classes.Exporters
 */
class AppExporterJoomla extends AppExporter {

	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->_name = 'Joomla';
	}

	/**
	 * If this exporter can be used
	 *
	 * @return boolean If this exporter can be used
	 *
	 * @since 2.0
	 */
	public function isEnabled() {
		return $this->app->joomla->version->isCompatible('3.0');
	}

	/**
	 * Perform the actual export of categories and articles
	 *
	 * @return string The exported data in JSON format
	 *
	 * @since 2.0
	 */
	public function export() {

		$categories = $this->app->database->queryObjectList('SELECT * FROM #__categories WHERE published != -2 ORDER BY lft ASC', 'id');

		$category_aliases = array();
		$ordered_categories = array();
		foreach ($categories as $category) {
			$ordered_categories[$category->parent_id][] = $category->id;
		}

	    foreach ($categories as $category) {

			if ($category->alias != 'root' && $category->extension != 'com_content') {
				continue;
			}

			if ($category->alias == 'root') {
				$category->title  = 'Root';
				$category->alias = '_root';
			}
			$i = 2;
			$new_alias = $category->alias;
			while (in_array($new_alias, $category_aliases)) {
				$new_alias = $category->alias . '-' . $i++;
			}
			$category_aliases[] = $category->alias = $new_alias;

			// store category parent
			if (isset($categories[$category->parent_id])) {
				$category->parent = $categories[$category->parent_id]->alias;
			}

			if (isset($ordered_categories[$category->parent_id]) && is_array($ordered_categories[$category->parent_id])) {
				$category->ordering = array_search($category->id, $ordered_categories[$category->parent_id]);
			}

			$params = $this->app->parameter->create($category->params);

	    	$data = array();
			foreach ($this->category_attributes as $attribute) {
				if (isset($category->$attribute)) {
					$data[$attribute] = $category->$attribute;
				}
			}
			if ($params->get('image')) {
				$data['content']['image'] = $params->get('image');
			}
			$this->_addCategory($category->title, $category->alias, $data);

			$query = "SELECT * FROM #__content WHERE catid =" . $category->id;
			$articles = $this->app->database->queryObjectList($query);

			foreach ($articles as $article) {
				if ($article->state != -2) {
					$this->_addJoomlaItem($article, $category->alias, JText::_('Joomla article'));
				}
			}
	    }

		$query = "SELECT * FROM #__content WHERE catid = 0";
		$articles = $this->app->database->queryObjectList($query);

		foreach ($articles as $article) {
			if ($article->state != -2) {
				$this->_addJoomlaItem($article, 0, JText::_('Joomla article'));
			}
		}

		return parent::export();

	}

	/**
	 * Add an item to the export list
	 *
	 * @param object $article The article to export
	 * @param string $parent Where the article should be inserted into
	 * @param string $group The group in which it should be put into
	 *
	 * @return AppExporterJoomla $this For chaining support
	 *
	 * @since 2.0
	 */
	protected function _addJoomlaItem($article, $parent, $group = 'default') {

		if ($article->state > 1) {
			$article->state = 0;
		}

		$data = array();
		foreach ($this->item_attributes as $attribute) {
			if (isset($article->$attribute)) {
				$data[$attribute] = $article->$attribute;
			}
		}

		$metadata = $this->app->parameter->create($article->metadata);
		$data['metadata'] = array('description' => $article->metadesc, 'keywords' => $article->metakey, 'robots' => $metadata->get('robots'), 'author' => $metadata->get('author'));

		$data['author'] = ($user = $this->app->user->get($article->created_by)) ? $user->username : $this->app->user->get()->username;

		if ($article->featured) {
			$data['categories'][] = '_root';
		}
		$data['categories'][] = $parent;

		$data['elements'][0]['type'] = 'textarea';
		$data['elements'][0]['name'] = 'Article';
		$data['elements'][0]['data'] = array(array('value' => $article->introtext), array('value' => $article->fulltext));

		$images = @json_decode($article->images, true) ?: array();

		$data['elements'][1]['type'] = 'image';
		$data['elements'][1]['name'] = 'Intro Image';
		$data['elements'][1]['data'] = array('file' => @$images['image_intro'], 'title' => @$images['image_intro_caption']);

		$data['elements'][2]['type'] = 'image';
		$data['elements'][2]['name'] = 'Fulltext Image';
		$data['elements'][2]['data'] = array('file' => @$images['image_fulltext'], 'title' => @$images['image_fulltext_caption']);

		$urls = @json_decode($article->urls, true) ?: array();

		$data['elements'][3]['type'] = 'link';
		$data['elements'][3]['name'] = 'Link A';
		$data['elements'][3]['data'] = array(array('value' => @$urls['urla'] ?: '', 'text' => @$urls['urlatext']));

		$data['elements'][4]['type'] = 'link';
		$data['elements'][4]['name'] = 'Link B';
		$data['elements'][4]['data'] = array(array('value' => @$urls['urlb'] ?: '', 'text' => @$urls['urlbtext']));

		$data['elements'][5]['type'] = 'link';
		$data['elements'][5]['name'] = 'Link C';
		$data['elements'][5]['data'] = array(array('value' => @$urls['urlc'] ?: '', 'text' => @$urls['urlctext']));

		parent::_addItem($article->title, $article->alias, $group, $data);
	}

}
