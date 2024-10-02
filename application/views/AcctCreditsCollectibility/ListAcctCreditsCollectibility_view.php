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
			<a href="<?php echo base_url();?>AcctCreditsCollectibility">
				KOLEKTIBILITAS PEMBIAYAAN
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	$auth = $this->session->userdata('auth');

	if($auth['branch_status'] == 1){

	$sesi = $this->session->userdata('filter-acctcreditscellectibility');

	if(!is_array($sesi)){
		$sesi['branch_id']			= '';
	}
?>
<h3 class="page-title">
	KOLEKTIBILITAS PEMBIAYAAN <small>KELOLA KOLEKTIBILITAS PEMBIAYAAN</small>
</h3>
<?php	echo form_open('AcctCreditsCollectibility/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
				KOLEKTIBILITAS PEMBIAYAAN
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me"');?>
								<label class="control-label">Cabang
									<span class="required">
										*
									</span>
								</label>
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

<?php echo form_close(); } else {?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
				KOLEKTIBILITAS PEMBIAYAAN
				</div>
			</div>

			<?php } ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">Nomor Akad</th>
							<th width="10%">Nama Anggota</th>
							<th width="10%">Alamat</th>
							<th width="8%">Outstanding</th>
							<th width="10%">Sisa Margin</th>
							<th width="10%">Kolektibilitas</th>
						</tr>
					</thead>
					<tbody></tbody>
					</table>

					<table class="table table-striped table-bordered table-hover table-full-width">

						<?php foreach($preferencecollectibility as $key => $v){
							if($v['collectibility_id'] == 1){
								$persent1 = ($datacolectibility['total1'] / $datacolectibility['totaloutstanding']) * 100;
								echo "
									<tr>
										<td width='15%'><div style='text-align: left; font-size:12px;'>JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
										<td width='20%'><div style='text-align: right; font-size:12px;'> ".number_format($datacolectibility['total1'], 2)." ( ".number_format($persent1, 2)." ) % &nbsp;&nbsp;</div></td>
										<td width='20%'><div style='text-align: left; font-size:12px;'>".$v['collectibility_name']."</div></td>
									</tr>
								";
							} else if($v['collectibility_id'] == 2){
								$persent2 = ($datacolectibility['total2'] / $datacolectibility['totaloutstanding']) * 100;
								echo "
									<tr>
										<td width='15%'><div style='text-align: left; font-size:12px;'>JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
										<td width='20%'><div style='text-align: right; font-size:12px;'> ".number_format($datacolectibility['total2'], 2)." ( ".number_format($persent2, 2)." ) % &nbsp;&nbsp;</div></td>
										<td width='20%'><div style='text-align: left; font-size:12px;'>".$v['collectibility_name']."</div></td>
									</tr>
								";
							} else if($v['collectibility_id'] == 3){
								$persent3 = ($datacolectibility['total3'] / $datacolectibility['totaloutstanding']) * 100;
								echo "
									<tr>
										<td width='15%'><div style='text-align: left; font-size:12px;'>JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
										<td width='20%'><div style='text-align: right; font-size:12px;'> ".number_format($datacolectibility['total3'], 2)." ( ".number_format($persent3, 2)." ) % &nbsp;&nbsp;</div></td>
										<td width='20%'><div style='text-align: left; font-size:12px;'>".$v['collectibility_name']."</div></td>
									</tr>
								";
							} else if($v['collectibility_id'] == 4){
								$persent4 = ($datacolectibility['total4'] / $datacolectibility['totaloutstanding']) * 100;
								echo "
									<tr>
										<td width='15%'><div style='text-align: left; font-size:12px;'>JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
										<td width='20%'><div style='text-align: right; font-size:12px;'> ".number_format($datacolectibility['total4'], 2)." ( ".number_format($persent4, 2)." ) % &nbsp;&nbsp;</div></td>
										<td width='20%'><div style='text-align: left; font-size:12px;'>".$v['collectibility_name']."</div></td>
									</tr>
								";
							}
						}?>

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

						
						<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctCreditsCollectibility/view/<?php echo $file; ?>/<?php echo $i; ?>","_blank"));' title="Export to Excel" class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> Export Data File Ke <?php echo $no; ?></a>
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
        "pageLength": 10,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('AcctCreditsCollectibility/getAcctCreditsCollectibilityList')?>",
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
<?php echo form_close(); ?>