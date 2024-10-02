<script>
	base_url = '<?php echo base_url();?>';
	mappia = "	<?php 
					$site_url = 'PpobTopupMember/addPpobTopupMember/'.$this->uri->segment(3);
					echo site_url($site_url); 
				?>";

	function reset_add(){
		document.location= base_url+"PpobTopupMember/reset_add";
	}

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('PpobTopupMember/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
	function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('PpobTopupMember/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

    function convertToRupiah(angka){
		var rupiah = '';
		var angkarev = angka.toString().split('').reverse().join('');
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
		return rupiah.split('',rupiah.length-1).reverse().join('');
	}

	$(document).ready(function(){
        $("#savings_account_id").change(function(){
            var savings_account_id 		= $("#savings_account_id").val();
				
				$.post(base_url + 'PpobTopupMember/getAcctSavingsAccount_Detail',
				{savings_account_id: savings_account_id},
				   function(data){
					   $("#savings_id").val(data.savings_id);
					   $("#savings_account_last_balance").val(data.savings_account_last_balance);
					   $("#savings_account_last_balance_view").val(toRp(data.savings_account_last_balance));
				  },
						'json'
				);
		});
	});

	$(document).ready(function(value, no){
        $("#ppob_topup_member_amount_view").change(function(){

			ppob_topup_amount 		= document.getElementById('ppob_topup_member_amount_view').value;

			name = "ppob_topup_member_amount";

			function_elements_add(name,ppob_topup_amount);

			document.getElementById("ppob_topup_member_amount").value 		= ppob_topup_amount;
			document.getElementById("ppob_topup_member_amount_view").value 	= toRp(ppob_topup_amount);

		});
    });

	

</script>

<!-- <style>

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
	}<div class="portlet"> 
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	
</style> -->

<?php 
		echo form_open('PpobTopupMember/processAddPpobTopupMember',array('id' => 'myform', 'class' => 'horizontal-form')); 


		$auth 	= $this->session->userdata('auth');
		$sesi 	= $this->session->userdata('unique');
		$data 	= $this->session->userdata('addPpobTopupMember-'.$sesi['unique']);
		$token 	= $this->session->userdata('ppobtopupmembertoken-'.$sesi['unique']);


		if(empty($data['savings_account_id'])){
			$data['savings_account_id'] = 0;
		}

		if(empty($data['ppob_topup_member_amount'])){
			$data['ppob_topup_member_amount'] = 0;
		}



	?>
	
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
				<a href="<?php echo base_url();?>PpobTopupMember">
					Daftar Top Up PPOB Anggota
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>PpobTopupMember/addPpobTopupMember">
					Tambah Top Up PPOB Anggota
				</a>
			</li>
		</ul>
	</div>
	<h3 class="page-title">
	Form Tambah Top Up PPOB Anggota
	</h3>
	<!-- END PAGE TITLE & BREADCRUMB-->	

<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Top Up PPOB Anggota
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>PpobTopupMember" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>

			<div class="portlet-body form">
				<div class="form-body">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								
								<input type="text" class="form-control" name="ppob_topup_member_date" id="ppob_topup_member_date" value="<?php echo date('d-m-Y');?>" readonly>

								<label class="control-label">Tanggal Top Up 
									<span class="required">
									*
									</span>
								</label>
								
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-2">
							<div class="form-group form-md-line-input">
								<input type="text" class="hidden" name="member_id" id="member_id" value="<?php echo $coremember['member_id']; ?>" readonly>

								<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $coremember['member_no']; ?>" readonly>

								<label class="control-label">Anggota
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="member_name" id="member_name" value="<?php echo $coremember['member_name']; ?>" readonly>

								<label class="control-label"></label>
							</div>
						</div>

						<div class="col-md-1">
							<div class="form-group form-md-line-input">
								<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari Anggota</a>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="ppob_balance_view" id="ppob_balance_view" value="<?php echo nominal($ppob_balance); ?>" readonly >

								<input type="text" class="hidden" name="ppob_balance_amount" id="ppob_balance_amount" value="<?php echo $ppob_balance; ?>" >

								<label class="control-label">Sisa Saldo
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php 
									echo form_dropdown('savings_account_id', $acctsavingsaccount,set_value('savings_account_id', $data['savings_account_id']),'id="savings_account_id" class="form-control select2me"');
								?>

								<label class="control-label">No. Rek Simpanan
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="savings_account_last_balance_view" id="savings_account_last_balance_view"  readonly >

								<input type="text" class="hidden" name="savings_account_last_balance" id="savings_account_last_balance" >

								<label class="control-label">Saldo Simpanan
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<input type="text" class="hidden" name="savings_id" id="savings_id" readonly>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="ppob_topup_member_amount_view" id="ppob_topup_member_amount_view" value="<?php echo nominal($data['ppob_topup_member_amount']); ?>" >

								<input type="text" class="hidden" name="ppob_topup_member_amount" id="ppob_topup_member_amount" value="<?php echo $data['ppob_topup_member_amount']; ?>" >

								<label class="control-label">Jumlah Top Up
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>
					</div>

					<input type="text" class="hidden" name="ppob_topup_member_token" id="ppob_topup_member_token" value="<?php echo $token; ?>" readonly>


					<div class="row">
						<div class="form-actions right">
							<button type="submit" name="Save" value="Save" id="Save" class="btn btn-md green-jungle" title="Simpan Data" ><i class="fa fa-check"> Simpan</i></button>
						</div>
					</div>	

				</div>
			</div>
		</div>
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
		        	<th>Member No</th>
		            <th>Member Nama</th>
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
            "url": "<?php echo site_url('PpobTopupMember/getListCoreMember')?>",
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


