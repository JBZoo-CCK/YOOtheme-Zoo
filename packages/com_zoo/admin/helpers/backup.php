<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Backup helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class BackupHelper extends AppHelper {

	/**
	 * Returns all ZOO table names
	 *
	 * @return array All ZOO tables
	 *
	 * @since 2.0
	 */
	public function getTables() {
		$tables = array();
		foreach (get_defined_constants() as $key => $define) {
			if (preg_match('/^ZOO_TABLE_/', $key)) {
				$tables[$key] = $define;
			}
		}
		return $tables;
	}

	/**
	 * Creates a backup of all ZOO tables
	 *
	 * @param callable $callback A callback to be called on all result rows
	 *
	 * @return string|false filename on success, else false
	 *
	 * @since 2.0
	 */
	public function all($callback = true) {
		return $this->table($this->getTables(), $callback);
	}

	/**
	 * Creates a backup of tables
	 *
	 * @param array $tables Table(s) to backup
	 * @param function $callback The callback to call when the backup is done. Default: false
	 *
	 * @return string the mysql statements
	 *
	 * @since 2.0
	 */
	public function table($tables, $callback = false) {

		// set_time_limit doesn't work in safe mode
		if (!ini_get('safe_mode')) {
			@set_time_limit(0);
		}

		// init vars
		$db = $this->app->database;
		$tables = (array) $tables;
		$result = array();
		if (!empty($tables)) {

			foreach ($tables as $table) {

				$table = $db->replacePrefix($table);

				// create comments
				$result[] = "\n\n-- --------------------------------------------------------\n";
				$result[] = '--';
				$result[] = '-- Table structure for table '.$table;
				$result[] = "--\n";

				$rows = $db->queryAssocList('SELECT * FROM '.$table);

				if (is_callable($callback)) {
					$rows = array_map($callback, $rows);
				}

				$result[] = 'DROP TABLE IF EXISTS '.$table.';';
				$create = $db->queryAssoc('SHOW CREATE TABLE '.$table);
				$create = preg_replace("#(TYPE)=(MyISAM)#i", "ENGINE=MyISAM", $create);
				$create = $create['Create Table'];
				$result[] = "$create;\n";

				// create comments
				$result[] = '--';
				$result[] = '-- Table data for table '.$table;
				$result[] = '--';

				$insert = 'INSERT INTO '.$table.' VALUES(';
				foreach ($rows as $row) {
					$result[] = $insert.'"'.implode('","', array_map(array($db, 'escape'), $row))."\");";
				}
			}

			return implode("\n", $result);
		}
	}

	/**
	 * Restores a backup from sql dump file
	 *
	 * @param array $file The sql dump file
	 *
	 * @return boolean true on success
	 *
	 * @since 2.0
	 */
	public function restore($file) {

		if (JFile::exists($file)) {

			$db = $this->app->database;

			// read index.sql
			$buffer = file_get_contents($file);

			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (!empty($queries)) {

				foreach ($queries as $query) {
					$query = trim($query);
					if (!empty($query)) {
						$db->query($query);
					}
				}

				return true;
			}
		}
		throw new RuntimeException("File not found ($file)");
	}

	/**
	 * Generates a header for the backup file
	 *
	 * @return string The header for the backup file
	 *
	 * @since 2.0
	 */
	public function generateHeader() {

		$header = array('-- ZOO SQL Dump');
		$header[] = '-- version '.$this->app->zoo->version();
		$header[] = '-- http://www.yootheme.com';
		$header[] = '--';
		$header[] = '-- Host: '.trim(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$header[] = '-- Creation Date: '.$this->app->date->create()->format('%Y-%m-%d %H:%M:%S');
		$header[] = '-- Server Software: '.trim(isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '');
		$header[] = "\n";
		$header[] = '-- ';
		$header[] = '-- Database: '.$this->app->system->config->get('db');
		$header[] = '-- ';

		return implode("\n", $header);
	}

}