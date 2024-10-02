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

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>
<script>
	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('fleet/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function setExportCoreMemberStartDate(name, value){
		document.getElementById("core_member_export_start_date").value 			= value;
	}

	function setExportCoreMemberEndDate(name, value){
		document.getElementById("core_member_export_end_date").value 		= value;
	}

	function setExportAcctSavingsAccountStartDate(name, value){
		document.getElementById("acct_savings_account_export_start_date").value 			= value;
	}

	function setExportAcctSavingsAccountEndDate(name, value){
		document.getElementById("acct_savings_account_export_end_date").value 		= value;
	}

	function setExportAcctDepositoAccountStartDate(name, value){
		document.getElementById("acct_deposito_account_export_start_date").value 			= value;
	}

	function setExportAcctDepositoAccountEndDate(name, value){
		document.getElementById("acct_deposito_account_export_end_date").value 		= value;
	}

	function setExportAcctCreditsAccountStartDate(name, value){
		document.getElementById("acct_credits_account_export_start_date").value 			= value;
	}

	function setExportAcctCreditsAccountEndDate(name, value){
		document.getElementById("acct_credits_account_export_end_date").value 		= value;
	}

	function setExportAcctGeneralLedgerStartDate(name, value){
		document.getElementById("acct_general_ledger_export_start_date").value 			= value;
	}

	function setExportAcctGeneralLedgerEndDate(name, value){
		document.getElementById("acct_general_ledger_export_end_date").value 		= value;
	}


	function setExportAcctJournalVoucherStartDate(name, value){
		document.getElementById("acct_journal_voucher_export_start_date").value 			= value;
	}

	function setExportAcctJournalVoucherEndDate(name, value){
		document.getElementById("acct_journal_voucher_export_end_date").value 		= value;
	}

	$(document).ready(function(){
        $("#branch_id").change(function(){
            var branch_id = $("#branch_id").val();
			$("#core_member_export_branch_id").val(branch_id);

			$("#acct_savings_account_export_branch_id").val(branch_id);

			$("#acct_deposito_account_export_branch_id").val(branch_id);

			$("#acct_credits_account_export_branch_id").val(branch_id);

			$("#acct_general_ledger_export_branch_id").val(branch_id);

			$("#acct_journal_voucher_export_branch_id").val(branch_id);
        });
    });

	
	
	
</script>
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
			<a href="<?php echo base_url();?>export">
				Export Data
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<h3 class="page-title">
	Export Data
</h3> 
<!-- END PAGE TITLE & BREADCRUMB-->

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					List
				</div>
			</div>

			<div class="portlet-body">
				<div class="form-body">
					<?php
						echo $this->session->userdata('message');
						$this->session->unset_userdata('message');

						$unique 		= $this->session->userdata('unique');
						$auth			= $this->session->userdata('auth');
						$data 			= $this->session->userdata('addSalesSalesman-'.$unique['unique']);


						if (empty($data['core_member_export_start_date'])){
							$data['core_member_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['core_member_export_end_date'])){
							$data['core_member_export_end_date'] = date("Y-m-d");
						}

						if (empty($data['acct_savings_account_export_start_date'])){
							$data['acct_savings_account_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['acct_savings_account_export_end_date'])){
							$data['acct_savings_account_export_end_date'] = date("Y-m-d");
						}

						if (empty($data['acct_deposito_account_export_start_date'])){
							$data['acct_deposito_account_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['acct_deposito_account_export_end_date'])){
							$data['acct_deposito_account_export_end_date'] = date("Y-m-d");
						}

						if (empty($data['acct_credits_account_export_start_date'])){
							$data['acct_credits_account_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['acct_credits_account_export_end_date'])){
							$data['acct_credits_account_export_end_date'] = date("Y-m-d");
						}

						if (empty($data['acct_general_ledger_export_start_date'])){
							$data['acct_general_ledger_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['acct_general_ledger_export_end_date'])){
							$data['acct_general_ledger_export_end_date'] = date("Y-m-d");
						}

						if (empty($data['acct_journal_voucher_export_start_date'])){
							$data['acct_journal_voucher_export_start_date'] = date("Y-m-d");
						}

						if (empty($data['acct_journal_voucher_export_end_date'])){
							$data['acct_journal_voucher_export_end_date'] = date("Y-m-d");
						}

						$auth 		= $this->session->userdata('auth');

					?>

					<div class = "row">
						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
										echo form_dropdown('branch_id', $corebranch, set_value('branch_id', $data['branch_id']), 'id="branch_id" class="form-control select2me" ');
									?>
									<label class="control-label">Nama Cabang</label>
								</div>
							</div>	
						<?php 
							} else {
								$data['branch_id'] 		= $auth['branch_id'];	
						?>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_name" id="branch_name"  value="<?php echo set_value('branch_name', $auth['branch_name']);?>" autocomplete="off" readonly/>

									<label class="control-label">Nama Cabang
										<span class="requiblue">
											*
										</span>
									</label>
								</div>
							</div>	
						<?php
							}
						?>
					</div>

					<h4 class = "form-section bold">Export Anggota</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="core_member_export_start_date1" id="core_member_export_start_date1" onchange='setExportCoreMemberStartDate(this.name, this.value)' value="<?php echo tgltoview($data['core_member_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Anggota
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="core_member_export_end_date1" id="core_member_export_end_date1" onchange='setExportCoreMemberEndDate(this.name, this.value)' value="<?php echo tgltoview($data['core_member_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Anggota
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-member') ?>" enctype="form-data" target="_BLANK">
						<input name="core_member_export_start_date" id="core_member_export_start_date" type="hidden" class="form-control" value="<?php echo $data['core_member_export_start_date'];?>" readonly>

						<input name="core_member_export_end_date" id="core_member_export_end_date" type="hidden" class="form-control" value="<?php echo $data['core_member_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="core_member_export_branch_id" id="core_member_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['core_member_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="core_member_export_branch_id" id="core_member_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Anggota</button>
							</div>
						</div>
					</form>	
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">
					
					<h4 class = "form-section bold">Export Simpanan</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_savings_account_export_start_date1" id="acct_savings_account_export_start_date1" onchange='setExportAcctSavingsAccountStartDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_savings_account_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Simpanan
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_savings_account_export_end_date1" id="acct_savings_account_export_end_date1" onchange='setExportAcctSavingsAccountEndDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_savings_account_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Simpanan
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-savings') ?>" enctype="form-data" target="_BLANK">
						<input name="acct_savings_account_export_start_date" id="acct_savings_account_export_start_date" type="hidden" class="form-control" value="<?php echo $data['acct_savings_account_export_start_date'];?>" readonly>

						<input name="acct_savings_account_export_end_date" id="acct_savings_account_export_end_date" type="hidden" class="form-control" value="<?php echo $data['acct_savings_account_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="acct_savings_account_export_branch_id" id="acct_savings_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['acct_savings_account_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="acct_savings_account_export_branch_id" id="acct_savings_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Simpanan</button>
							</div>
						</div>
					</form>	
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">


					<h4 class = "form-section bold">Export Simpanan Berjangka</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_deposito_account_export_start_date1" id="acct_deposito_account_export_start_date1" onchange='setExportAcctDepositoAccountStartDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_deposito_account_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Simpanan Berjangka
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_deposito_account_export_end_date1" id="acct_deposito_account_export_end_date1" onchange='setExportAcctDepositoAccountEndDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_deposito_account_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Simpanan Berjangka
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-deposito') ?>" enctype="form-data" target="_BLANK">
						<input name="acct_deposito_account_export_start_date" id="acct_deposito_account_export_start_date" type="hidden" class="form-control" value="<?php echo $data['acct_deposito_account_export_start_date'];?>" readonly>

						<input name="acct_deposito_account_export_end_date" id="acct_deposito_account_export_end_date" type="hidden" class="form-control" value="<?php echo $data['acct_deposito_account_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="acct_deposito_account_export_branch_id" id="acct_deposito_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['acct_deposito_account_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="acct_deposito_account_export_branch_id" id="acct_deposito_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Simpanan Berjangka</button>
							</div>
						</div>
					</form>	
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">

					<h4 class = "form-section bold">Export Pembiayaan</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_credits_account_export_start_date1" id="acct_credits_account_export_start_date1" onchange='setExportAcctCreditsAccountStartDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_credits_account_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Pembiayaan
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_credits_account_export_end_date1" id="acct_credits_account_export_end_date1" onchange='setExportAcctCreditsAccountEndDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_credits_account_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Pembiayaan
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-credits') ?>" enctype="form-data" target="_BLANK">
						<input name="acct_credits_account_export_start_date" id="acct_credits_account_export_start_date" type="hidden" class="form-control" value="<?php echo $data['acct_credits_account_export_start_date'];?>" readonly>

						<input name="acct_credits_account_export_end_date" id="acct_credits_account_export_end_date" type="hidden" class="form-control" value="<?php echo $data['acct_credits_account_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="acct_credits_account_export_branch_id" id="acct_credits_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['acct_credits_account_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="acct_credits_account_export_branch_id" id="acct_credits_account_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Pembiayaan</button>
							</div>
						</div>
					</form>	
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">


					<h4 class = "form-section bold">Export Buku Besar</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_general_ledger_export_start_date1" id="acct_general_ledger_export_start_date1" onchange='setExportAcctGeneralLedgerStartDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_general_ledger_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Buku Besar
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_general_ledger_export_end_date1" id="acct_general_ledger_export_end_date1" onchange='setExportAcctGeneralLedgerEndDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_general_ledger_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Buku Besar
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-general') ?>" enctype="form-data" target="_BLANK">
						<input name="acct_general_ledger_export_start_date" id="acct_general_ledger_export_start_date" type="hidden" class="form-control" value="<?php echo $data['acct_general_ledger_export_start_date'];?>" readonly>

						<input name="acct_general_ledger_export_end_date" id="acct_general_ledger_export_end_date" type="hidden" class="form-control" value="<?php echo $data['acct_general_ledger_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="acct_general_ledger_export_branch_id" id="acct_general_ledger_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['acct_general_ledger_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="acct_general_ledger_export_branch_id" id="acct_general_ledger_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Buku Besar</button>
							</div>
						</div>
					</form>	
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">


					<h4 class = "form-section bold">Export Jurnal</h4>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_journal_voucher_export_start_date1" id="acct_journal_voucher_export_start_date1" onchange='setExportAcctJournalVoucherStartDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_journal_voucher_export_start_date']);?>"/>
								<label class="control-label">Tanggal Awal Export Jurnal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="acct_journal_voucher_export_end_date1" id="acct_journal_voucher_export_end_date1" onchange='setExportAcctJournalVoucherEndDate(this.name, this.value)' value="<?php echo tgltoview($data['acct_journal_voucher_export_end_date']);?>"/>
								<label class="control-label">Tanggal Akhir Export Jurnal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>
					<form method="post" action="<?php echo base_url('export/process-export-journal') ?>" enctype="form-data" target="_BLANK">
						<input name="acct_journal_voucher_export_start_date" id="acct_journal_voucher_export_start_date" type="hidden" class="form-control" value="<?php echo $data['acct_journal_voucher_export_start_date'];?>" readonly>

						<input name="acct_journal_voucher_export_end_date" id="acct_journal_voucher_export_end_date" type="hidden" class="form-control" value="<?php echo $data['acct_journal_voucher_export_end_date'];?>" readonly>

						<?php 
							if ($auth['branch_status'] == 1){
						?>
							<input name="acct_journal_voucher_export_branch_id" id="acct_journal_voucher_export_branch_id" type="hidden" class="form-control" value="<?php echo $data['acct_journal_voucher_export_branch_id'];?>" readonly>
						<?php 
							} else {
						?>
							<input name="acct_journal_voucher_export_branch_id" id="acct_journal_voucher_export_branch_id" type="hidden" class="form-control" value="<?php echo $auth['branch_id'];?>" readonly>
						<?php
							}
						?>

						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-plus"></i> Export Jurnal</button>
							</div>
						</div>
					</form>	
				</div>
			</div>
		</div>
	</div>
</div>
