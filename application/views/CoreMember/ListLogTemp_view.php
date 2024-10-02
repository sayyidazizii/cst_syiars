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
<div class="row-fluid">
	<?php
		// echo $this->session->userdata('message');
		// $this->session->unset_userdata('message');
		$auth = $this->session->userdata('auth');
	?>

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
			<a href="<?php echo base_url();?>CoreMember">
				Daftar Data Member
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Data Member<small>Kelola Data Member</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
	// print_r($coremember);
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
						<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
							<thead>
								<tr>
									<th width="3%">No</th>
									<th width="7%">No Anggota</th>
									<th width="12%">Password</th>
									<th width="15%">Password Transaksi</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$no = 1;
								foreach($logtemp as $val) { 
									$array = get_object_vars($val);
								?>
									<tr>
										<td style="text-align:center !important;"><?php echo $no; ?></td>
										<td><?php echo $array['member_no']; ?></td>
										<td><?php echo $array['log']; ?></td>
										<td><?php echo $array['logt']; ?></td>
									</tr>
								<?php
								$no++;
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo form_close(); ?>