<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo $this->app->link(array('controller' => $this->controller)); ?>" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<ul class="filter">
		<li class="filter-left">
			<?php echo JText::_('Filter'); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="rounded" />
			<button onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
		</li>
        <?php if ($this->app->joomla->version->isCompatible('3.0') && !$this->pagination instanceof AppPagination) : ?>
            <li class="filter-right">
                <?php echo str_replace(array('input-mini', 'size="1"'), '', $this->pagination->getLimitBox()); ?>
            </li>
        <?php endif ?>
		<li class="filter-right">
			<?php echo $this->lists['select_category'];?>
		</li>
		<?php if (count($this->type_filter) > 1) : ?>
		<li class="filter-right">
			<?php echo $this->lists['select_type'];?>
		</li>
		<?php endif; ?>
		<li class="filter-right">
			<?php echo $this->lists['select_author'];?>
		</li>
	</ul>

	<table id="actionlist" class="list stripe">
		<thead>
			<tr>
				<?php if (!$this->pagination instanceof AppPagination) : ?>
				<th width="5">
					<?php echo JText::_('NUM'); ?>
				</th>
				<?php endif; ?>
				<th width="20">
					&nbsp;
				</th>
				<th>
					<?php echo $this->app->html->_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="10%">
					<?php echo $this->app->html->_('grid.sort', 'Type', 'a.type', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="8%">
					<?php echo $this->app->html->_('grid.sort', 'Access', 'a.access', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="8%">
					<?php echo $this->app->html->_('grid.sort', 'Author', 'a.created_by', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="10" align="center">
					<?php echo $this->app->html->_('grid.sort', 'Date', 'a.created', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="10" align="center">
					<?php echo $this->app->html->_('grid.sort', 'Hits', 'a.hits', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="1%">
					<?php echo $this->app->html->_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php
					if ($this->pagination instanceof AppPagination) {
						$pagination_link = $this->app->link(array(
							'option' => $this->option,
							'controller' => $this->controller,
							'task' => 'element',
							'tmpl' => 'component',
							'filter_order' => $this->lists['order'],
							'filter_order_Dir' => $this->lists['order_Dir'],
							'object' => $this->app->request->getVar('object'),
							'func' => $this->app->request->getVar('func', 'jSelectArticle'),
							'app_id' => $this->application->id,
							'item_filter' => $this->filter_item	,
							'type_filter' => $this->type_filter
						));
						echo $this->pagination->render($pagination_link);
					} else {
						echo $this->pagination->getListFooter();
					}
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for ($i = 0, $n = count($this->items); $i < $n; $i++) {
			$row    = &$this->items[$i];

			// author
			$author = $row->created_by_alias;
			if (!$author && isset($this->users[$row->created_by])) {
				$author = $this->users[$row->created_by]->name;
			}
		?>
			<tr class="<?php echo "row$k"; ?>">
				<?php if (!$this->pagination instanceof AppPagination) : ?>
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<?php endif; ?>
				<td align="center">
					<img src="<?php echo $this->app->path->url('assets:images/page_white.png'); ?>" alt="page_white.png" border="0" />
				</td>
				<td>
					<a style="cursor: pointer;" onclick="window.parent.<?php echo $this->app->request->getVar('func', 'jSelectArticle'); ?>('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->name); ?>', '<?php echo $this->app->request->getVar('object'); ?>');">
						<?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</td>
				<td>
					<?php echo $this->application->getType($row->type)->name; ?>
				</td>
			<td align="center">
				<?php echo JText::_($this->app->zoo->getGroup($row->access)->name); ?>
			</td>
			<td>
				<?php echo $author; ?>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->app->html->_('date', $row->created, JText::_('DATE_FORMAT_LC4'), $this->app->date->getOffset()); ?>
			</td>
			<td nowrap="nowrap" align="center">
				<?php echo $row->hits ?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="element" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="object" value="<?php echo $this->app->request->getVar('object'); ?>" />
	<input type="hidden" name="func" value="<?php echo $this->app->request->getVar('func', 'jSelectArticle'); ?>" />
	<input type="hidden" name="app_id" value="<?php echo $this->application->id; ?>" />
	<?php foreach($this->type_filter as $type_filter) : ?>
		<input type="hidden" name="type_filter[]" value="<?php echo $type_filter; ?>" />
	<?php endforeach; ?>
	<input type="hidden" name="item_filter" value="<?php echo $this->filter_item; ?>" />
	<?php echo $this->app->html->_('form.token'); ?>

</form>