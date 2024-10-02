
<script type="text/javascript">
	base_url = '<?php echo base_url();?>';
		/*  setTimeout(function(){
       location.reload();
   },18000); */

   function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('management-dashboard/state-add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}
</script>
<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}

	#chartdivpie {
		width: 100%;
		height: 250px;
	}

	#chartdivcolumn {
		width: 100%;
		height: 250px;
	}

	#chartdivItem {
		width: 100%;
		height: 250px;
	}

	#chartdivCustomer {
		width: 100%;
		height: 250px;
	}

	#chartdivcolumnomzet {
		width: 100%;
		height: 500px;
	}
</style>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<?php 
	$unique 			= $this->session->userdata('unique');
	$state 				= $this->session->userdata('ManagementDashboardState-'.$unique['unique']);
?>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?php echo base_url();?>">
				Home
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>management-dashboard">
				Dashboard
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<!-- <h3 class="page-title">
	Dashboard
</h3> -->



<div class="row widget-row">
	<div class="col-md-12">
		<div class="tabbable-line boxless tabbable-reversed ">
			<ul class="nav nav-tabs">
				<?php
					if($state['active_tab']=="" || $state['active_tab']=="itemdata"){
						$tabmemberdata = "<li class='active'><a href='#tabitemdata' name='itemdata' data-toggle='tab' onClick='function_state_add(this.name);'><b>Keanggotaan </b></a></li>";
					}else{
						$tabmemberdata = "<li><a href='#tabitemdata' data-toggle='tab' name='itemdata' onClick='function_state_add(this.name);'><b>Keanggotaan </b></a></li>";
					}

					if($state['active_tab']=="itempacking"){
						$tabitempacking = "<li class='active'><a href='#tabitempacking' name='itempacking' data-toggle='tab' onClick='function_state_add(this.name)'><b>Salesman Performance</b></a></li>";
					}else{
						$tabitempacking = "<li><a href='#tabitempacking' data-toggle='tab' name='itempacking' onClick='function_state_add(this.name)'><b>Salesman Performance</b></a></li>";
					}
					
					echo $tabmemberdata;
					/* echo $tabitempacking; */
				?>
			</ul>
			<div class="tab-content">
				<?php
					if($state['active_tab']=="" || $state['active_tab']=="itemdata"){
						$statitemdata = "active";
					}else{
						$statitemdata = "";
					}

					if($state['active_tab']=="itempacking"){
						$statitempacking = "active";
					}else{
						$statitempacking = "";
					}

					echo"<div class='tab-pane ".$statitemdata."' id='tabitemdata'>";
						$this->load->view("ManagementDashboard/ManagementDashboardMember_view");
					echo"</div>";

					echo"<div class='tab-pane ".$statitempacking."' id='tabitempacking'>";
						$this->load->view("ManagementDashboard/ManagementDashboardSalesman_view");
					echo"</div>";
				?>
			</div>
		</div>
	</div>
</div>