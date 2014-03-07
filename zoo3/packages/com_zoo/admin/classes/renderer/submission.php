<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Render submissions for an item
 *
 * @package Component.Classes.Renderers
 */
class SubmissionRenderer extends PositionRenderer {

	/**
	 * The item to render the submission for
	 *
	 * @var Item The item
	 * @since 2.0
	 */
	protected $_item;

	/**
	 * The submission object to render
	 *
	 * @var Submission The submission to render
	 * @since 2.0
	 */
	protected $_submission;

	/**
	 * Render the submission using a layout
	 *
	 * @param string $layout The layout to use
	 * @param array $args The list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 *
	 * @since 2.0
	 */
	public function render($layout, $args = array()) {

        // init vars
		$this->_item = isset($args['item']) ? $args['item'] : null;
		$this->_submission = isset($args['submission']) ? $args['submission'] : null;

		return parent::render($layout, $args);

	}

	/**
	 * Check if a position generates output
	 *
	 * @param string $position The position to check
	 *
	 * @return boolean If the position generates output
	 *
	 * @since 2.0
	 */
	public function checkPosition($position) {

		foreach ($this->_getConfigPosition($position) as $index => $data) {
            if ($element = $this->_item->getElement($data['element'])) {

                $data['_layout'] = $this->_layout;
                $data['_position'] = $position;
                $data['_index'] = $index;

                if ($element->canAccess()) {

					// trigger elements beforesubmissiondisplay event
					$render = true;
					$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforesubmissiondisplay', array('render' => &$render, 'element' => $element, 'params' => $data)));

					if ($render) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Check if the submission position generates output
	 *
	 * @param string $position The position to check
	 *
	 * @return boolean If the position generates output
	 *
	 * @deprecated 2.5 Use SubmissionRenderer::checkPosition() instead
	 *
	 * @since 2.0
	 */
	public function checkSubmissionPosition($position) {
		return $this->checkPosition($position);
	}

	/**
	 * Render the output of a position
	 *
	 * @param string $position The position to render
	 * @param array $args A list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 *
	 * @since 2.0
	 */
	public function renderPosition($position, $args = array()) {

		// init vars
		$elements = array();
		$output   = array();
        $trusted_mode = !$this->app->user->get()->guest && $this->_submission->isInTrustedMode();
		$show_tooltip = $this->_submission->showTooltip();

		// get style
		$style = isset($args['style']) ? $args['style'] : 'submission.block';

		// store layout
		$layout = $this->_layout;

		// render elements
        foreach ($this->_getConfigPosition($position) as $index => $data) {
            if (($element = $this->_item->getElement($data['element']))) {

				if (!$element->canAccess()) {
					continue;
				}

				$data['_layout'] = $this->_layout;
                $data['_position'] = $position;
                $data['_index'] = $index;

                // set params
                $params = array_merge((array) $data, $args);

                // check value
                $elements[] = compact('element', 'params');
            }
        }

        foreach ($elements as $i => $data) {
            $params = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements)-1)), compact('trusted_mode', 'show_tooltip'), $data['params']);

			// trigger elements beforesubmissiondisplay event
			$render = true;
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforesubmissiondisplay', array('render' => &$render, 'element' => $data['element'], 'params' => $params)));

			if ($render) {
				$output[$i] = parent::render("element.$style", array('element' => $data['element'], 'params' => $params));

				// trigger elements aftersubmissiondisplay event
				$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:aftersubmissiondisplay', array('html' => &$output[$i], 'element' => $data['element'], 'params' => $params)));
			}

        }

		// restore layout
		$this->_layout = $layout;

		return implode("\n", $output);
	}

	/**
	 * Render the output of a position
	 *
	 * @param string $position The position to render
	 * @param array $args A list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 *
	 * @deprecated 2.5 Use SubmissionRenderer::renderPosition() instead
	 *
	 * @since 2.0
	 */
	public function renderSubmissionPosition($position, $args = array()) {
		return $this->renderPosition($position, $args);
	}

	/**
	 * Get the configuration for this position
	 *
	 * @param string $position The name of the position
	 *
	 * @return JSONData The configuration object
	 *
	 * @since 2.0
	 */
    protected function _getConfigPosition($position) {
		$config	= $this->getConfig('item')->get($this->_item->getApplication()->getGroup().'.'.$this->_item->getType()->id.'.'.$this->_layout);
        return $config && isset($config[$position]) ? $config[$position] : array();
    }

}