<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
$this->app->system->document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$locale&key=$key");
$this->app->document->addScript('elements:googlemaps/googlemaps.js');

?>
<div class="googlemaps" style="<?php echo $css_module_width ?>">

	<?php if ($information) : ?>
	<p class="mapinfo"><?php echo $information; ?></p>
	<?php endif; ?>

	<div id="<?php echo $maps_id ?>" style="<?php echo $css_module_width . $css_module_height ?>"></div>

</div>
<?php echo "<script type=\"text/javascript\" defer=\"defer\">\n// <!--\n$javascript\n// -->\n</script>\n";