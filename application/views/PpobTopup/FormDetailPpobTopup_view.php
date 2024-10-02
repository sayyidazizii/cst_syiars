<script>
	base_url = '<?php echo base_url();?>';
</script>
	
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class = "page-bar">
		<ul class="page-breadcrumb">
			<li>
				<a href="<?php echo base_url();?>">
					Beranda
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>AcctSavingsTransferPpob">
					Daftar Data Transfer PPOB
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>AcctSavingsTransferPpob/showdetail/<?php echo $this->uri->segment(3);?>">
					Detail Transfer PPOB
				</a>
			</li>
		</ul>
	</div>
	<h3 class="page-title">
	Form Detail Data Transfer PPOB
	</h3>
	<!-- END PAGE TITLE & BREADCRUMB-->	

<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Data Transfer PPOB
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctSavingsTransferPpob" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>

			<div class="portlet-body form">
				<div class="form-body">
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								
								<input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo tgltoview($acctsavingstransferppob['savings_transfer_ppob_date']);?>" readonly>

								<label class="control-label">Tanggal Transfer</label>
								
							</div>
						</div>
					</div>

					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="savings_account_ppob_no" id="savings_account_ppob_no" value="<?php echo "( ".$acctsavingstransferppob['savings_account_no']." ) ".$acctsavingstransferppob['member_name']; ?>" readonly>
								<label class="control-label">No. Rekening PPOB Madani</label>
									
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="savings_transfer_ppob_amount_view" id="savings_transfer_ppob_amount_view" value="<?php echo nominal($acctsavingstransferppob['savings_transfer_ppob_total_amount']); ?>" readonly>
								<label class="control-label">Jumlah Transfer</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="portlet box blue">
	<div class="portlet-title">
		<div class="caption">
			Daftar Data Transfer Dipilih
		</div>
	</div>
	<div class="portlet-body form">
		<div class="form-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered table-advance table-hover">
							<thead>
								<tr>
									<th style='text-align:center; vertical-align:middle' width="5%">No</th>
									<th style='text-align:center; vertical-align:middle' width="10%">No Rekening</th>
									<th style='text-align:center; vertical-align:middle' width="20%">Nama Anggota</th>
									<th style='text-align:center; vertical-align:middle' width="30%">Alamat</th>
									<th style='text-align:center; vertical-align:middle' width="20%">Jumlah Transfer</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$no=1;
								$total_amount =0;
								$total_amount_total = 0;
								
								if(!is_array($acctsavingstransferppobitem)){
									echo "<tr><th colspan='7' style='text-align  : center !important;'>Data is Empty</th></tr>";
								} else {
									foreach ($acctsavingstransferppobitem as $key=>$val){

										$total_amount_item 		= 0;

										echo"

													<tr>				
														<td style='text-align:center'>".$no."</td>
														<td>".$val['savings_account_no']."</td>
														<td>".$val['member_name']."</td>
														<td>".$val['member_address']."</td>
														
														<td style='text-align  : right !important;'>".nominal($val['savings_transfer_ppob_item_amount'],2)."</td>
												</td>												
												
											</tr>
										";
											
										$no++;

										
										$total_amount 	+= $val['savings_transfer_ppob_item_amount'];

										$total_amount_total = $total_amount;

									}

								}

								echo "
									<tr>
										<td colspan='4' style='text-align  : right !important;'><h4 class='form control-label'>Total Semua</h4></td>
										
										<td colspan='1' style='text-align  : right !important;'><b>".nominal($total_amount_total,2)."</b>

											<input readonly type='hidden' name='total_amount_total' id='total_amount_total' value='".$total_amount_total."' class='form-control'>
										</td>

									</tr>
								";								
							?>	

								

							</tbody>
						</table>
					</div>
				</div>
			</div>	

		</div>
	</div>
</div>


