<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with application events.
 *
 * @package Component.Events
 * @since 3.2
 */
class AssetEvent {

    /**
     * Function for the saved event
     *
     * @param  AppEvent $event The event triggered
     */
    public static function saved($event) {
        $application    = $event->getSubject();
        $currentAssetId = $application->asset_id;
        $parentId       = $application->getAssetParentId();
        $name           = $application->getAssetName();
        $title          = $application->getAssetTitle();
        $asset          = JTable::getInstance('Asset');

        $asset->loadByName($name);
        $application->asset_id = $asset->id;

        if ($asset_id = self::saveAsset($asset, $name, $parentId, $title, $application->rules)) {
            if (empty($application->asset_id) || ($currentAssetId != $application->asset_id && !empty($application->asset_id)))
            {
                $application->updateAssetId($asset_id);
            }
            $parentId = $asset_id;
            foreach ($application->assetRules as $assetName => $rules) {
                $childName = strtolower(preg_replace('#[\s\-]+#', '.', trim($name . '.' . $assetName)));
                $asset = JTable::getInstance('Asset');
                $asset->loadByName($childName);
                self::saveAsset($asset, $childName, $parentId, $title . ' (' . ucfirst($assetName) . ')', $rules);
            }
        } else {
            return false;
        }
    }

    /**
     * Function for the deleted event
     *
     * @param  AppEvent $event The event triggered
     */
    public static function deleted($event) {
        $application = $event->getSubject();
        $name        = $application->getAssetName();
        $asset       = JTable::getInstance('Asset');

        if ($asset->loadByName($name))
        {
            if (!$asset->delete())
            {
                $this->setError($asset->getError());

                return false;
            }
        }
    }

    /**
     * Saves an asset
     *
     * @param  JTableAsset $asset
     * @param  string $name
     * @param  int $parentId
     * @param  string $title
     * @param  array $rules
     *
     * @return int|boolean
     */
    protected static function saveAsset($asset, $name, $parentId, $title, $rules) {
        $asset->parent_id = $parentId;
        $asset->name      = $name;
        $asset->title     = $title;

        $error = $asset->getError();

        if ($error) {
            $this->setError($error);
            return false;
        } else {
            if (empty($asset->id) || $asset->parent_id != $parentId) {
                $asset->setLocation($parentId, 'last-child');
            }
            $temp = array();
            foreach ($rules as $ruleName => $rules) {
                $temp[$ruleName] = array();
                foreach ($rules as $userGroup => $permission) {
                    if ($permission !== "") {
                        $temp[$ruleName][(int) $userGroup] = (int) $permission;
                    }
                }
            }
            $asset->rules = json_encode($temp);
            if (!$asset->check() || !$asset->store()) {
                $this->setError($asset->getError());
                return false;
            } else {
                return $asset->id;
            }
        }
    }
}
