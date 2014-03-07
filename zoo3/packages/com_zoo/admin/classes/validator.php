<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Validator Class
 *
 * @package Component.Classes.Validators
 */
class AppValidator {

    /**
     * Reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

	const ERROR_CODE_REQUIRED = 100;

    /**
     * List of messages to return for the validation
     *
     * @var array
     * @since 2.0
     */
    protected $_messages = array();

    /**
     * List of options for the validation
     *
     * @var array
     * @since 2.0
     */
    protected $_options  = array();

    /**
     * Class Constructor
     *
     * @param array $options  The list of options to use for the validation (array('requred' , 'trim', 'empty_value'))
     * @param array $messages Associative array of text messages for the validation (array('requred' , 'invalid'))
     */
    public function __construct($options = array(), $messages = array()) {

		$this->app = App::getInstance('zoo');

        $this->_options  = array_merge(array('required' => true, 'trim' => false, 'empty_value' => null), $this->_options);
        $this->_messages = array_merge(array('required' => 'This field is required', 'invalid' => 'Invalid'), $this->_messages);

        $this->_configure($this->_options, $this->_messages);

 	    $this->_options  = array_merge($this->_options, $options);
 	    $this->_messages = array_merge($this->_messages, $messages);

    }

    /**
     * Configure the validation
     *
     * @param  array  $options  The list of options
     * @param  array  $messages The list of messages
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('invalid',  'Invalid');
    }

    /**
     * Clean a value using the options set in the validator
     *
     * @param  mixed $value The value to clean
     *
     * @return mixed        The cleaned value
     *
     * @since 2.0
     */
    public function clean($value) {
        $clean = $value;

        if ($this->getOption('trim') && is_string($clean)) {
            $clean = JString::trim($clean);
        }

        if ($this->isEmpty($clean)) {
            if ($this->getOption('required')) {
                throw new AppValidatorException($this->getMessage('required'), self::ERROR_CODE_REQUIRED);
            }

            return $this->getEmptyValue();
        }

        return $this->_doClean($clean);
    }

    /**
     * Add a message
     *
     * @param string $name  The key of the message
     * @param string $value The message
     *
     * @return AppValidator $this for chaining support
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
    public function addMessage($name, $value = null) {
        $this->_messages[$name] = $value;

        return $this;
    }

    /**
     * Get a message string
     *
     * @param  string $name The key of the message
     *
     * @return string       The message
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
    public function getMessage($name) {
        return isset($this->_messages[$name]) ? $this->_messages[$name] : '';
    }

    /**
     * Check if a value is empty (either null, an empty array or an emtpy string)
     *
     * @param  mixed  $value A value to check
     *
     * @return boolean        If the value is empty
     *
     * @since 2.0
     */
    protected function isEmpty($value) {
        return in_array($value, array(null, '', array()), true);
    }

    /**
     * If the validator has a given option
     *
     * @param  string  $name The option key
     *
     * @return boolean       True if that option is set
     *
     * @since 2.0
     */
    public function hasOption($name) {
        return isset($this->_options[$name]);
    }

    /**
     * Get an option valu
     *
     * @param  string $name The option key
     *
     * @return mixed       The option value
     *
     * @since 2.0
     */
    public function getOption($name) {
        if ($this->hasOption($name)) {
            return $this->_options[$name];
        }
        return null;
    }

    /**
     * Add an option to the validator
     *
     * @param string $name  The option key
     * @param mixed  $value The option value
     *
     * @return  AppValidator    $this for chaining support
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
    public function addOption($name, $value = null) {
        $this->_options[$name] = $value;
        return $this;
    }

    /**
     * Set a a list of options
     *
     * @param array $options The list of options to set
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
    public function setOptions($options = array()) {
        $this->_options = $options;
        return $this;
    }

    /**
     * Remove an option
     *
     * @param  string $name The option key
     *
     * @see AppValidator::__construct()
     *
     * @since 2.0
     */
	public function removeOption($name) {
		if (isset($this->_options[$name])) {
			unset($this->_options);
		}
	}

    /**
     * Get the "empty_value" option status
     *
     * @return boolean  If the "empty_value" option is set
     *
     * @since 2.0
     */
    public function getEmptyValue() {
        return $this->getOption('empty_value');
    }

    /**
     * perform a clean operation
     *
     * @param  mixed $value The value to clean
     *
     * @return mixed        the cleaned value
     *
     * @since 2.0
     */
    protected function _doClean($value) {
        return $value;
    }

}

/**
 * Password Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorPass extends AppValidator {

    /**
     * Clean the value
     *
     * @param  mixed $value The value to clean
     *
     * @return mixed        The cleaned value
     *
     * @see AppValidator::clean()
     *
     * @since 2.0
     */
    public function clean($value) {
        return $this->_doClean($value);
    }

    /**
     * Perform the actual clean operation
     *
     * @param  mixed $value the value to clean
     *
     * @return mixed        The cleaned value
     *
     * @since 2.0
     */
    protected function _doClean($value) {
        return $value;
    }
}

/**
 * String Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorString extends AppValidator {

    /**
     * Clean a value, forcing it to be a string
     *
     * @param  mixed $value the value to clean
     *
     * @return string        the clean value as a string
     *
     * @see AppValidator::clean()
     *
     * @since 2.0
     */
    protected function _doClean($value) {
        $clean = (string) $value;

        return $clean;
    }

    /**
     * Get the string emtpy value
     *
     * @return string the empty string
     *
     * @since 2.0
     */
    public function getEmptyValue() {
        return '';
    }

}

/**
 * TextFilter Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorTextFilter extends AppValidatorString {

    /**
     * Clean a value, using joomla's textfilters options
     *
     * @param  mixed $value the value to clean
     *
     * @return string        the clean value as a string
     *
     * @see AppValidator::clean()
     *
     * @since 2.0
     */
    protected function _doClean($value) {
        $clean = parent::_doClean($value);
        $clean = $this->app->string->applyTextFilters($clean);

        return $clean;
    }
}

/**
 * Integer Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorInteger extends AppValidator {

    /**
     * Configure the validator, adding the "number" Message
     *
     * @param  array  $options  The list of options to add
     * @param  array  $messages The list of messages to add
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not an integer.');
    }

    /**
     * Performs the clean operation, forcing the value to be an integer
     *
     * @param  mixed $value The value to clean
     *
     * @return int        The clean value as an integer
     *
     * @since 2.0
     */
    protected function _doClean($value) {

        $clean = intval($value);

        if (strval($clean) != $value) {
            throw new AppValidatorException($this->getMessage('number'));
        }

        return $clean;
    }

    /**
     * Get the integer empty value, 0
     *
     * @return int Returns 0
     *
     * @since 2.0
     */
    public function getEmptyValue() {
        return 0;
    }

}

/**
 * Number validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorNumber extends AppValidator {

    /**
     * Configure the validator, adding the "number" message
     *
     * @param  array  $options  The list of options to add
     * @param  array  $messages The list of messages to add
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not a number.');
    }

    /**
     * Perform the actual clean, forcing it to be a float number
     *
     * @param  mixed $value The value to clean
     *
     * @return float        The value to clean, as a float
     *
     * @since 2.0
     */
    protected function _doClean($value) {

        if (!is_numeric($value)) {
            throw new AppValidatorException($this->getMessage('number'));
        }

        $clean = floatval($value);

        return $clean;
    }

    /**
     * Get the empty value as a float, 0.0
     *
     * @return float Returns 0.0
     *
     * @since 2.0
     */
    public function getEmptyValue() {
        return 0.0;
    }

}

/**
 * File validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorFile extends AppValidator {

    /**
     * Configure the Validator, adding options and messages regarding file validation
     *
     * @param  array  $options  The list of options to add
     * @param  array  $messages The list of messages to add
     *
     * @throws AppValidatorException    If file uploads are disabled
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        if (!ini_get('file_uploads')) {
            throw new AppValidatorException('File uploads are disabled.');
        }

		$this->addOption('max_size');
        $this->addOption('mime_types');
		$this->addOption('mime_type_group');
		$this->addOption('extension');

		$this->addMessage('extension', 'This is not a valid extension.');
        $this->addMessage('max_size', 'File is too large (max %s KB).');
        $this->addMessage('mime_types', 'Invalid mime type.');
		$this->addMessage('mime_type_group', 'Invalid mime type.');
        $this->addMessage('partial', 'The uploaded file was only partially uploaded.');
        $this->addMessage('no_file', 'No file was uploaded.');
        $this->addMessage('no_tmp_dir', 'Missing a temporary folder.');
        $this->addMessage('cant_write', 'Failed to write file to disk.');
        $this->addMessage('err_extension', 'File upload stopped by extension.');

    }

    /**
     * Clean the file value
     *
     * @param  mixed $value The value to clean
     *
     * @return mixed        The cleaned value
     *
     * @see AppValidator::clean()
     *
     * @since 2.0
     */
    public function clean($value) {
        if (!is_array($value) || !isset($value['tmp_name'])) {
			throw new AppValidatorException($this->getMessage('invalid'));
        }

        if (!isset($value['name'])) {
			$value['name'] = '';
        }

        $value['name'] = JFile::makeSafe($value['name']);

        if (!isset($value['error'])) {
			$value['error'] = UPLOAD_ERR_OK;
        }

        if (!isset($value['size'])) {
			$value['size'] = filesize($value['tmp_name']);
        }

        if (!isset($value['type'])) {
            $value['type'] = 'application/octet-stream';
        }

        switch ($value['error']) {
			case UPLOAD_ERR_INI_SIZE:
				throw new AppValidatorException(sprintf($this->getMessage('max_size'), $this->returnBytes(@ini_get('upload_max_filesize')) / 1024), UPLOAD_ERR_INI_SIZE);
			case UPLOAD_ERR_FORM_SIZE:
				throw new AppValidatorException($this->getMessage('max_size'), UPLOAD_ERR_FORM_SIZE);
			case UPLOAD_ERR_PARTIAL:
				throw new AppValidatorException($this->getMessage('partial'), UPLOAD_ERR_PARTIAL);
			case UPLOAD_ERR_NO_FILE:
				throw new AppValidatorException($this->getMessage('no_file'), UPLOAD_ERR_NO_FILE);
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new AppValidatorException($this->getMessage('no_tmp_dir'), UPLOAD_ERR_NO_TMP_DIR);
			case UPLOAD_ERR_CANT_WRITE:
				throw new AppValidatorException($this->getMessage('cant_write'), UPLOAD_ERR_CANT_WRITE);
			case UPLOAD_ERR_EXTENSION:
				throw new AppValidatorException($this->getMessage('err_extension'), UPLOAD_ERR_EXTENSION);
        }

        // check mime type
        if ($this->hasOption('mime_types')) {
            $mime_types = $this->getOption('mime_types') ? $this->getOption('mime_types') : array();
            if (!in_array($value['type'], array_map('strtolower', $mime_types))) {
                throw new AppValidatorException($this->getMessage('mime_types'));
            }
        }

		// check mime type group
		if ($this->hasOption('mime_type_group')) {
			if (!in_array($value['type'], $this->_getGroupMimeTypes($this->getOption('mime_type_group')))) {
                throw new AppValidatorException($this->getMessage('mime_type_group'));
            }
		}

        // check file size
        if ($this->hasOption('max_size') && $this->getOption('max_size') < (int) $value['size']) {
			throw new AppValidatorException(sprintf(JText::_($this->getMessage('max_size')), ($this->getOption('max_size') / 1024)));
        }

		// check extension
		if ($this->hasOption('extension') && !in_array($this->app->filesystem->getExtension($value['name']), $this->getOption('extension'))) {
			throw new AppValidatorException($this->getMessage('extension'));
        }

        return $value;
    }

    /**
     * Get the mime type for a group
     *
     * @param  string $group The group
     *
     * @return array        The list of mime types
     *
     * @see FilesystemHelper::getMimeMapping()
     *
     * @since 2.0
     */
	protected function _getGroupMimeTypes($group) {
		$mime_types = $this->app->data->create($this->app->filesystem->getMimeMapping());
		$mime_types = $mime_types->flattenRecursive();
		$mime_types = array_filter($mime_types, create_function('$a', 'return preg_match("/^'.$group.'\//i", $a);'));
		return array_map('strtolower', $mime_types);
	}

    /**
     * Get the size string in bytes
     *
     * @param  string $size_str The size to convert (Mb, GB, KB)
     *
     * @return int           The size in bytes
     *
     * @since 2.0
     */
	protected function returnBytes ($size_str) {
	    switch (substr ($size_str, -1)) {
	        case 'M': case 'm': return (int) $size_str * 1048576;
	        case 'K': case 'k': return (int) $size_str * 1024;
	        case 'G': case 'g': return (int) $size_str * 1073741824;
	        default: return $size_str;
	    }
	}

}

/**
 * Date Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorDate extends AppValidatorString {

    /**
     * Configure the validator, adding options and messages for the date validation
     *
     * @param  array  $options  The list of options
     * @param  array  $messages The list of messages
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
		$this->addOption('date_format_regex', '/^((\d{2}|\d{4}))-(\d{1,2})-(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?$/');
		$this->addOption('date_format', '%Y-%m-%d %H:%M:%S');
		$this->addOption('allow_db_null_date', false);
		$this->addOption('db_null_date', $this->app->database->getNullDate());
		$this->addMessage('bad_format', '"%s" is not a valid date.');
	}

    /**
     * Performs the real clean, convering the value to the configured date format
     *
     * @param  mixed $value The value to clean
     *
     * @return string        The cleaned value
     *
     * @throws AppValidatorException If the format is not configured or the date is not in a recognized format
     *
     * @since 2.0
     */
    protected function _doClean($value) {

		// init vars
		$value = parent::_doClean($value);

		if (!preg_match($this->getOption('date_format_regex'), $value)) {
			throw new AppValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		if ($this->getOption('allow_db_null_date') && $value == $this->getOption('db_null_date')) {
			return $value;
		}

		$clean = strtotime($value);

		if (empty($clean)) {
			throw new AppValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		$clean = strftime($this->getOption('date_format'), $clean);
		return $clean;

    }

}

/**
 * Base class for validating regular expression based strings
 *
 * @package Component.Classes.Validators
 */
abstract class AppValidatorRegex extends AppValidatorString {

    /**
     * Performs the real clean, checking the pattern
     *
     * @param  mixed $value The value to clean
     *
     * @return string        The cleaned regexp
     *
     * @throws AppValidatorException If no pattern is configured
     *
     * @since 2.0
     */
    protected function _doClean($value) {

        $clean = parent::_doClean($value);

        if ($pattern = $this->getPattern()) {
            if (!preg_match($pattern, $clean)) {
                throw new AppValidatorException($this->getMessage('pattern'));
            }
        }

        return $clean;
    }

    /**
     * Set a pattern for the regular expression
     *
     * @param string $pattern The regular expression pattern
     *
     * @return AppValidatorRegex $this for chaining support
     *
     * @since 2.0
     */
    public function setPattern($pattern) {
        $this->addOption('pattern', $pattern);
        return $this;
    }

    /**
     * Get the regular expression pattern
     *
     * @return string The regular expression pattern
     *
     * @since 2.0
     */
    public function getPattern() {
        return $this->getOption('pattern');
    }

}

/**
 * Email validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorEmail extends AppValidatorRegex {

    const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    /**
     * Configure the validator, adding the email regexp and the message for an invalid email
     *
     * @param  array  $options  The list of options
     * @param  array  $messages The list of messages
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        $this->setPattern(self::REGEX_EMAIL);
        $this->addMessage('pattern', 'Please enter a valid email address.');
    }

}

/**
 * Url Validator
 *
 * @package Component.Classes.Validators
 */
class AppValidatorUrl extends AppValidatorRegex {

    const REGEX_URL ='/^(%s):\/\/(([a-z0-9-\\x80-\\xff]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(\/?|\/\S+)$/i';

    /**
     * Configure the validator, adding the protocol options, the url regexp and the message
     *
     * @param  array  $options  The list of options
     * @param  array  $messages The list of messages
     *
     * @since 2.0
     */
    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('protocols', array('http', 'https', 'ftp', 'ftps'));
        $this->setPattern(sprintf(self::REGEX_URL, implode('|', $this->getOption('protocols'))));
        $this->addMessage('pattern', 'Please enter a valid URL.');
    }

}

/**
 * Validator to clean multiple values
 *
 * @package Component.Classes.Validators
 */
class AppValidatorForeach extends AppValidator {

    /**
     * The validator to use to validate each item in the lsit
     *
     * @var AppValidator
     * @since 2.0
     */
    protected $_validator;

    /**
     * Class Constructor
     *
     * @param AppValidator $validator The validator to use
     * @param array  $options   The list of options
     * @param array  $messages  The list of messages
     */
    public function __construct($validator, $options = array(), $messages = array()) {

        parent::__construct($options, $messages);

        $this->_validator = $validator;

    }

    /**
     * Get the validator we're using for validating the single items
     *
     * @return AppValidator The validator
     *
     * @since 2.0
     */
    public function getValidator() {

        if (!$this->_validator) {
            $this->_validator = new AppValidatorPass();
        }

        return $this->_validator;

    }

    /**
     * Performs the cleaning on each item
     *
     * @param  mixed $value The value to clean
     *
     * @return mixed        The cleaned value
     *
     * @since 2.0
     */
    protected function _doClean($value) {
        $clean = array();

        if (is_array($value)) {

            foreach ($value as $key => $single_value) {
                $clean[$key] = $this->getValidator()->clean($single_value);
            }

        } else {
            throw new AppValidatorException($this->getMessage('invalid'));
        }

        return $clean;

    }

    /**
     * Get the empty value, i.e.: an empty array
     *
     * @return array The empty array
     *
     * @since 2.0
     */
    public function getEmptyValue() {
        return array();
    }

}

/**
 * Exception for the validator classes
 *
 * @see AppValidator
 */
class AppValidatorException extends AppException {

    /**
     * Converts the exception to string using the error string
     *
     * @return string The error message
     */
	public function __toString() {
		return JText::_($this->getMessage());
	}

}