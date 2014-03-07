<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The editor helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class EditorHelper extends AppHelper {

	/**
	 * The asset id retrieved from the assets table (J2.5)
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $_asset_id;

	/**
	 * Display the editor area.
	 *
	 * @param string $name The control name.
	 * @param string $content The contents of the text area.
	 * @param string $width The width of the text area (px or %).
	 * @param string $height The height of the text area (px or %).
	 * @param integer $col The number of columns for the textarea.
	 * @param integer $row The number of rows for the textarea.
	 * @param boolean $buttons True and the editor buttons will be displayed.
	 * @param string $id An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param string $asset The object asset
	 * @param object $author The author.
	 *
	 * @return string The html output
	 *
	 * @since 2.0
	 */
	public function display($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null) {

		if ($asset === null) {
			if (!isset($this->_asset_id)) {
				$this->_asset_id = $this->app->database->queryResult('SELECT id FROM #__assets WHERE name = '.$this->app->database->quote($this->app->component->self->name));
			}
			$asset = $this->_asset_id;
		}

		if ($author === null) {
			$author = $this->app->user->get()->id;
		}

		return $this->app->system->editor->display($name, $content, $width, $height, $col, $row, $buttons, $id, $asset, $author);

	}

}