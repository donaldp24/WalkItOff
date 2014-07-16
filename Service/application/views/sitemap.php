<?php foreach($urls as $info => $url) {
	$infos = explode("|", $info);
	$ismenu = is_array($url);
?>
    <li class="<?php if ($infos[0] == $mainactive) { ?>active<?php } ?>  <?php if ($infos[0] == $mainactive && $ismenu) { ?>open<?php } ?>">
		<a href="<?php echo base_url().$url ?>" class="<?php if ($ismenu) { ?>dropdown-toggle<?php } ?>">
			<i class="<?php echo $infos[1]; ?>"></i>
			<span class="menu-text"> <?php echo $infos[0]; ?> </span>
			<?php if ($ismenu) { ?>
			<b class="arrow icon-angle-down"></b>
			<?php } ?>
		</a>
		<?php if ($ismenu) { ?>
		<ul class="submenu">
			<?php foreach($url as $subtitle => $suburl) {
			$subinfos = explode("|", $subtitle); ?>
			<li class="<?php if ($subinfos[0] == $subactive) { ?>active<?php } ?>">
				<a href="<?php echo base_url().$suburl ?>">
					<i class="<?php echo $subinfos[1]; ?>"></i>
					<?php echo $subinfos[0]; ?>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
    </li>
<?php } ?>