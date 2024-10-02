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
			<a href="<?php echo base_url();?>AcctLedgerReport/cashTellerReport">
				Laporan Arus Kas Harian
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Laporan Arus Kas Harian<small> Kelola Laporan Arus Kas Harian</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
	$sesi 	= $this->session->userdata('filter-AcctLedgerReportTeller');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
	}

	if(empty($sesi['branch_id'])){
		$sesi['branch_id']		= $auth['branch_id'];
	}
?>	
<?php	echo form_open('AcctLedgerReport/filtercash',array('id' => 'myform', 'class' => '')); 
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
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($sesi['start_date']);?>" autocomplate="off"/>
								<label class="control-label">Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id', $sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
						</div>	
					</div>
				</div>
			</div>

			<?php echo form_close(); ?>

			<div class="portlet-body">
					<div class="form-body">
						<?php echo form_open('AcctGeneralLedgerReport/processPrinting'); ?>
						
						<table class="table table-striped table-bordered table-hover table-full-width">
						<thead>
							<tr>
								<th width="5%" rowspan="2">No</th>
								<th width="10%" rowspan="2">Tanggal</th>
								<th width="10%" rowspan="2">No. Jurnal</th>
								<th width="20%" rowspan="2">Deskripsi</th>
								<th width="10%" rowspan="2">Nama Perkiraan</th>
								<th width="10%" rowspan="2">Debet</th>
								<th width="10%" rowspan="2">Kredit</th>
								<th width="30%" colspan="2">Saldo</th>
							</tr>
							<tr>
								<th width="15%">Debet</th>
								<th width="15%">Kredit</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="5" align="center"><b> Saldo Awal</b></td>
								<td></td>
								<td></td>
								<?php 
		
										if($opening_balance >= 0){
											echo "
												<td style='text-align: right'>".number_format($opening_balance, 2)."</td>
												<td style='text-align: right'>0.00</td>
											";
										} else {
											echo "
												<td style='text-align: right'>0.00</td>
												<td style='text-align: right'>".number_format($opening_balance, 2)."</td>
											";
										}
									
									
								?>
								
								
							</tr>
							<?php
								$no = 1;
								$last_balance_debet 	= 0;
								$last_balance_credit	= 0;
								$total_debet 			= 0;
								$total_kredit 			= 0;
								if(!empty($AcctGeneralLedgerReport)){	
									foreach ($AcctGeneralLedgerReport as $key=>$val){	
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:center'>".tgltoview($val['transaction_date'])."</td>
												<td>".$val['transaction_no']."</td>
												<td>".$val['transaction_description']."</td>
												<td>".$val['account_name']."</td>
												<td style='text-align:right'>".number_format($val['account_in'], 2)."</td>
												<td style='text-align:right'>".number_format($val['account_out'], 2)."</td>
												<td style='text-align:right'>".number_format($val['last_balance_debet'], 2)."</td>
												<td style='text-align:right'>".number_format($val['last_balance_credit'], 2)."</td>
											</tr>
										";
										$no++;

										$last_balance_debet 	= $val['last_balance_debet'];
										$last_balance_credit 	= $val['last_balance_credit'];

										$total_debet 			+= $val['account_in'];
										$total_kredit			+= $val['account_out'];
									} 
								} else {
						
										if($opening_balance >= 0){
											$last_balance_debet 	= $opening_balance;
											$last_balance_credit 	= 0;
										} else {
											$last_balance_debet 	= 0;
											$last_balance_credit 	= $opening_balance;
										}
									
									
								}
								
							?>
									<tr>
										<td colspan="5" align="center"><b> Total Debet Kredit</b></td>
										<td style="text-align: right"><?php echo number_format($total_debet, 2); ?></td>
										<td style="text-align: right"><?php echo number_format($total_kredit, 2); ?></td>
										
										<td></td>
										<td></td>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td colspan="5" align="center"><b> Saldo Akhir</b></td>
										<td></td>
										<td></td>
										<td style="text-align: right"><?php echo number_format($last_balance_debet, 2); ?></td>
										<td style="text-align: right"><?php echo number_format($last_balance_credit, 2); ?></td>
									</tr>
								</tbody>
							</table>
							<div class="row">
								<div class="col-md-12 " style="text-align  : right !important;">
									<!-- <input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview"> -->
									<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#pdf"><span class="glyphicon glyphicon-eye-open"></span> Preview Data</a>
									<!-- <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#export"><span class="glyphicon glyphicon-print"></span> Export Data</a> -->
								</div>
							</div> 
						
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
<div id="export" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">File Export</h4>
      </div>
      <div class="modal-body">
      	<center>
      		<table>
				<?php 
				$no =1;

					for ($i=0; $i < $fileexcel ; $i++) {  ?>

						
						<a href='<?php echo base_url(); ?>AcctLedgerReport/export/<?php echo $fileexcel; ?>/<?php echo $i; ?>' title="Export to Excel" class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> Export Data File Ke <?php echo $no; ?></a>
						<br>
						<br>

					<?php
						$no++;
					} 
				?>
			</table>
      	</center>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="pdf" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">File Pdf</h4>
      </div>
      <div class="modal-body">
      	<center>
      		<table>
				<?php 
				$no =1;

					for ($i=0; $i < $file ; $i++) {  ?>

						
						<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctLedgerReport/pdf/<?php echo $file; ?>/<?php echo $i; ?>","_blank"));' title="Export to Excel" class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> View Data File Ke <?php echo $no; ?></a>
						<br>
						<br>

					<?php
						$no++;
					} 
				?>
			</table>
      	</center>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

