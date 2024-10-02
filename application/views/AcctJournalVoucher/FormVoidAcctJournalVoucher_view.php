<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}


</style>

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
			<a href="<?php echo base_url();?>AcctJournalVoucher">
				Daftar Jurnal Umum
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctJournalVoucher/void/<?php echo $this->uri->segment(3); ?>">
				Pembatalan Jurnal Umum 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Detail Jurnal Umum
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctJournalVoucher" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">						
							<div class="col-md-1"></div>
							<div class="col-md-10">
								<table width="100%">
									<tr>
										<td width="35%">Tanggal</td>
										<td width="5%">:</td>
										<td width="60%">
											<input class="easyui-textbox"  name="journal_voucher_date" id="journal_voucher_date" value="<?php echo tgltoview($acctjournalvoucher['journal_voucher_date']);?>" style="width: 60%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Uraian</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="journal_voucher_description" id="journal_voucher_description" placeholder="Uraian" style="width: 100%" value="<?php echo $acctjournalvoucher['journal_voucher_description']; ?>" readonly>
										</td>
									</tr>
								</table>
								<table width="100%" class="table table-striped table-bordered table-hover table-full-width">
									<tr>
										<td width="20%">No. Perkiraan</td>
										<td width="30%">Nama Perkiraan</td>
										<td width="25%">Debit</td>
										<td width="25%">Kredit</td>
									</tr>

									<?php

										if(empty($acctjournalvoucheritem)){

										} else {
											foreach ($acctjournalvoucheritem as $key => $val) {
												echo "
												<tr>
													<td>".$this->AcctJournalVoucher_model->getAccountCode($val['account_id'])."</td>
													<td>".$this->AcctJournalVoucher_model->getAccountName($val['account_id'])."</td>
													<td style='text-align:right'>".number_format($val['journal_voucher_debit_amount'], 2)."</td>
													<td style='text-align:right'>".number_format($val['journal_voucher_credit_amount'], 2)."</td>
												</tr>
												";
											}
										}
									?>


								</table>
								<table width="100%">
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%">
											<a class="btn red" data-toggle="modal" href="#modalvoidcctdisbursement"><i class="fa fa-times"></i> Pembatalan</a>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

<?php echo form_open('AcctJournalVoucher/processVoidAcctJournalVoucher',array('id' => 'myform', 'class' => 'horizontal-form'));
?>
	<!-- /.modal -->
	<div class="modal fade bs-modal-lg" id="modalvoidcctdisbursement" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Pembatalan Jurnal</h4>
				</div>
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Keterangan</label>
							<div class="input-icon right">
								<i class="fa"></i>
								<?php 
									echo form_textarea(array('rows'=>'3','name'=>'voided_remark','class'=>'form-control','id'=>'voided_remark','value'=>set_value('voided_remark')))
								?>
							</div>	
						</div>	
					</div>
					
					<input type="hidden" class="form-control" name="journal_voucher_id" id="journal_voucher_id"  value="<?php echo set_value('journal_voucher_id',$acctjournalvoucher['journal_voucher_id']);?>"/>
					<input type="hidden" class="form-control" name="journal_voucher_no" id="journal_voucher_no"  value="<?php echo set_value('journal_voucher_no',$acctjournalvoucher['journal_voucher_no']);?>"/>
				
					<div class="modal-footer">
						<button type="button" class="btn red" data-dismiss="modal">Tutup</button>
						<button type="submit" id="Save" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>
	<!-- /.modal -->
<?php
echo form_close(); 
?>