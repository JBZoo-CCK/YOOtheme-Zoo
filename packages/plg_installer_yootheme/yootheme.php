<?php

defined('_JEXEC') or die;

class plgInstallerYootheme extends JPlugin
{
    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        if (parse_url($url, PHP_URL_HOST) == 'yootheme.com') {

            if ($key = $this->params->get('apikey')) {

                $pos = strpos($url, '?');

                if ($pos === false) {
                    $url .= "?key=$key";
                } else {
                    $url = substr_replace($url, "?key=$key&", $pos, 1);
                }
            } else {

                // load default and current language
                $jlang = JFactory::getLanguage();
                $jlang->load('plg_installer_yootheme', JPATH_ADMINISTRATOR, 'en-GB', true);
                $jlang->load('plg_installer_yootheme', JPATH_ADMINISTRATOR, null, true);

                // warn about missing api key
                JFactory::getApplication()->enqueueMessage(JText::_('PLG_INSTALLER_YOOTHEME_API_KEY_WARNING'), 'notice');
            }

        }

        return true;
    }
}
