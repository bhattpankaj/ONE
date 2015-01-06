<li <?php if(currentPage()=='globalsearch.php'){echo 'class="active"';}?>>
	<a href="<?php echo get_app_info('path').'/globalsearch?i='.$_GET['i'];?>">
    	<i class="icon-search  <?php if(currentPage()=='globalsearch.php'){
        	echo 'icon-white';}?>">
		</i> 
		<?php echo _('Global search');?>
	</a>
</li>