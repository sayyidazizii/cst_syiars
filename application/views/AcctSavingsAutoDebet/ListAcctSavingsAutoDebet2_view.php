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
			<a href="<?php echo base_url();?>AcctSavingsProfitSharingNew">
				Perhitungan Basil Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Basil Simpanan
</h3>
<?php echo form_open('AcctSavingsProfitSharingNew/processAddAcctSavingsProfitSharing',array('id' => 'myform', 'class' => 'horizontal-form')); 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Basil Simpanan
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="last_date" id="last_date" value="<?php echo date('d-m-Y');?>"/>
								<label class="control-label">Tanggal Diproses</label>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('savings_id', $acctsavings, set_value('savings_id'),'id="savings_id" class="form-control select2me" ');?>
								<label class="control-label">Jenis Simpanan</label>
							</div>
						</div>					
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								
								<input class="form-control"  type="text" name="savings_daily_average_balance_minimum" id="savings_daily_average_balance_minimum"/>
								<label class="control-label">SRH Minimal</label>
								
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Proses</i></button>
						</div>	
					</div>
						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>