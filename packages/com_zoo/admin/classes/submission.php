<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class that represents a Submission
 *
 * @package Component.Classes
 */
class Submission {

    /**
     * Id of the submission
     * 
     * @var int
     * @since 2.0
     */
	public $id;

    /**
     * Id of the application which the submission belongs to
     * 
     * @var int
     * @since 2.0
     */
	public $application_id;

    /**
     * Name of the submission
     * 
     * @var string
     * @since 2.0
     */
	public $name;

    /**
     * Alias of the submission
     * 
     * @var string
     * @since 2.0
     */
	public $alias;

    /**
     * State of the submission
     * 
     * @var integer
     * @since 2.0
     */
	public $state = 0;

    /**
     * Submission access level
     * 
     * @var int
     * @since 2.0
     */
	public $access;

   	/**
   	 * Submission parameters
   	 * 
   	 * @var ParameterData
   	 * @since 2.0
   	 */
	public $params;

    /**
     * Reference to the global App object
     * 
     * @var App
     * @since 2.0
     */
	public $app;

    /**
     * Related type objects
     * 
     * @var array
     * @since 2.0
     */
	protected $_types = array();

	/**
	 * Class constructor
	 */
	public function  __construct() {

		// decorate data as object
		$this->params = App::getInstance('zoo')->parameter->create($this->params);

	}

	/**
	 * Check if the user can access the submission
	 * 
	 * @param  JUser $user The user object
	 * 
	 * @return boolean       If the user can access the submission
	 *
	 * @since 2.0
	 */
	public function canAccess($user = null) {
		return $this->app->user->canAccess($user, $this->access);
	}

	/**
	 * Get the published state 
	 * 
	 * @return int The published state
	 *
	 * @since 2.0
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * Set the state of the submission
	 * 
	 * @param int $val The state of the submission
	 *
	 * @since 2.0
	 */
	public function setState($val) {
		$this->state = $val;
	}

	/**
	 * Get the submission parameters
	 * 
	 * @return ParameterData The parameters
	 *
	 * @since 2.0
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * Get the list of types for the submission
	 * 
	 * @return array The list of types
	 *
	 * @since 2.0
	 */
    public function getTypes() {
        if (empty($this->_types)) {
            foreach (array_keys($this->params->get('form.', array())) as $type_id) {
				if ($type = $this->getApplication()->getType($type_id)) {
					$this->_types[$type_id] = $type;
				}
            }
        }

        return $this->_types;
    }

	/**
	 * Get a type given its id
	 * 
	 * @param  string $id The identifier of the type
	 * 
	 * @return Type     The type object
	 *
	 * @since 2.0
	 */
	public function getType($id) {
		$types = $this->getTypes();

		if (isset($types[$id])) {
			return $types[$id];
		}

		return null;
	}

	/**
	 * Get the submittable types
	 * 
	 * @return array The list of types
	 *
	 * @since 2.0
	 */
    public function getSubmittableTypes() {
        $types = $this->getTypes();
        $result = array();
        foreach ($types as $type) {
			if ($form = $this->getForm($type->id)) {
				$layout = $form->get('layout');
				if (!empty($layout)) {
					$result[$type->id] = $type;
				}
			}
        }
        return $result;
    }

	/**
	 * Get the parameters for a type
	 * 
	 * @param  string $type_id The type identifier
	 * 
	 * @return ParameterData          The params for the type
	 *
	 * @since 2.0
	 */
    public function getForm($type_id) {
        return $this->app->data->create($this->getParams()->get('form.'.$type_id, array()));
    }

	/**
	 * Get the application object
	 * 
	 * @return Application The application
	 *
	 * @since 2.0
	 */
	public function getApplication() {
 		return $this->app->table->application->get($this->application_id);
	}

	/**
	 * Check if the submission is in trusted mode
	 * 
	 * @return boolean is in trusted mode
	 *
	 * @since 2.0
	 */
    public function isInTrustedMode() {
        return (bool) $this->getParams()->get('trusted_mode', false);
    }
	
	/**
	 * Is the submission the item edit one?
	 * 
	 * @return boolean If it's the item edit submission
	 *
	 * @since 2.0
	 */
    public function isItemEditSubmission() {
        return (bool) $this->getParams()->get('item_edit', false);
    }	

	
	/**
	 * Check if the tooltips should be shown
	 * 
	 * @return boolean If the tooltips are to be displayed
	 *
	 * @since 2.0
	 */
    public function showTooltip() {
        return (bool) $this->getParams()->get('show_tooltip', true);
    }

}

/**
 * Exception for the Submission Class
 *
 * @see Submission
 */
class SubmissionException extends AppException {}