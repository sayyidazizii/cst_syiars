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
			<a href="<?php echo base_url();?>AcctRecalculatePHU">
				Recalculate PHU
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Recalculate PHU
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
?>	
<?php	echo form_open('AcctRecalculatePHU/processAcctRecalculatePHU',array('id' => 'myform', 'class' => '')); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="0%"></th>
							<th width="5%">No</th>
							<th width="10%">No Anggota</th>
							<th width="25%">Nama Anggota</th>
							<th width="20%">Nama Simpanan</th>
							<th width="20%">Nomor Rekening</th>
							<th width="20%">Bagi Hasil</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($bagihasilanggota)){
								echo "
									<tr>
										<td colspan='7' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($bagihasilanggota as $key=>$val){									
									echo"
										<tr>			
											<td style='text-align:center'></td>
											<td style='text-align:center'>".$no."</td>
											<td>".$val['member_no']."</td>
											<td>".$val['member_name']."</td>
											<td>".$val['savings_name']."</td>
											<td>".$val['savings_account_no']."</td>
											<td>".number_format($val['bagi_hasil'])."</td>
										</tr>
									";
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
					<div class="col-md-12" style='text-align:right'>
						<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
						<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
					</div>	
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>
