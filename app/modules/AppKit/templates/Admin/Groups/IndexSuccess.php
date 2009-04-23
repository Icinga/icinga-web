<?php 
	/**
	 * @var Doctrine_Collection
	 */
	$collection = $t['group_collection'];
	
	/**
	 * @var AppKitDoctrinePager
	 */
	$pager = $t['group_pager'];
?>

<p>
<strong>List of available groups</strong><br />
<span>(<?php echo AppKitHtmlHelper::Obj()->LinkToRoute('appkit.admin.groups.edit', 'Create a new group', array('id' => 'new'))?>)</span>
</p>

<div class="round_box" style="max-width: 600px;">
<div class="c1"></div><div class="c2"></div><div class="c3"></div><div class="c4"></div>
<div class="content">
	Please keep in mind to define privileges for newly created groups because there are just useless.<br />
	Take a look at <code><?php echo AgaviConfig::get('core.config_dir'); ?>/rbac_definitions.xml</code>
</div>
<div class="c4"></div><div class="c3"></div><div class="c2"></div><div class="c1"></div>
</div>

<?php if ($collection && $collection->count() > 0) { ?>
<table class="dataTable">
<tr>
	<th>&nbsp;</th>
	<th>Name</th>
	<th>Description</th>
	<th>Created</th>
	<th>&#160;</th>
</tr>

<?php foreach ($collection as $group) { ?>
<tr class="<?php echo AppKitHtmlHelper::Obj()->classAlternate('light', 'dark') ?>">
		<td><?php echo AppKitHtmlHelper::Obj()->LinkImageToRoute('appkit.admin.groups', 'Toggle activity', $group->role_disabled ? 'icons.cross' : 'icons.tick', array('id' => $group->role_id, 'toggleActivity' => true), array(), $rd) ?></td>
		<td><?php echo $group->role_name; ?></td>
		<td><?php echo $group->role_description; ?></td>
		<td><?php echo $group->role_created; ?></td>
		<td><?php echo AppKitHtmlHelper::Obj()->LinkImageToRoute('appkit.admin.groups.edit', 'Edit group', 'icons.group_edit', array('id' => $group->role_id)); ?></td>
</tr>
<?php } ?>

</table>

<?php $pager instanceof AppKitDoctrinePager ? $pager->displayLayout() : null; ?>

<?php } ?>