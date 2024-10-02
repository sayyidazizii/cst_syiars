<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}

	.hidden {
	    background-color:rgba(0, 0, 0, 0);
	    color:white;
	    border: none;
	    outline:none;
	    height:30px;
	    transition:height 1s;
	    -webkit-transition:height 1s;
	}
</style>
<script type="text/javascript">
	base_url = '<?php echo base_url();?>';

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctSavingsDataMutation/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}
	
	function reset_search(){
		document.location = base_url+"AcctSavingsDataMutation/reset_search";
	}

	function processPrinting(){
		var value 				= 'print';
		var savings_account_id 	= $('#savings_account_id').val();
		var last_number 		= $('#savings_account_last_number').val();

		document.location = base_url+"AcctSavingsDataMutation/processPrinting/"+value+"/"+savings_account_id+"/"+last_number;
	}

	function processPreview(){
		var value 				= 'preview';
		var savings_account_id 	= $('#savings_account_id').val();
		var last_number 		= $('#savings_account_last_number').val();

		document.location = base_url+"AcctSavingsDataMutation/processPrinting/"+value+"/"+savings_account_id+"/"+last_number;
	}

	function cek_satu (kunci) {
		 
		if($('#'+kunci+"_cek").is(':checked')==false){
			document.getElementById(kunci+"_cek2").value ='';
		} else {
			/*alert(kunci);*/
			document.getElementById(kunci+"_cek2").value =1;
		}
    }

    function cek_satu_non (kunci) {
		 
		if($('#'+kunci+"_cek_non").is(':checked')==false){
			document.getElementById(kunci+"_cek2_non").value ='';
		} else {
			/*alert(kunci);*/
			document.getElementById(kunci+"_cek2_non").value =1;
		}
    }

</script>
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
			<a href="<?php echo base_url();?>AcctSavingsDataMutation">
				Data Mutasi Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-acctsavingsdatamutation');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['savings_account_id']	= '';
	}
?>	
<?php	echo form_open('AcctSavingsDataMutation/filter',array('id' => 'myform', 'class' => '')); ?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Data Mutasi Simpanan
				</div>
				<!-- <div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div> -->
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					<div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">No Rekening</td>
									<td width="5%">:</td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="savings_account_no" id="savings_account_no"  value="<?php echo $acctsavingsaccount['savings_account_no'];?>" style="width: 60%" readonly/>
										<input class="easyui-textbox" type="hidden" name="savings_account_id" id="savings_account_id"  value="<?php echo $acctsavingsaccount['savings_account_id'];?>"/>
										<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari No Rek</a>
										
									</td>
								</tr>
								<tr>
									<td width="35%">Nama</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-textbox" type="text" name="end_date" id="end_date"  value="<?php echo $acctsavingsaccount['member_name'];?>" readonly/></td>
								</tr>
								<tr>
									<td width="35%">Alamat</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-textbox" type="text" name="start_date" id="start_date"  value="<?php echo $acctsavingsaccount['member_address']?>" readonly/></td>
								</tr>
								<tr>
									<td width="35%">No. Terakhir</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-textbox" type="text" name="savings_account_last_number" id="savings_account_last_number"  value="<?php echo $acctsavingsaccount['savings_account_last_number'];?>"  onChange="function_elements_add(this.name, this.value);"/></td>
								</tr>
							</table>
						</div>
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">Tanggal Mulai</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"type="text" name="start_date" id="start_date"  value="<?php echo tgltoview($sesi['start_date']);?>"/></td>
								</tr>
								<tr>
									<td width="35%">Tanggal Akhir</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="end_date" id="end_date"  value="<?php echo tgltoview($sesi['end_date']);?>"/></td>
								</tr>
								<tr>
									<td width="35%"></td>
									<td width="5%"></td>
									<td width="60%" align="left">
										<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
										<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Cari</button>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<br>
					<?php echo form_close(); ?>
				<?php	echo form_open('AcctSavingsDataMutation/processAddAcctSavingsDataMutation',array('id' => 'myform', 'class' => '')); ?>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th style="text-align: center; width: 5%">No</th>
								<th style="text-align: center; width: 15%">No. Rek</th>
								<th style="text-align: center; width: 15%">Jenis Simp</th>
								<th style="text-align: center; width: 10%">Tgl Transaksi</th>
								<th style="text-align: center; width: 8%">Sandi</th>
								<th style="text-align: center; width: 15%">Debet</th>
								<th style="text-align: center; width: 15%">Kredit</th>
								<th style="text-align: center; width: 15%">Saldo</th>
								<th style="text-align: center; width: 10%">Oprt</th>
								<th style="text-align: center; width: 10%">Cetak</th>
								<th style="text-align: center; width: 10%">Aktifkan</th>
								<th style="text-align: center; width: 10%">Non Aktifkan</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctsavingsaccountdetail)){
									echo "
										<tr>
											<td colspan='8' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctsavingsaccountdetail as $key=>$val){									
										echo"
										<input type='text' name='".$no."_cek2' value='".$no."' id='".$no."_cek2' class='hidden'>
										<input type='text' name='".$no."_cek2_non' value='".$no."' id='".$no."_cek2_non' class='hidden'>
										<input type='text' name='".$no."_savings_account_detail_id' value='".$val['savings_account_detail_id']."' id='".$no."_savings_account_detail_id' class='hidden'>

											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['savings_account_no']."</td>
												<td>".$val['savings_name']."</td>
												<td>".tgltoview($val['today_transaction_date'])."</td>
												<td>".$val['mutation_code']."</td>
												<td style='text-align:right'>".number_format($val['mutation_out'], 2)."</td>
												<td style='text-align:right'>".number_format($val['mutation_in'], 2)."</td>
												<td style='text-align:right'>".number_format($val['last_balance'], 2)."</td>
												<td>".$val['operated_name']."</td>
												<td>".$printstatus[$val['savings_print_status']]."</td>
												<td><input type='checkbox' class='checkboxes' name='".$no."_cek' id='".$no."_cek' value='1'  OnClick='cek_satu(".$no.")';/></td>
												<td><input type='checkbox' class='checkboxes' name='".$no."_cek_non' id='".$no."_cek_non' value='1'  OnClick='cek_satu_non(".$no.")';/></td>
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
						</table>
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<input class="easyui-textbox" type="hidden" name="savings_account_id" id="savings_account_id"  value="<?php echo $acctsavingsaccount['savings_account_id'];?>"/>
								
								<button type="submit" name="Preview" id="Preview" value="preview" class="btn blue" title="Preview">Aktifkan / Non Aktifkan</button>
							</div>
						</div>
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
		        	<th>No Rek</th>
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
            "url": "<?php echo site_url('AcctSavingsDataMutation/getListAcctSavingsAccount')?>",
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

<script type="text/javascript">
        function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(d,m-1,y);
            } else {
                return new Date();
            }
        }
    </script>

<?php echo form_close(); ?>