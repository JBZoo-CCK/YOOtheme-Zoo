<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->html->_('behavior.tooltip');

// add script
$this->app->document->addScript('assets:js/type.js');

?>

<form class="assign-elements menu-has-level3" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-50">

		<fieldset>
		<legend><?php echo JText::_('Positions'); ?></legend>

			<?php
				$elements  = array_merge($this->type->getElements(), $this->type->getCoreElements());
				if (isset($this->positions['positions']) && ($positions = $this->positions['positions']) && count($positions)) {
					foreach ($positions as $position => $name) {
						echo '<div class="position">'.$name.'</div>';
						echo '<ul class="element-list" data-position="'.$position.'">';

						if ($this->config && isset($this->config[$position])) {
							$i = 0;
							foreach ($this->config[$position] as $data) {
								if (isset($data['element'], $elements[$data['element']])) {
									$element = $elements[$data['element']];

									// render partial
									echo $this->partial('assignelement', array('element' => $element, 'data' => $data, 'position' => $position, 'index' => $i++, 'core' => ($element->getGroup() == 'Core')));
								}
							}
						}

						echo '</ul>';
					}
				} else {
					echo '<i>'.JText::_('No positions defined').'</i>';
				}
			?>

		</fieldset>

	</div>

	<div class="col col-right width-50">

		<fieldset>
		<legend><?php echo JText::_('Core'); ?></legend>

		<?php
			$elements = $this->type->getCoreElements();
			if ($elements !== false) {
				echo '<ul class="element-list unassigned core" data-position="unassigned">';
				foreach ($elements as $element) {

					// render partial
					echo $this->partial('assignelement', array('element' => $element, 'data' => array()));
				}
				echo '</ul>';
			}
		?>

		</fieldset>

		<fieldset>
		<legend><?php echo JText::_('Custom'); ?></legend>

		<?php
			$elements = $this->type->getElements();
			if ($elements === false || count($elements) == 0) {
				echo '<i>'.JText::_('No custom elements defined for this type').'</i>';
			}

			if ($elements !== false) {
				echo '<ul class="element-list unassigned" data-position="unassigned">';
				foreach ($elements as $element) {

					// render partial
					echo $this->partial('assignelement', array('element' => $element, 'data' => array()));
				}
				echo '</ul>';
			}
		?>

		</fieldset>

	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="type" value="<?php echo $this->type->id; ?>" />
<input type="hidden" name="path" value="<?php echo urlencode($this->relative_path); ?>" />
<input type="hidden" name="layout" value="<?php echo $this->layout; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').AssignElements();
	});
</script>

<?php echo ZOO_COPYRIGHT;