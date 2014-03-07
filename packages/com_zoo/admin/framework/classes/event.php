<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class for Event Handling and dispatching
 * Based on sfEvent (http://components.symfony-project.org, Fabien Potencier <fabien.potencier@symfony-project.com>, MIT License)
 * 
 * @package Framework.Classes
 */
class AppEvent implements ArrayAccess {

	/**
	 * The return value of the event
	 * 
	 * @var mixed
	 * @since 1.0.0
	 */
	protected $value = null;
	
	/**
	 * If the event has been processed by a listener
	 * 
	 * @var boolean
	 * @since 1.0.0
	 */
	protected $processed = false;
	
	/**
	 * The event subject
	 * 
	 * @var mixed
	 * @since 1.0.0
	 */
	protected $subject = null;
	
	/**
	 * The event name
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $name = '';
	
	/**
	 * The parameters for the event
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $parameters = null;

	/**
	 * Constructs a new AppEvent.
	 *
	 * @param mixed $subject The subject
	 * @param string $name The event name
	 * @param array $parameters An array of parameters
	 */
	public function __construct($subject, $name, $parameters = array()) {
		$this->subject = $subject;
		$this->name = $name;
		$this->parameters = $parameters;
	}

	/**
	 * Returns the subject.
	 *
	 * @return mixed The subject
	 * 
	 * @since 1.0.0
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * Returns the event name.
	 *
	 * @return string The event name
	 * 
	 * @since 1.0.0
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the return value for this event.
	 *
	 * @param mixed $value The return value
	 * 
	 * @return AppEvent Return the object itself to allow chaining
	 * 
	 * @since 1.0.0
	 */
	public function setReturnValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns the return value.
	 *
	 * @return mixed The return value
	 * 
	 * @since 1.0.0
	 */
	public function getReturnValue() {
		return $this->value;
	}

	/**
	 * Sets the processed flag.
	 *
	 * @param boolean $processed The processed flag value
	 * 
	 * @return AppEvent Return the object itself to allow chaining
	 * 
	 * @since 1.0.0
	 */
	public function setProcessed($processed) {
		$this->processed = (boolean) $processed;
		return $this;
	}

	/**
	 * Returns whether the event has been processed by a listener or not.
	 *
	 * @return boolean true if the event has been processed, false otherwise
	 * 
	 * @since 1.0.0
	 */
	public function isProcessed() {
		return $this->processed;
	}

	/**
	 * Returns the event parameters.
	 *
	 * @return array The event parameters
	 * 
	 * @since 1.0.0
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * Returns true if the parameter exists (implements the ArrayAccess interface).
	 *
	 * @param string $name The parameter name
	 *
	 * @return boolean true if the parameter exists, false otherwise
	 * 
	 * @since 1.0.0
	 */
	public function offsetExists($name) {
		return array_key_exists($name, $this->parameters);
	}

	/**
	 * Returns a parameter value (implements the ArrayAccess interface).
	 *
	 * @param string $name The parameter name
	 *
	 * @return mixed The parameter value
	 * 
	 * @since 1.0.0
	 */
	public function offsetGet($name) {

		if (!array_key_exists($name, $this->parameters)) {
			throw new InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
		}

		return $this->parameters[$name];
	}

	/**
	 * Sets a parameter (implements the ArrayAccess interface).
	 *
	 * @param string $name The parameter name
	 * @param mixed $value The parameter value
	 * 
	 * @since 1.0.0
	 */
	public function offsetSet($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 * Removes a parameter (implements the ArrayAccess interface).
	 *
	 * @param string $name The parameter name
	 * 
	 * @since 1.0.0
	 */
	public function offsetUnset($name) {
		unset($this->parameters[$name]);
	}

}

/**
 * Handles the dispatching of the AppEvent events
 */
class AppEventDispatcher {

	/**
	 * The listeners for the events
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $listeners = array();

	/**
	 * Connects a listener to a given event name.
	 *
	 * @param string $name An event name
	 * @param mixed $listener A PHP callable
	 * 
	 * @since 1.0.0
	 */
	public function connect($name, $listener) {

		if (!isset($this->listeners[$name])) {
			$this->listeners[$name] = array();
		}

		$this->listeners[$name][] = $listener;
	}

	/**
	 * Disconnects a listener for a given event name.
	 *
	 * @param string $name An event name
	 * @param mixed $listener A PHP callable
	 *
	 * @return mixed false if listener does not exist, null otherwise
	 * 
	 * @since 1.0.0
	 */
	public function disconnect($name, $listener) {

		if (!isset($this->listeners[$name])) {
			return false;
		}

		foreach ($this->listeners[$name] as $i => $callable) {
			if ($listener === $callable) {
				unset($this->listeners[$name][$i]);
			}
		}

	}

	/**
	 * Notifies all listeners of a given event.
	 *
	 * @param AppEvent $event A AppEvent instance
	 *
	 * @return AppEvent The AppEvent instance
	 * 
	 * @since 1.0.0
	 */
	public function notify(AppEvent $event) {

		foreach ($this->getListeners($event->getName()) as $listener) {
			call_user_func($listener, $event);
		}

		return $event;
	}

	/**
	 * Notifies all listeners of a given event until one returns a non null value.
	 *
	 * @param AppEvent $event A AppEvent instance
	 *
	 * @return AppEvent The AppEvent instance
	 * 
	 * @since 1.0.0
	 */
	public function notifyUntil(AppEvent $event) {

		foreach ($this->getListeners($event->getName()) as $listener) {
			if (call_user_func($listener, $event)) {
				$event->setProcessed(true);
				break;
			}
		}

		return $event;
	}

	/**
	 * Filters a value by calling all listeners of a given event.
	 *
	 * @param AppEvent $event A AppEvent instance
	 * @param mixed $value The value to be filtered
	 *
	 * @return AppEvent The AppEvent instance
	 * 
	 * @since 1.0.0
	 */
	public function filter(AppEvent $event, $value) {

		foreach ($this->getListeners($event->getName()) as $listener) {
			$value = call_user_func_array($listener, array(
				$event,
				$value
			));
		}

		$event->setReturnValue($value);
		return $event;
	}

	/**
	 * Returns true if the given event name has some listeners.
	 *
	 * @param string $name The event name
	 *
	 * @return boolean true if some listeners are connected, false otherwise
	 * 
	 * @since 1.0.0
	 */
	public function hasListeners($name) {

		if (!isset($this->listeners[$name])) {
			$this->listeners[$name] = array();
		}

		return (boolean) count($this->listeners[$name]);
	}

	/**
	 * Returns all listeners associated with a given event name.
	 *
	 * @param string $name The event name
	 *
	 * @return array An array of listeners
	 * 
	 * @since 1.0.0
	 */
	public function getListeners($name) {

		if (!isset($this->listeners[$name])) {
			return array();
		}

		return $this->listeners[$name];
	}

}