

<style>

	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
		padding: 3px;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
		padding: 3px;
	}

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	
	input:focus { 
	  background-color: 42f483;
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
			<a href="<?php echo base_url();?>AcctSavingsDailyAverageBalanceReport">
				Daftar Saldo Harian
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	$auth 		= $this->session->userdata('auth');
	$data 		= $this->session->userdata('filter-acctsavingsdailyaveragebalancereport');
	$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']			= date('m');
		$data['year_period']			= $year_now;
		$data['branch_id']				= $auth['branch_id'];
	}

	if($auth['branch_status'] <> 1){
		$data['branch_id'] 		= $auth['branch_id'];
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
<?php	echo form_open('AcctSavingsDailyAverageBalanceReport/filter',array('id' => 'myform', 'class' => '')); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Saldo Harian
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $month,set_value('month_period',$data['month_period']),'id="month_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period',$data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<?php
										echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me" ');
									?>
									<label>Cabang</label>
								</div>
							</div>
						<?php } ?>

						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_id', $acctsavings,set_value('savings_id', $data['savings_id']), 'id="savings_id" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
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
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Saldo Harian
				</div>
			</div>
			<div class="portlet-body">
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
						<thead>
							<tr>
								<th style="text-align: center; width: 5%">No</th>
								<th style="text-align: center; width: 12%">Nama Simpanan</th>
								<th style="text-align: center; width: 12%">No. Rek</th>
								<th style="text-align: center; width: 15%">Nama</th>
								<th style="text-align: center; width: 20%">Alamat</th>
								<th style="text-align: center; width: 12%">SRH</th>
							</tr>
						</thead>
						<tbody></tbody>
						</table>
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#export"><span class="glyphicon glyphicon-print"></span> View Data</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END EXAMPLE TABLE PORTLET-->
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

					for ($i=0; $i < $file ; $i++) {  ?>

						
						<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctSavingsDailyAverageBalanceReport/view/<?php echo $file; ?>/<?php echo $i; ?>","_blank"));' title="View" class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> View Data File Ke <?php echo $no; ?></a>
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

<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('AcctSavingsDailyAverageBalanceReport/getSavingsDailyAverageBalance')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});
</script>

