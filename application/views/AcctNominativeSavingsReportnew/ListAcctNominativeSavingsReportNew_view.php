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
			<a href="<?php echo base_url();?>AcctNominativeSavingsReportnew">
				Daftar Nominatif Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<?php	echo form_open('AcctNominativeSavingsReportnew/filter',array('id' => 'myform', 'class' => ''));

	$auth = $this->session->userdata('auth');
	$sesi = $this->session->userdata('filter-acctnominativesavingsreport');

	if(!is_array($sesi)){
	$sesi['start_date']			= date('d-m-Y');
	$sesi['end_date']			= date('d-m-Y');
	$sesi['branch_id']			= $auth['branch_id'];

	}

	// $start_date			= $sesi['start_date'];
	// $end_date			= $sesi['end_date'];

	if($auth['branch_status'] == 1){
		if(empty($sesi['branch_id'])){
			$sesi['branch_id'] = $auth['branch_id'];
		}
	} else {
		$sesi['branch_id'] = $auth['branch_id'];
	}
	
	// for($i = ($year_now-2); $i<($year_now+2); $i++){
	// 	$year[$i] = $i;
	// } 

?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Nominatif Simpanan
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo $sesi['start_date'];?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo $sesi['end_date'];?>"/>
								<label class="control-label">Tanggal Akhir
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
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>

<?php echo form_close(); ?>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="8%">No. Rek</th>
							<th width="15%">Nama Anggota</th>
							<th width="8%">Status Anggota</th>
							<th width="15%">Alamat</th>
							<th width="8%">Tanggal Buka</th>
							<th width="8%">Jml Mutasi Masuk</th>
							<th width="8%">Jml Mutasi Keluar</th>
							<th width="8%">SRH</th>
							<th width="15%">Saldo Perakhir Maret 2020</th>

						</tr>
					</thead>
					<tbody></tbody>
					</table>
				</div>
				<div class="row">
					<div class="col-md-12 " style="text-align  : right !important;">
						<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#export"><span class="glyphicon glyphicon-print"></span> Export Data</a>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
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

						
						<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctNominativeSavingsReportnew/view/<?php echo $file; ?>/<?php echo $i; ?>","_blank"));' title="Export to Excel" class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> Export Data File Ke <?php echo $no; ?></a>
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
            "url": "<?php echo site_url('AcctNominativeSavingsReportnew/getListAcctNominativeSavingsReportnew')?>",
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