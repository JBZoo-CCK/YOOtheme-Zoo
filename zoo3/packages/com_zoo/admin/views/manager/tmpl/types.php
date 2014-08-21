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

?>

<form class="manager-types menu-has-level3" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<?php if (count($this->types)) : ?>

	<table id="actionlist" class="list stripe">
	<thead>
		<tr>
			<th class="checkbox">
				<input type="checkbox" class="check-all" />
			</th>
			<th class="name" colspan="2">
				<?php echo JText::_('Name'); ?>
			</th>
			<th class="template">
				<?php echo JText::_('Template Layouts'); ?>
			</th>
			<th class="extension">
				<?php echo JText::_('Extension Layouts'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($this->types as $type) :
				$edit     = $this->app->link(array('controller' => $this->controller, 'group' => $this->group, 'task' => 'edittype', 'cid[]' => $type->id));
				$edit_elm = $this->app->link(array('controller' => $this->controller, 'group' => $this->group, 'task' => 'editelements', 'cid[]' => $type->id));
		?>
		<tr>
			<td class="checkbox">
				<input type="checkbox" name="cid[]" value="<?php echo $type->id; ?>" />
			</td>
			<td class="icon"></td>
			<td class="name">
				<span class="editlink hasTip" title="<?php echo JText::_('Edit Type');?>::<?php echo $type->name; ?>">
					<a href="<?php echo $edit; ?>"><?php echo $type->name; ?></a>
				</span>
				<span class="actions-links">&rsaquo;
					<span class="hasTip" title="<?php echo JText::_('Edit Elements');?>::<?php echo $type->name; ?>">
						<a href="<?php echo $edit_elm; ?>"><?php echo JText::_('Edit Elements');?></a>
					</span>
				</span>
			</td>
			<td class="template">
				<?php foreach ($this->templates as $template) {
					echo '<div>'.$template->getMetadata('name').': ';

					$renderer = $this->app->renderer->create('item')->addPath($template->getPath());

					$path   = 'item';
					$prefix = 'item.';
					if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type->id)) {
						$path   .= DIRECTORY_SEPARATOR.$type->id;
						$prefix .= $type->id.'.';
					}

					$links = array();
					foreach ($renderer->getLayouts($path) as $layout) {

						// get layout metadata
						$metadata = $renderer->getLayoutMetaData($prefix.$layout);

                        if (in_array($metadata->get('type'), array(null, 'related', 'googlemaps'))) {

                            // create link
							$path = $this->app->path->relative($template->getPath());
                            $link = '<a href="'.$this->app->link(array('controller' => $this->controller, 'task' => 'assignelements', 'group' => $this->group, 'type' => $type->id, 'path' => urlencode($path), 'layout' => $layout)).'">'.$metadata->get('name', $layout).'</a>';

                        } else if ($metadata->get('type') == 'submission' || $metadata->get('type') == 'edit') {

							// create link
							$link = '<a href="'.$this->app->link(array('controller' => $this->controller, 'task' => 'assignsubmission', 'group' => $this->group, 'type' => $type->id, 'template' => $template->name, 'layout' => $layout)).'">'.$metadata->get('name', $layout).'</a>';

						}

						// create tooltip
						if ($description = $metadata->get('description')) {
							$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
						}

						$links[] = $link;

					}
					echo implode(' | ', $links);
					echo '</div>';
				} ?>
			</td>
			<td class="extension">
				<?php foreach ($this->extensions as $extension) {

					echo '<div>'.ucfirst($extension['name']).': ';

					$renderer = $this->app->renderer->create()->addPath($extension['path']);

					$links = array();
					foreach ($renderer->getLayouts('item') as $layout) {

						// get layout metadata
						$metadata = $renderer->getLayoutMetaData("item.$layout");

						// create link
						$path = $this->app->path->relative($extension['path']);
						$link = '<a href="'.$this->app->link(array('controller' => $this->controller, 'task' => 'assignelements', 'group' => $this->group, 'type' => $type->id, 'path' => urlencode($path), 'layout' => $layout)).'">'.$metadata->get('name', $layout).'</a>';

						// create tooltip
						if ($description = $metadata->get('description')) {
							$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
						}

						$links[] = $link;
					}
					echo implode(' | ', $links);
					echo '</div>';
				} ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>

	<?php else:

			$title   = JText::_('You don\'t have any types yet').'!';
			$message = JText::_('In the Type Manager you can create, edit and manage your content types. Each type is a composition of different elements. To create an item you have to chose of which type it should be. Build your custom type to fit your item requirements. Make sure your app has at least one type to create an item. Types can be created by clicking the new button in the toolbar to the upper right.');
			echo $this->partial('message', compact('title', 'message'));

	endif; ?>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT;