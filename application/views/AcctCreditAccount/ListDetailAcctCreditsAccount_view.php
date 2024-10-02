<style>
th, td {
  padding: 3px;
}
td {
  font-size: 12px;
}
input:focus { 
  background-color: 42f483;
}
.custom{

margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

}
.textbox .textbox-text{
font-size: 10px;


}

</style>
	

<script>
	base_url = '<?php echo base_url();?>';
	function reset_search(){
		document.location = base_url+"AcctCreditAccount/reset_search";
	}
</script>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-AcctCreditsAccount');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['member_id']			= '';
		$sesi['credits_id']			= '';


	}


?>	
<?php	echo form_open('AcctCreditAccount/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian Pinjaman
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-show">
				<div class="form-body form">	
					<table style="width: 100%;" border="0" padding:"0">
						<tr>
							<td>Tanggal Awal</td>
							<td><input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/></td>
							<td>Tanggal Akhir</td>
							<td><input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/></td>
						</tr>
						<tr>
							<td>Anggota</td>
							<td>
								<!-- <?php
									echo form_dropdown('member_id', $coremember,set_value('member_id',$sesi['member_id']),'id="member_id" class="form-control select2me"');
								?> -->
								<input class="easyui-textbox" type="text" name="member_name" id="member_name"  value="<?php echo $coremember['member_no'].' - '.$coremember['member_name'];?>" style="width: 60%" readonly/>
								<input class="easyui-textbox" type="hidden" name="member_id" id="member_id"  value="<?php echo $coremember['member_id'];?>"/>
								<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari Anggota</a>
							</td>
							<td>Jenis Pinjaman</td>
							<td><?php
									echo form_dropdown('credits_id', $acctcredits, set_value('credits_id', $sesi['credits_id']),'id="credits_id" class="form-control select2me" ');
								?></td>
						</tr>
					</table>

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
						Daftar
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_1">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">No Anggota</th>
								<th width="20%">Nama</th>
								<th width="25%">Alamat</th>
								<th width="10%">Pinjaman</th>
								<th width="10%">No Akad</th>
								<th width="10%">Tanggal Akad</th>
								<th width="10%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctcreditsaccount)){
									echo "
										<tr>
											<td colspan='11' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach ($acctcreditsaccount as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['member_no']."</td>
												<td>".$val['member_name']."</td>
												<td>".$val['member_address']." ".$val['kecamatan_name']." ".$val['city_name']." ".$val['province_name']."</td>
												<td>".$val['credits_name']."</td>
												<td>".$val['credits_account_serial']."</td>
												<td>".tgltoview($val['credits_account_date'])."</td>
												
												
												<td>
													<a href='".$this->config->item('base_url').'AcctCreditAccount/showdetail/'.$val['credits_account_id']."' class='btn default btn-xs yellow-lemon'>
														<i class='fa fa-bars'></i> Detail
													</a>
													<a href='".$this->config->item('base_url').'AcctCreditAccount/polaangsuran/'.$val['credits_account_id']."' class='btn default btn-xs blue'>
														<i class='fa fa-bars'></i> Pola Angsuran
													</a>
												</td>
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
							</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
<div id="memberlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No Anggota</th>
		            <th>Nama Anggota</th>
		            <th>Alamat</th>
		            <th>Action</th>
		        </tr>
		    </thead>
		    <tbody></tbody>
		</table>
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
            "url": "<?php echo site_url('AcctCreditAccount/getmemberlist')?>",
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