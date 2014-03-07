<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($pagination = $this->pagination->render($this->pagination_link)) : ?>
	<div class="zoo-pagination">
		<div class="pagination-bg">
			<?php echo $pagination; ?>
		</div>
	</div>
<?php endif;