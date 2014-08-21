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

<form class="submissions-default" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">

		<?php if(count($this->submissions) > 0) : ?>

		<table class="list stripe">
			<thead>
				<tr>
					<th class="checkbox">
						<input type="checkbox" class="check-all" />
					</th>
					<th class="name" colspan="2">
						<?php echo $this->app->html->_('grid.sort', 'Name', 'name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="types">
						<?php echo JText::_('Submittable Types'); ?>
					</th>
					<th class="trusted">
						<?php echo JText::_('Trusted Mode'); ?>
					</th>
					<th class="published">
						<?php echo $this->app->html->_('grid.sort', 'Published', 'state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="access">
						<?php echo $this->app->html->_('grid.sort', 'Access', 'access', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
				for ($i=0, $n=count($this->submissions); $i < $n; $i++) :

					$row = $this->submissions[$i];

					$img 	= $row->state ? 'tick.png' : 'publish_x.png';
					$task 	= $row->state ? 'unpublish' : 'publish';
					$alt 	= $row->state ? JText::_('Published') : JText::_('Unpublished');
					$action = $row->state ? JText::_('Unpublish submission') : JText::_('Publish submission');

					$types = array_map(create_function('$type', 'return $type->name;'), $row->getSubmittableTypes());

					// access
					$group = $this->app->zoo->getGroup($row->access);
					$group_access = JText::_($group->name);
					$public = !JAccess::checkGroup($group->id, 'core.login.site');

					// trusted mode
					$trusted_mode     = (int) $row->isInTrustedMode();
					$trusted_mode_img = $trusted_mode ? 'tick.png' : 'publish_x.png';
					$trusted_mode_alt = $trusted_mode ? JText::_('Trusted Mode enabled') : JText::_('Trusted Mode disabled');

					?>
					<tr>
						<td class="checkbox">
							<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
						</td>
						<td class="icon"></td>
						<td class="name">
							<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Submission');?>::<?php echo $row->name; ?>">
								<a href="<?php echo $this->app->link(array('controller' => $this->controller, 'changeapp' => $this->application->id, 'task' => 'edit', 'cid[]' => $row->id));  ?>"><?php echo $row->name; ?></a>
							</span>
						</td>
						<td class="types">
							<?php if (count($types)) : ?>
							<?php echo implode(', ', $types); ?>
							<?php else: ?>
							<span><?php echo JText::_('YOU WILL NEED AT LEAST ONE SUBMITTABLE TYPE FOR THIS SUBMISSION TO WORK'); ?></span>
							<?php endif; ?>
						</td>
						<td class="trusted">
							<?php if (!$public) : ?>
								<a href="#" rel="task-<?php echo $trusted_mode ? 'disabletrustedmode' : 'enabletrustedmode' ?>" title="<?php echo JText::_('Enable/Disable Trusted Mode');?>">
							<?php endif; ?>
									<img src="<?php echo $this->app->path->url('assets:images/'.$trusted_mode_img); ?>" border="0" alt="<?php echo $trusted_mode_alt; ?>" />
							<?php if (!$public) : ?>
								</a>
							<?php endif; ?>
						</td>
						<td class="published">
							<a href="#" rel="task-<?php echo $task; ?>" title="<?php echo $action; ?>">
								<img src="<?php echo $this->app->path->url('assets:images/'.$img); ?>" border="0" alt="<?php echo $alt; ?>" />
							</a>
						</td>
						<td class="access">
							<span><?php echo $group_access; ?></span>
						</td>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>

		<?php else :

				$title   = JText::_('NO_SUBMISSIONS_YET').'!';
				$message = JText::_('SUBMISSION_MANAGER_DESCRIPTION');
				echo $this->partial('message', compact('title', 'message'));

			endif;
		?>

	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
	<?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT;