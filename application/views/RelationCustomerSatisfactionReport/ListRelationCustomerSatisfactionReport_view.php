<style>

	th{
		font-size:12px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>
<?php
	// echo form_open('RelationCustomerSatisfactionReport/previewreport'); 
	
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<script>
	base_url = '<?php echo base_url();?>';
	function reset_search(){
		document.location = base_url+"customer-satisfaction-report/reset-search";
	}
	
	function openform(){
		var a = document.getElementById("passwordf").style;
		if(a.display=="none"){
			a.display = "block";
		}else{
			a.display = "none";
		}
		// document.getElementById("code").style.display = "block";
		// document.getElementById("name").style.display = "block";
	}

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('customer-satisfaction-report/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
</script>
<?php
	$data=$this->session->userdata('filter-RelationCustomerSatisfactionReport');
	if(!is_array($data)){
		$data['start_date']		= date('d-m-Y');
		$data['end_date']		= date('d-m-Y');
		$data['branch_id']		= '';
	}

	$auth 	= $this->session->userdata('auth');
?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<div class = "page-bar">
			<ul class="page-breadcrumb">
				<li>
					<a href="<?php echo base_url();?>">
						Home
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>customer-satisfaction-report">
						Laporan Kepuasan Layanan
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Laporan Kepuasan Layanan
		</h3>
		<!-- END PAGE TITLE & BREADCRUMB-->
	
<?php echo form_open('customer-satisfaction-report/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box red">
			<div class="portlet-title">
				<div class="caption">
					Filter List
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
				
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body form">
					<div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"  value="<?php echo tgltoview($data['start_date']);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"  value="<?php echo tgltoview($data['end_date']);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group form-md-line-input">
															
								<?php
									echo form_dropdown('branch_id', $corebranch, set_value('branch_id', $data['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label class="control-label">Nama Cabang<span class="required">*</span></label>						
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box red">
			<div class="portlet-title">
				<div class="caption">
					List
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					
					
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_1">
						<thead>
							<tr>								
								<th width="0%"></th>
								<th width="5%">No</th>
								<?php
									if (!empty($relationcustomersatisfactionreport)){
										$array_key = array_keys($relationcustomersatisfactionreport[0]);

										$count = count($array_key);

										for($i=0; $i<$count; $i++){
											$index_array = $array_key[$i];

											echo "<th>
												".$index_array."
											</th>";
										}

										echo "<th>Total Tidak Puas</th>
											<th>Total Puas</th>
											<th>Total Kunjungan</th>";
									}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;

								if (!empty($relationcustomersatisfactionreport)){

									$count_satisfaction 	= count($relationcustomersatisfactionreport);

									$array_key 				= array_keys($relationcustomersatisfactionreport[0]);

									$count 					= count($array_key);

									

									for ($j=0; $j<$count_satisfaction; $j++){
										$total_visit 		= 0;
										$total_visit_yes 	= 0;
										$total_visit_no 	= 0;

										echo"
											<tr>
												<td></td>
												<td>".$no."</td>";

										for($i=0; $i<$count; $i++){

											$index_array = $array_key[$i];
											
											$array_value = $relationcustomersatisfactionreport[$j][$index_array];

											if ($array_value == ''){
												$array_value = 0;
											}

											if ($i > 0){
												$total_visit += $array_value;
											}
									

											$status = substr($index_array, -1);

											/* print_r("status ");
											print_r($status);
											print_r("<BR> "); */

											if ($status == "Y"){
												$total_visit_yes += $array_value;
											}
											
											if ($status == "N"){
												$total_visit_no += $array_value;
											}

											echo "	
												<td>".$array_value."</td>
											";
										}
										echo "	
												<td>".$total_visit_no."</td>
												<td>".$total_visit_yes."</td>
												<td>".$total_visit."</td>
											";
										echo "</tr>";

										$no++;
									}
								}
							?>
						</tbody>
					</table>
				</div>
				
				<BR>
				<BR>
				<div class="row">
					<div class="col-md-12 " style="text-align  : right !important;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>customer-satisfaction-report/export","_blank","top=100,left=200,width=300,height=300"));' class="btn green-jungle" title="Export to Excel">
							<i class="fa fa-file-excel-o"></i> Export Data
						</a>	
					</div>
				</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<?php echo form_close(); ?>