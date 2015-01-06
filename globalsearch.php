<?php
include('includes/header.php');
include('includes/login/auth.php');
include('includes/globalsearch/main.php');
include('includes/helpers/short.php');

// Get all lists associated with the current app ($_GET['i']).


$q = 'SELECT `id` FROM `lists` WHERE `app` = ' . mysqli_real_escape_string($mysqli, $_GET['i']);
$r = mysqli_query($mysqli, $q);

$listIds = '';
if ($r && mysqli_num_rows($r) > 0) {
	while($row = mysqli_fetch_object($r)) {
		$listIds .= $row->id . ',';
	}
}

$listIds = substr($listIds,0,strlen($listIds)-1);

if(get_app_info('is_sub_user')) {
	if(get_app_info('app')!=get_app_info('restricted_to_app'))
	{
		echo '<script type="text/javascript">window.location="'.get_app_info('path').'/list?i='.get_app_info('restricted_to_app').'"</script>';
		exit;
	}
	$q = 'SELECT app FROM lists WHERE id IN ('. $listIds . ')';
	$r = mysqli_query($mysqli, $q);
	if ($r)
	{
	    while($row = mysqli_fetch_array($r))
	    {
			$a = $row['app'];
	    }  
	    if($a!=get_app_info('restricted_to_app'))
	    {
		    echo '<script type="text/javascript">window.location="'.get_app_info('path').'/list?i='.get_app_info('restricted_to_app').'"</script>';
			exit;
	    }
	}
}


//vars
if(isset($_GET['s'])) $s = trim($_GET['s']);
else $s = '';
if(isset($_GET['c'])) $c = $_GET['c'];
else $c = '';
if(isset($_GET['p'])) $p = $_GET['p'];
else $p = '';
if(isset($_GET['a'])) $a = $_GET['a'];
else $a = '';
if(isset($_GET['u'])) $u = $_GET['u'];
else $u = '';
if(isset($_GET['b'])) $b = $_GET['b'];
else $b = '';
if(isset($_GET['cp'])) $cp = $_GET['cp'];
else $cp = '';


?>
<div class="row-fluid">
    <div class="span2">
        <?php include('includes/sidebar.php');?>
    </div> 
    <div class="span10">
    	<div>
	    	<p class="lead"><?php echo get_app_data('app_name');?></p>
    	</div>
    	<h2><?php echo _('Global search');?></h2> <br/>
    	<?php 
    		//export according to which section user is on
    		if($a=='' && $c=='' && $u=='' && $b=='' && $cp=='')
    		{
	    		$filter = '';
	    		$filter_val = '';
	    		$export_title = _('all subscribers');
    		}
    		else if($a!='')
    		{
	    		$filter = 'a';
	    		$filter_val = $a;
	    		$export_title = _('active subscribers');
    		}
    		else if($c!='')
    		{
	    		$filter = 'c';
	    		$filter_val = $c;
	    		$export_title = _('unconfirmed subscribers');
    		}  
    		else if($u!='')
    		{
	    		$filter = 'u';
	    		$filter_val = $u;
	    		$export_title = _('unsubscribers');
    		} 
    		else if($b!='')
    		{
	    		$filter = 'b';
	    		$filter_val = $b;
	    		$export_title = _('bounced subscribers');
    		}
    		else if($cp!='')
    		{
	    		$filter = 'cp';
	    		$filter_val = $cp;
	    		$export_title = _('subscribers who marked your email as spam');
    		}     	
    	?>
    	<button class="btn" onclick="window.location='<?php echo get_app_info('path');?>/includes/subscribers/export-csv.php?i=<?php echo get_app_info('app');?>&l=<?php echo $lid;?>&<?php echo $filter.'='.$filter_val;?>'"><i class="icon-download-alt"></i> <?php echo _('Export').' '.$export_title;?></button>
    	<form class="form-search" action="<?php echo get_app_info('path');?>/subscribers" method="GET" style="float:right;">
    		<input type="hidden" name="i" value="<?php echo get_app_info('app');?>">
    		<input type="hidden" name="l" value="all">
    		<?php if($a!=''):?>
    		<input type="hidden" name="a" value="<?php echo $a;?>">
    		<?php elseif($c!=''):?>
    		<input type="hidden" name="c" value="<?php echo $c;?>">
    		<?php elseif($u!=''):?>
    		<input type="hidden" name="u" value="<?php echo $u;?>">
    		<?php elseif($b!=''):?>
    		<input type="hidden" name="b" value="<?php echo $b;?>">
    		<?php elseif($cp!=''):?>
    		<input type="hidden" name="cp" value="<?php echo $cp;?>">
    		<?php endif;?>
			<input type="text" class="input-medium search-query" name="s">
			<button type="submit" class="btn"><i class="icon-search"></i> <?php echo _('Search globally');?></button>
		</form>
		<div class="row-fluid">
		    <div class="span12">				
				<ul class="nav nav-tabs">
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>" id="all"><?php echo _('All');?> <span class="badge badge-info"><?php echo get_totals('', '', $listIds);?></span></a></li>
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>&a=1" id="active"><?php echo _('Active');?> <span class="badge badge-success"><?php echo get_totals('a', '', $listIds);?></span></a></li>
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>&c=0" id="unconfirmed"><?php echo _('Unconfirmed');?> <span class="badge"><?php echo get_totals('confirmed', 0, $listIds);?></span></a></li>
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>&u=1" id="unsubscribed"><?php echo _('Unsubscribed');?> <span class="badge badge-important"><?php echo get_totals('unsubscribed', 1, $listIds);?></span></a></li>
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>&b=1" id="bounced"><?php echo _('Bounced');?> <span class="badge badge-inverse"><?php echo get_totals('bounced', 1, $listIds);?></span></a></li>
				  <li><a href="<?php echo get_app_info('path')?>/globalsearch?i=<?php echo $_GET['i']?>&cp=1" id="complaint"><?php echo _('Marked as spam');?> <span class="badge badge-inverse"><?php echo get_totals('complaint', 1, $listIds);?></span></a></li>
				</ul>
		    </div>
	    </div>
		<script type="text/javascript">
			$(document).ready(function() {
				<?php if($a=='' && $c=='' && $u=='' && $b=='' && $cp==''):?>
				$("#all").addClass("tab-active");
				<?php elseif($a!=''):?>
				$("#active").addClass("tab-active");
				<?php elseif($c!=''):?>
				$("#unconfirmed").addClass("tab-active");
				<?php elseif($u!=''):?>
				$("#unsubscribed").addClass("tab-active");
				<?php elseif($b!=''):?>
				$("#bounced").addClass("tab-active");
				<?php elseif($cp!=''):?>
				$("#complaint").addClass("tab-active");
				<?php endif;?>
				
				$("#single").click(function(){
					$("#opt_in").val("0");
				});
				$("#double").click(function(){
					$("#opt_in").val("1");
				});
			});
		</script>
		
	    <table class="table table-striped table-condensed responsive">
		  <thead>
		    <tr>
		      <th><?php echo _('Name');?></th>
		      <th><?php echo _('Email');?></th>
		      <th><?php echo _('Last activity');?></th>
		      <th><?php echo _('Status');?></th>
		      <th><?php echo _('Unsubscribe');?></th>
		      <th><?php echo _('Delete');?></th>
		    </tr>
		  </thead>
		  <tbody>
		  	
		  	<?php 	  			
		  		$limit = 20;
				$total_subs = totalsByMultiple($listIds);
				$total_pages = ceil($total_subs/$limit);
				
				if($p!=null)
				{
					$offset = ($p-1) * $limit;
				}
				else
					$offset = 0;
		  		
		  		if($s=='')
		  		{
		  			if($a=='' && $c=='' && $u=='' && $b=='' && $cp=='')
		  				$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
		  			else if($a!='')
			  			$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND confirmed = 1 AND unsubscribed = 0 AND bounced = 0 AND complaint = 0 ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
		  			else if($c!='')
			  			$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND confirmed = '.$c.' ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
			  		else if($u!='')
			  			$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND unsubscribed = '.$u.' AND bounced = 0 ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
			  		else if($b!='')
			  			$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND bounced = '.$b.' ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
			  		else if($cp!='')
			  			$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND complaint = '.$cp.' ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
				}
				else
				{
					if($a=='' && $c=='' && $u=='' && $b=='' && $cp=='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
					else if($a!='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND confirmed = 1 AND unsubscribed = 0 AND bounced = 0 AND complaint = 0 AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
					else if($c!='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND confirmed = '.$c.' AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
					else if($u!='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND unsubscribed = '.$u.' AND bounced = 0 AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
					else if($b!='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND bounced = '.$b.' AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
					else if($cp!='')
						$q = 'SELECT * FROM subscribers WHERE list IN ('. $listIds .') AND complaint = '.$cp.' AND (name LIKE "%'.$s.'%" OR email LIKE "%'.$s.'%" OR custom_fields LIKE "%'.$s.'%") ORDER BY timestamp DESC LIMIT '.$offset.','.$limit;
				}
			  	$r = mysqli_query($mysqli, $q);
			  	if ($r && mysqli_num_rows($r) > 0)
			  	{
			  	    while($row = mysqli_fetch_array($r))
			  	    {
			  			$id = $row['id'];
			  			$name = stripslashes($row['name']);
			  			$email = stripslashes($row['email']);
			  			$unsubscribed = $row['unsubscribed'];
			  			$bounced = $row['bounced'];
			  			$complaint = $row['complaint'];
			  			$confirmed = $row['confirmed'];
			  			$timestamp = parse_date($row['timestamp'], 'short', true);
			  			if($unsubscribed==0)
			  				$unsubscribed = '<span class="label label-success">'._('Subscribed').'</span>';
			  			else if($unsubscribed==1)
			  				$unsubscribed = '<span class="label label-important">'._('Unsubscribed').'</span>';
			  			if($bounced==1)
				  			$unsubscribed = '<span class="label label-inverse">'._('Bounced').'</span>';
				  		if($complaint==1)
				  			$unsubscribed = '<span class="label label-inverse">'._('Marked as spam').'</span>';
				  		if($confirmed==0)
			  				$unsubscribed = '<span class="label">'._('Unconfirmed').'</span>';
				  			
				  		if($name=='')
				  			$name = '['._('No name').']';
			  			
			  			echo '
			  			
			  			<tr id="'.$id.'">
			  			  <td><a href="#subscriber-info" data-id="'.$id.'" data-toggle="modal" class="subscriber-info">'.$name.'</a></td>
					      <td><a href="#subscriber-info" data-id="'.$id.'" data-toggle="modal" class="subscriber-info">'.$email.'</a></td>
					      <td>'.$timestamp.'</td>
					      <td id="unsubscribe-label-'.$id.'">'.$unsubscribed.'</td>
					      <td>
					    ';
					    
					    if($row['unsubscribed']==0)
							$action_icon = '
								<a href="javascript:void(0)" title="'._('Unsubscribe').' '.$email.'" data-action'.$id.'="unsubscribe" id="unsubscribe-btn-'.$id.'">
									<i class="icon icon-ban-circle"></i>
								</a>
								';
						else if($row['unsubscribed']==1)
							$action_icon = '
								<a href="javascript:void(0)" title="'._('Resubscribe').' '.$email.'" data-action'.$id.'="resubscribe" id="unsubscribe-btn-'.$id.'">
									<i class="icon icon-ok"></i>
								</a>
							';
						if($row['bounced']==1 || $row['complaint']==1)
							$action_icon = '
								-
							';
						if($row['confirmed']==0)
							$action_icon = '
								<a href="javascript:void(0)" title="'._('Confirm').' '.$email.'" data-action'.$id.'="confirm" id="unsubscribe-btn-'.$id.'">
									<i class="icon icon-ok"></i>
								</a>
							';
						
						echo $action_icon;
					    
					    echo'
					      </td>
					      <td><a href="javascript:void(0)" title="Delete '.$email.'?" id="delete-btn-'.$id.'" class="delete-subscriber"><i class="icon icon-trash"></i></a></td>
					      <script type="text/javascript">
					    	$("#delete-btn-'.$id.'").click(function(e){
								e.preventDefault(); 
								c = confirm("'._('Confirm delete').' '.$email.'?");
								if(c)
								{
									$.post("includes/subscribers/delete.php", { subscriber_id: '.$id.' },
									  function(data) {
									      if(data)
									      {
									      	$("#'.$id.'").fadeOut();
									      }
									      else
									      {
									      	alert("'._('Sorry, unable to delete. Please try again later!').'");
									      }
									  }
									);
								}
							});
							$("#unsubscribe-btn-'.$id.'").click(function(e){
								e.preventDefault(); 
								action = $("#unsubscribe-btn-'.$id.'").data("action'.$id.'");
								$.post("includes/subscribers/unsubscribe.php", { subscriber_id: '.$id.', action: action},
								  function(data) {
								      if(data)
								      {
								      	if($("#unsubscribe-label-'.$id.'").text()=="'._('Subscribed').'")
								      	{
								      		$("#unsubscribe-btn-'.$id.'").html("<li class=\'icon icon-ok\'></li>");
								      		$("#unsubscribe-btn-'.$id.'").data("action'.$id.'", "resubscribe");
									      	$("#unsubscribe-label-'.$id.'").html("<span class=\'label label-important\'>'._('Unsubscribed').'</span>");
									    }
									    else
									    {
									    	$("#unsubscribe-btn-'.$id.'").html("<li class=\'icon icon-ban-circle\'></li>");
								      		$("#unsubscribe-btn-'.$id.'").data("action'.$id.'", "unsubscribe");
									      	$("#unsubscribe-label-'.$id.'").html("<span class=\'label label-success\'>'._('Subscribed').'</span>");
									    }
									    if($("#unsubscribe-label-'.$id.'").text()=="'._('Unconfirmed').'")
									    {
									    	$("#unsubscribe-btn-'.$id.'").html("<li class=\'icon icon-ban-circle\'></li>");
								      		$("#unsubscribe-btn-'.$id.'").data("action'.$id.'", "confirm");
									      	$("#unsubscribe-label-'.$id.'").html("<span class=\'label label-success\'>'._('Subscribed').'</span>");
									    }
								      }
								      else
								      {
								      	alert("'._('Sorry, unable to unsubscribe. Please try again later!').'");
								      }
								  }
								);
							});
							</script>
					    </tr>
						
			  			';
			  	    }  
			  	}
			  	else
			  	{
			  		echo '
			  			<tr>
			  				<td>'._('No subscribers found.').'</td>
			  				<td></td>
			  				<td></td>
			  				<td></td>
			  				<td></td>
			  				<td></td>
			  			</tr>
			  		';
			  	}
		  	?>
		    
		  </tbody>
		</table>
		<?php paginationMultiple($limit, $listIds); ?>
    </div>
</div>



<!-- Subscriber info card -->
<div id="subscriber-info" class="modal hide fade">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3><?php echo _('Subscriber info');?></h3>
    </div>
    <div class="modal-body">
	    <p id="subscriber-text"></p>
    </div>
    <div class="modal-footer">
      <a href="#" class="btn btn-inverse" data-dismiss="modal"><i class="icon icon-ok-sign" style="margin-top: 5px;"></i> <?php echo _('Close');?></a>
    </div>
  </div>
<script type="text/javascript">
	$(".subscriber-info").click(function(){
		s_id = $(this).data("id");
		$("#subscriber-text").html("<?php echo _('Fetching');?>..");
		
		$.post("<?php echo get_app_info('path');?>/includes/subscribers/subscriber-info.php", { id: s_id, app:<?php echo get_app_info('app');?> },
		  function(data) {
		      if(data)
		      {
		      	$("#subscriber-text").html(data);
		      }
		      else
		      {
		      	$("#subscriber-text").html("<?php echo _('Oops, there was an error getting the subscriber\'s info. Please try again later.');?>");
		      }
		  }
		);
	});
</script>

<?php include('includes/footer.php');?>
