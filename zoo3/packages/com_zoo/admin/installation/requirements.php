<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppRequirements
		Class that handles the requirements.
*/
class AppRequirements {

	var $_required_results = array();
	var $_recommended_results = array();

	var $_required_extensions = array(
			array('name' => 'JSON', 'extension' => 'json', 'info' => 'Check http://www.php.net/manual/en/book.json.php'),
			array('name' => 'Multibyte String', 'extension' => 'mbstring', 'info' => 'http://www.php.net/manual/en/book.mbstring.php')
	);

	var $_recommended_extensions = array(
		array('name' => 'cURL', 'extension' => 'curl', 'info' => 'cURL is required for Facebook Connect and Twitter Authenticate to work.'),
        array('name' => 'Multibyte String', 'extension' => 'mbstring', 'info' => 'mbstring is designed to handle Unicode-based encodings such as UTF-8. Check http://www.php.net/manual/en/book.mbstring.php'),
		array('name' => 'Alternative PHP Cache (APC)', 'extension' => 'apc', 'info' => 'An opcode cache, like the Alternative PHP Cache (APC) extension, might significantly improve ZOOs memory footprint. http://www.php.net/manual/en/book.apc.php')
	);

	var $_required_functions = array(
		array('function' => 'imagegd', 'info' => 'Check http://www.php.net/manual/en/image.installation.php'),
		array('function' => 'simplexml_load_string', 'info' => 'Check http://www.php.net/manual/en/function.simplexml-load-file.php'),
		array('function' => 'simplexml_load_file', 'info' => 'Check http://www.php.net/manual/en/function.simplexml-load-string.php'),
		array('function' => 'dom_import_simplexml', 'info' => 'Check http://www.php.net/manual/en/dom.setup.php')
	);

	var $_recommended_functions = array();

	var $_required_classes = array(
		array('class' => 'SimpleXMLElement', 'info' => 'Check http://de.php.net/manual/en/book.simplexml.php'),
		array('class' => 'ArrayObject', 'info' => 'Check http://www.php.net/manual/en/class.arrayobject.php')
	);

	var $_recommended_classes = array();

	function checkPHP() {
		return !version_compare(PHP_VERSION, '5.2.7', '<');
	}

	function checkSafeMode() {
		return !ini_get('safe_mode');
	}

	function checkMemoryLimit() {
		$memory_limit = ini_get('memory_limit');

		return $memory_limit == '-1' ? true : $this->_return_bytes($memory_limit) >= 33554432;
	}

	function checkRealpathCache() {

		if ($this->_return_bytes((string) ini_get('realpath_cache_size')) / 1024 < 512) {
			return false;
		}

		return true;
	}

	function checkAPC() {
		return extension_loaded('apc') && class_exists('APCIterator');
	}

	function _return_bytes ($size_str) {
	    switch (substr ($size_str, -1)) {
	        case 'M': case 'm': return (int) $size_str * 1048576;
	        case 'K': case 'k': return (int) $size_str * 1024;
	        case 'G': case 'g': return (int) $size_str * 1073741824;
	        default: return $size_str;
	    }
	}

	function checkRequirements() {
		$this->_required_results = array();
		$this->_recommended_results = array();

		$result = $this->_checkRequired();
		$this->_checkRecommended();

		return $result;
	}

	function _checkRequired() {

		// check php
		$status = $this->checkPHP();
		$info 	= 'Zoo requires PHP 5.2.7+. Please upgrade your PHP version (http://www.php.net).';
		$this->_addRequiredResult('PHP 5.2.7+', $status, $info);

		foreach ($this->_required_extensions as $extension) {
			$status = extension_loaded($extension['extension']);
			$this->_addRequiredResult('Extension: ' . $extension['name'], $status, $extension['info']);
		}

		foreach ($this->_required_functions as $function) {
			$status = function_exists($function['function']);
			$this->_addRequiredResult('Function: ' . $function['function'], $status, $function['info']);
		}

		foreach ($this->_required_classes as $class) {
			$status = class_exists($class['class']);
			$this->_addRequiredResult('Class: ' . $class['class'], $status, $class['info']);
		}

		foreach ($this->_required_results as $return) {
			if (!$return['status']) {
				return $return;
			}
		}

		return true;
	}

	function _checkRecommended() {

		foreach ($this->_recommended_extensions as $extension) {
			$status = extension_loaded($extension['extension']);
			$this->_addRecommendedResult('Extension: ' . $extension['name'], $status, $extension['info']);
		}

		foreach ($this->_recommended_functions as $function) {
			$status = function_exists($function['function']);
			$this->_addRecommendedResult('Function: ' . $function['function'], $status, $function['info']);
		}

		foreach ($this->_recommended_classes as $class) {
			$status = class_exists($class['class']);
			$this->_addRecommendedResult('Class: ' . $class['class'], $status, $class['info']);
		}

		// check safe mode
		$status = $this->checkSafeMode();
		$info 	= 'It is recommended to turn off PHP safe mode.';
		$this->_addRecommendedResult('PHP Safe Mode', $status, $info);

		$status = $this->checkMemoryLimit();
		$info 	= 'It is recommended to set the php setting memory_limit to 32M or higher.';
		$this->_addRecommendedResult('PHP Memory Limit', $status, $info);

		$status = $this->checkRealpathCache();
		$info 	= 'It is recommended to set the php <a target="_blank" href="http://www.php.net/manual/en/ini.core.php#ini.realpath-cache-size">realpath cache setting</a> realpath_cache_size to 512K or higher.';
		$this->_addRecommendedResult('PHP Realpath Cache', $status, $info);

		if (extension_loaded('apc')) {
			$status = $this->checkAPC();
			$info 	= 'It is recommended to turn on APC (version 3.1.2+).';
			$this->_addRecommendedResult('Alternative PHP Cache (APC) enabled', $status, $info);
		}

		foreach ($this->_recommended_results as $return) {
			if (!$return['status']) {
				return $return['info'];
			}
		}

		return false;
	}

	function _addRequiredResult($name, $status, $info = '') {
		$this->_required_results[] = compact('name', 'status', 'info');
	}

	function _addRecommendedResult($name, $status, $info = '') {
		$this->_recommended_results[] = compact('name', 'status', 'info');
	}

	function displayResults() {
		?>

		<h3><?php echo JText::_('Zoo Requirements'); ?></h3>
		<div><?php echo JText::_('If any of the items below are highlighted in red, you should try to correct them. Failure to do so could lead to your ZOO installation not functioning correctly.'); ?></div>
		<table class="adminlist table table-bordered table-striped" width="100%">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_('Requirement'); ?></th>
					<th width="20%"><?php echo JText::_('Status'); ?></th>
					<th width="60%"><?php echo JText::_('Info'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($this->_required_results as $i => $req) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $req['name']; ?></td>
						<td>
							<?php $style = $req['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo $req['status'] ? JText::_('OK') : JText::_('Not OK'); ?></span>
						</td>
						<td>
							<span><?php echo $req['status'] ? '' : JText::_($req['info']); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h3><?php echo JText::_('Zoo Recommendations'); ?></h3>
		<div><?php echo JText::_('The items below are recommendations only and ZOO will work fine if items are marked in red. However, you\'ll have an improved ZOO experience by fullfilling these recommendations.'); ?></div>
		<table class="adminlist table table-bordered table-striped" width="100%">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_('Recommendation'); ?></th>
					<th width="20%"><?php echo JText::_('Status'); ?></th>
					<th width="60%"><?php echo JText::_('Info'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($this->_recommended_results as $i => $req) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $req['name']; ?></td>
						<td>
							<?php $style = $req['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo $req['status'] ? JText::_('OK') : JText::_('Not OK'); ?></span>
						</td>
						<td>
							<span><?php echo $req['status'] ? '' : JText::_($req['info']); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
	}

}