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
			<a href="<?php echo base_url();?>AcctDepositoDailyCashMutation">
				Mutasi Harian Setoran Simp Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Mutasi Harian Setoran Simp Berjangka
				</div>
			</div>
			

			<div class="portlet-body">
				<div class="form-body">

					<?php	echo form_open('AcctDepositoDailyCashMutation/processPrintingCashDeposit',array('id' => 'myform', 'class' => ''));  

						$auth = $this->session->userdata('auth');
					?>

					<div class="row">						
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Mulai Tanggal</label>
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo date('d-m-Y');?>"/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Sampai Tanggal</label>
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo date('d-m-Y');?>"/>
							</div>
						</div>
						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="row">
						<div class="col-md-12" style='text-align:right'>
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Cetak</i></button>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>