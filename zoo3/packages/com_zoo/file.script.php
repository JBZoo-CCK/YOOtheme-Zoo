<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class com_zooInstallerScript {


	public function install($parent) {

		// try to set time limit
		@set_time_limit(0);

		// try to increase memory limit
		if ((int) ini_get('memory_limit') < 32) {
			@ini_set('memory_limit', '32M');
		}

		// create applications folder
		if (!JFolder::exists(JPATH_ROOT . '/media/zoo/applications/')) {
			JFolder::create(JPATH_ROOT . '/media/zoo/applications/');
		}

		// initialize zoo framework
		require_once($parent->getParent()->getPath('extension_administrator').'/config.php');

		// get zoo instance
		$zoo = App::getInstance('zoo');

		// copy checksums file
		if (JFile::exists($parent->getParent()->getPath('source').'/checksums')) {
			JFile::copy($parent->getParent()->getPath('source').'/checksums', $zoo->path->path('component.admin:').'/checksums');
		}

		try {

			// clean ZOO installation
			$zoo->modification->clean();

		} catch (Exception $e) {}

		// applications
		foreach (JFolder::folders($parent->getParent()->getPath('source').'/media/applications', '.', false, true) as $folder) {
			try {
				if (!$manifest = $zoo->install->findManifest($folder) or !$zoo->install->installApplicationFromFolder($folder)) {
					$zoo->error->raiseNotice(0, JText::sprintf('Unable to install/update app from folder (%s)', $folder));
				}
			} catch (AppException $e) {}
		}

		return true;

	}

	public function uninstall($parent) {

		// remove media folder
		if (JFolder::exists(JPATH_ROOT . '/media/zoo/applications/')) {
			JFolder::delete(JPATH_ROOT . '/media/zoo/applications/');
		}

		return true;
	}

	public function update($parent) {

		if ($manifest = $parent->get('manifest')) {
			if (isset($manifest->install->sql)) {
				if ($parent->getParent()->parseSQLFiles($manifest->install->sql) === false) {
					// Install failed, rollback changes
					$parent->getParent()->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_SQL_ERROR', JFactory::getDBO()->stderr(true)));

					return false;
				}
			}
		}

		return $this->install($parent);
	}

	public function preflight($type, $parent) {

		// check ZOO requirements
		require_once($parent->getParent()->getPath('source').'/admin/installation/requirements.php');

		$requirements = new AppRequirements();
		if (true !== $error = $requirements->checkRequirements()) {
			$parent->getParent()->abort(JText::_('Component').' '.JText::_('Install').': '.JText::sprintf('Minimum requirements not fulfilled (%s: %s).', $error['name'], $error['info']));
			return false;
		}

	}

	public function postflight($type, $parent) {

		$row = JTable::getInstance('extension');
		if ($row->load($row->find(array('element' => 'com_zoo'))) && strlen($row->element)) {
			$row->client_id = 1;
			$row->store();
		}

		// initialize zoo framework
		require_once($parent->getParent()->getPath('extension_administrator').'/config.php');

		// get zoo instance
		$zoo = App::getInstance('zoo');

		// finally update
		if ($zoo->update->required()) {
			$zoo->error->raiseNotice(0, JText::_('ZOO requires an update. Please click <a href="index.php?option=com_zoo">here</a>.'));
		}

	}

}