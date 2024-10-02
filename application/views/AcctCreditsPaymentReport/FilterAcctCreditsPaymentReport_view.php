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
	
</style>
<script type="text/javascript">
	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctCreditsPaymentReport/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
</script>
<div class="row-fluid">
	

			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">	
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctCreditsPaymentReport/duedate">
				Daftar Angsuran Anggota per Tanggal
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Angsuran Anggota per Tanggal<small> Kelola Daftar Angsuran Anggota per Tanggal</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
	$sesi = $this->session->userdata('unique');
	$data = $this->session->userdata('filterpayment-'.$sesi['unique']);

	if(empty($data['start_date'])){
		$data['start_date'] = date('d-m-Y');
	}
	if(empty($data['branch_id'])){
		$data['branch_id'] = $auth['branch_id'];
	}
?>	
<?php	echo form_open('AcctCreditsPaymentReport/processPrinting2',array('id' => 'myform', 'class' => '')); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<!-- <div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div> -->
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="<?php echo $data['start_date'];?>" autocomplate="off"/>
								<label class="control-label">Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-print"></i> Cetak</button>
							<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctCreditsPaymentReport/export","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn green-jungle"><i class="fa fa-download"></i> Export Data</a>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
