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
$this->app->document->addScript('assets:js/item.js');

?>

<form class="items-default" action="<?php echo $this->app->link(); ?>" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<?php if ($this->is_filtered || $this->pagination->total > 0) :?>

		<ul class="filter">
			<li class="filter-left">
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="rounded" />
				<button onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
			</li>
            <?php if ($this->app->joomla->version->isCompatible('3.0')) : ?>
            <li class="filter-right">
                <?php echo str_replace(array('input-mini', 'size="1"'), '', $this->pagination->getLimitBox()); ?>
            </li>
            <?php endif ?>
			<li class="filter-right">
				<?php echo $this->lists['select_category'];?>
			</li>
			<li class="filter-right">
				<?php echo $this->lists['select_type'];?>
			</li>
			<li class="filter-right">
				<?php echo $this->lists['select_author'];?>
			</li>
		</ul>

	<?php endif;

	if($this->pagination->total > 0) : ?>

		<table class="list stripe">
			<thead>
				<tr>
					<th class="checkbox">
						<input type="checkbox" class="check-all" />
					</th>
					<th class="name" colspan="2">
						<?php echo $this->app->html->_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="type">
						<?php echo $this->app->html->_('grid.sort', 'Type', 'a.type', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="published">
						<?php echo $this->app->html->_('grid.sort', 'Published', 'a.state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="frontpage">
						<?php echo JText::_('Frontpage'); ?>
					</th>
					<th class="searchable">
						<?php echo JText::_('Searchable'); ?>
					</th>
					<th class="comments">
						<?php echo JText::_('Comments'); ?>
					</th>
					<th class="priority">
						<?php echo $this->app->html->_('grid.sort', 'Order Priority', 'a.priority', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="access">
						<?php echo $this->app->html->_('grid.sort', 'Access', 'a.access', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="author">
						<?php echo $this->app->html->_('grid.sort', 'Author', 'a.created_by', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="date">
						<?php echo $this->app->html->_('grid.sort', 'Date', 'a.created', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="hits">
						<?php echo $this->app->html->_('grid.sort', 'Hits', 'a.hits', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$nullDate = $this->app->database->getNullDate();
			for ($i=0, $n=count($this->items); $i < $n; $i++) :

				$row		  = $this->items[$i];
				$now		  = $this->app->date->create()->toUnix();
				$publish_up   = $this->app->date->create($row->publish_up);
				$publish_down = $this->app->date->create($row->publish_down);
				$offset		  = $this->app->date->getOffset();
				$publish_up->setTimezone(new DateTimeZone($offset));
				$publish_down->setTimezone(new DateTimeZone($offset));

				$img = '';
				$alt = '';
				if ($now <= $publish_up->toUnix() && $row->state == 1) {
					$img = 'publish_y.png';
					$alt = JText::_('Published');
				} else if (($now <= $publish_down->toUnix() || $row->publish_down == $nullDate) && $row->state == 1 ) {
					$img = 'publish_g.png';
					$alt = JText::_('Published');
				} else if ($now > $publish_down->toUnix() && $row->state == 1) {
					$img = 'publish_r.png';
					$alt = JText::_('Expired');
				} else if ($row->state == 0) {
					$img = 'publish_x.png';
					$alt = JText::_('Unpublished');
				}

				if ($row->searchable == 0) {
					$search_img = 'publish_x.png';
					$search_alt = JText::_('None searchable');
				} elseif ($row->searchable == 1) {
					$search_img = 'tick.png';
					$search_alt = JText::_('Searchable');
				}

				if ($row->frontpage) {
					$frontpage_img = 'tick.png';
					$frontpage_alt = JText::_('JYES');
				} else {
					$frontpage_img = 'publish_x.png';
					$frontpage_alt = JText::_('JNO');
				}

				$comments_enabled = (int) $row->getParams()->get('config.enable_comments', 1);
				$comments_img 	  = $comments_enabled ? 'tick.png' : 'publish_x.png';
				$comments_alt 	  = $comments_enabled ? JText::_('Comments enabled') : JText::_('Comments disabled');

				$times = '';

				if (isset($row->publish_up)) {
					if ($row->publish_up == $nullDate) {
						$times .= JText::_( 'Start: Always' );
					} else {
						$times .= JText::_( 'Start' ) .": ". $publish_up->format('Y-m-d H:i:s', true);
					}
				}

				if (isset($row->publish_down)) {
					if ($row->publish_down == $nullDate) {
						$times .= "<br />". JText::_( 'Finish No Expiry' );
					} else {
						$times .= "<br />". JText::_( 'Finish' ) .": ". $publish_down->format('Y-m-d H:i:s', true);
					}
				}

				// author
				$author = $row->created_by_alias;
				if (!$author) {
					if (isset($this->users[$row->created_by])) {
						$author = $this->users[$row->created_by]->name;

						if ($this->app->user->get()->authorise('core.edit', 'com_users')) {
							$author = '<a href="'.$this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $row->created_by)).'" title="'.JText::_('Edit User').'">'. $author.'</a>';
						}
					} else {
						$author = JText::_('Guest');
					}
				}
			?>
				<tr>
					<td class="checkbox">
						<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
					</td>
					<td class="icon"></td>
					<td class="name">
						<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Item');?>::<?php echo $row->name; ?>">
							<a href="<?php echo $this->app->link(array('controller' => $this->controller, 'changeapp' => $this->application->id, 'task' => 'edit', 'cid[]' => $row->id));  ?>"><?php echo $row->name; ?></a>
						</span>
					</td>
					<td class="type">
						<?php echo $this->application->getType($row->type)->name; ?>
					</td>
					<td class="published">
						<span class="editlinktip hasTip" title="<?php echo JText::_('Publish Information');?>::<?php echo $times; ?>">
							<a href="#" rel="task-<?php echo $row->state ? 'unpublish' : 'publish'; ?>">
								<img src="<?php echo $this->app->path->url('assets:images/'.$img) ;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
							</a>
						</span>
					</td>
					<td class="frontpage">
						<a href="#" rel="task-<?php echo 'toggleFrontpage'; ?>" title="<?php echo JText::_('Toggle frontpage state');?>">
							<img src="<?php echo $this->app->path->url('assets:images/'.$frontpage_img); ?>" width="16" height="16" border="0" alt="<?php echo $frontpage_alt; ?>" />
						</a>
					</td>
					<td class="searchable">
						<a href="#" rel="task-<?php echo $row->searchable ? 'makenonesearchable' : 'makesearchable'; ?>" title="<?php echo JText::_('Edit searchable state');?>">
							<img src="<?php echo $this->app->path->url('assets:images/'.$search_img); ?>" width="16" height="16" border="0" alt="<?php echo $search_alt; ?>" />
						</a>
					</td>
					<td class="comments">
						<a href="#" rel="task-<?php echo $comments_enabled ? 'disablecomments' : 'enablecomments'; ?>" title="<?php echo JText::_('Enable/Disable comments');?>">
							<img src="<?php echo $this->app->path->url('assets:images/'.$comments_img); ?>" width="16" height="16" border="0" alt="<?php echo $comments_alt; ?>" />
						</a>
					</td>
					<td class="priority">
						<span class="minus"></span>
						<input type="text" class="value" value="<?php echo $row->priority; ?>" size="5" name="priority[<?php echo $row->id; ?>]"/>
						<span class="plus"></span>
					</td>
					<td class="access">
						<span><?php echo JText::_($this->app->zoo->getGroup($row->access)->name); ?></span>
					</td>
					<td class="author">
						<?php echo $author; ?>
					</td>
					<td class="date">
						<?php echo $this->app->html->_('date', $row->created, JText::_('DATE_FORMAT_LC4'), $this->app->date->getOffset()); ?>
					</td>
					<td class="hits">
						<?php echo $row->hits ?>
					</td>
				</tr>
				<?php endfor; ?>
			</tbody>
		</table>

	<?php elseif($this->is_filtered) :

			$title   = JText::_('SEARCH_NO_ITEMS').'!';
			$message = null;
			echo $this->partial('message', compact('title', 'message'));

		else :

			$title   = JText::_('NO_ITEMS_YET').'!';
			$message = JText::_('ITEM_MANAGER_DESCRIPTION');
			echo $this->partial('message', compact('title', 'message'));

		endif;
	?>

</div>

<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').BrowseItems();
	});
</script>

<?php echo ZOO_COPYRIGHT;