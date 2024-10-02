<?php
error_reporting(0);
?>
<style>
th, td {
  padding: 2px;
  font-size: 13px;
}
input:focus { 
  background-color: 42f483;
}

.table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{
    padding: 2px;
    vertical-align: top;
    border-top: 1px solid #e7ecf1;
}
.table td, .table th {
    font-size: 12px;
}

input:-moz-read-only { /* For Firefox */
  background-color: #e7ecf1;
}

input:read-only {
  background-color: #e7ecf1;
}
</style>
<script>
	base_url = '<?php echo base_url();?>';
	function function_elements_edit(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('CoreMember/function_elements_edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}
	function reset_edit(){
		document.location = base_url+"CoreMember/reset_edit/<?php echo $coremember['member_id']?>";
	}

	var loopjumlah	= 1;
	var looppokok 	= 1;

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}
	

	$(document).ready(function(){
		$.ajax({
					type: 'GET',
					url : base_url + 'AcctCashPayments/getDetailPayment',
					data: {'credits_account_id' : <?php echo $this->uri->segment(3);?>},
					success: function(msg){
						$('#tabelpembayaran').html(msg);
					}
				});
		$('#city_id').combobox({
			onChange: function(value){
				// alert(value);
      		var city_id   =document.getElementById("city_id").value;
      		// alert(city_id);
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+city_id,
	               data : {city_id: city_id},
	               success: function(data){
	               	alert($data);
					 	$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+city_id,
							valueField:'id',
							textField:'text'
						});
						
	              	}
	            });
  			}
  		});		
    });

    function calc(){
		var pokok= +document.getElementById("credits_payment_principal").value;
		var margin= +document.getElementById("credits_payment_margin").value;
		var denda= +document.getElementById("credits_payment_fine").value;
		var total = pokok+margin+denda;

		$('#total').textbox('setValue',total);
		$('#total_view').textbox('setValue',toRp(total));
	}

	function hitungpayment(ganti){
		var jumlah_angsuran 	= +document.getElementById("jumlah_angsuran").value;
		var pokok_angsuran 		= +document.getElementById("credits_payment_principal").value;
		var margin_angsuran 	= +document.getElementById("credits_payment_margin").value;
		var harga_pokok 		= +document.getElementById("harga_pokok").value;
		var total_margin 		= +document.getElementById("harga_margin").value;
		// var sisa_margin				= $('#sisa_margin_awal').val();	
		// var sisa_margin_awal = parseFloat(sisa_margin) + parseFloat(margin_angsuran);

		/*alert(pokok_angsuran);*/
		
		if(jumlah_angsuran == ''){
			alert('Isikan Jumlah Angsuran ! ');
			$('#credits_payment_principal_view').textbox('setValue','');
			$('#credits_payment_principal').textbox('setValue','');
			$('#credits_payment_margin_view').textbox('setValue','');
			$('#credits_payment_margin').textbox('setValue','');
			$('#total_view').textbox('setValue','');
			$('#total').textbox('setValue','');
		}else{
			if(ganti == 'jumlah'){
				console.log(ganti);
				var total_pembiayaan 	= parseFloat(harga_pokok) + parseFloat(total_margin);

				var angsuran_pokok 		= (((parseFloat(harga_pokok) / parseFloat(total_pembiayaan)) * 100 ) / 100 ) * parseFloat(jumlah_angsuran);
				var angsuran_margin		= parseFloat(jumlah_angsuran) - parseFloat(angsuran_pokok);
				var angsuran_total 		= parseFloat(angsuran_pokok) + parseFloat(angsuran_margin);

				

				console.log(total_pembiayaan);
				console.log(angsuran_pokok);
				console.log(angsuran_margin);
				console.log(angsuran_total);

				
				$('#credits_payment_principal_view').textbox('setValue',angsuran_pokok);
				$('#credits_payment_principal').textbox('setValue',angsuran_pokok);
				$('#credits_payment_margin_view').textbox('setValue',toRp(angsuran_margin));
				$('#credits_payment_margin').textbox('setValue',angsuran_margin);
				$('#total_view').textbox('setValue',toRp(angsuran_total));
				$('#total').textbox('setValue',angsuran_total);
			} else 
			if(ganti == 'pokok'){
				var pokok_angsuran2 	= +document.getElementById("credits_payment_principal_view").value;

				var angsuran_pokok 		= parseFloat(pokok_angsuran2);
				var angsuran_margin		= parseFloat(jumlah_angsuran) - parseFloat(angsuran_pokok);
				var angsuran_total 		= parseFloat(angsuran_pokok) + parseFloat(angsuran_margin);

				console.log(pokok_angsuran2);
				console.log(angsuran_pokok);
				console.log(angsuran_margin);
				console.log(angsuran_total);

				$('#credits_payment_principal_view').textbox('setValue',toRp(angsuran_pokok));
				$('#credits_payment_principal').textbox('setValue',angsuran_pokok);
				$('#credits_payment_margin_view').textbox('setValue',toRp(angsuran_margin));
				$('#credits_payment_margin').textbox('setValue',angsuran_margin);
				$('#total_view').textbox('setValue',toRp(angsuran_total));
				$('#total').textbox('setValue',angsuran_total);


			} else {
				return;
			}
			
			
			
		}
	}

	$(document).ready(function(){
		$('#jumlah_angsuran_view').textbox({
			onChange: function(value){
				if(loopjumlah == 0){
					loopjumlah = 1;
					return;
				}
				if(loopjumlah ==1){
					loopjumlah = 0;
					var jumlah_angsuran_view				= $('#jumlah_angsuran_view').val();	
					$('#jumlah_angsuran_view').textbox('setValue',toRp(jumlah_angsuran_view));
					$('#jumlah_angsuran').textbox('setValue',jumlah_angsuran_view);			
					hitungpayment('jumlah');

				}else{
					loopjumlah=1;
					return;
				}
				
			}
		});

		$('#credits_payment_principal_view').textbox({
			onChange: function(value){
				// alert(looppokok);
				if(looppokok == 0){
					looppokok = 1;
					return;
				}
				
				if(looppokok ==1){
					looppokok = 0;
					nilai = (value);
					$('#angsuran_pokok').textbox('setValue',value);
					$('#credits_payment_principal_view').textbox('setValue',nilai);
					hitungpayment('pokok');
					console.log('nilai');
					console.log(nilai);

				}else{
					looppokok = 1;
					return;
				}
			}
		});
	});

    


	// $(document).ready(function(){
 //        $("#Save").click(function(){
	// 		var member_principal_savings = $("#member_principal_savings").val();
			
	// 		if(member_principal_savings == ''){
	// 			return confirm("Simpanan Pokok kosong, apakah yakin ingin disimpan ?");
	// 		} 	
	// 	});
 //    });
</script>
<?php echo form_open('AcctCashPayments/AcctCashPaymentsProcess',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
<?php 
	$segement3 						= $this->uri->segment(3);
	
	
	$segement4 						= $this->uri->segment(4); ?>
<?php 
	$credits_payment_date 			= date('Y-m-d');
	$date1 							= date_create($credits_payment_date);
	$date2 							= date_create($credit_account['credits_account_payment_date']);
	$angsuranke 					= $credit_account['credits_account_payment_to'] + 1;
	$tambah 						= $pembayaran_ke.'month';


	$credits_payment_day_of_delay 	= date_diff($date1, $date2)->format('%d');

	$credits_payment_fine 			= $credits_payment_day_of_delay * $credit_account['credits_fine'];

	if($credit_account['credits_account_net_price'] == 0){
		$harga_pokok 				= $credit_account['credits_account_financing'];
	} else {
		$harga_pokok 				= $credit_account['credits_account_net_price'] - $credit_account['credits_account_um'];
	}

?>
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctCashPayments/addCashlessPayment/">
				Angusran Non tunai
			</a>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Angsuran Non Tunai
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctCashPayments/indCashLessPayment" class="btn btn-default btn-sm">
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

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Akad<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="20%"><input type="text"  class="easyui-textbox" size="4" name="credits_account_serial" id="credits_account_serial" autocomplete="off" value="<?php echo set_value('credits_account_serial', $credit_account['credits_account_serial']);?>" style="width: 50%" readonly/> &nbsp<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#akadlist">Search</a></td>
									</tr>
									<tr>
										<td width="35%">Pembiayaan<span class="required">*</span></td>
										<td width="5%"></td>
										<input type="hidden" size="4" name="credits_account_id" id="credits_account_id" autocomplete="off" value="<?php echo set_value('credits_account_id', $credit_account['credits_account_id']);?>" style="width: 50%" readonly/>
										<input type="hidden" size="4" name="credits_id" id="credits_id" autocomplete="off" value="<?php echo set_value('credits_id', $credit_account['credits_id']);?>" style="width: 50%" readonly/>
										<?php if($segement4 != ''){ ?>
											<input type="hidden" size="4" name="savings_account_id" id="savings_account_id" autocomplete="off" value="<?php echo set_value('savings_account_id', $saving_account['savings_account_id']);?>" readonly/>
										<?php } else { ?>
											<input type="hidden" size="4" name="savings_account_id" id="savings_account_id" autocomplete="off" value="<?php echo set_value('savings_account_id', $credit_account['savings_account_id']);?>" readonly/>
										<?php } ?>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_used" id="credits_account_used" autocomplete="off" value="<?php echo set_value('credits_name', $credit_account['credits_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Rek Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="20%">
											<?php if($segement4 != ''){ ?>
												<input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo set_value('savings_account_no', $saving_account['savings_account_no']);?>" style="width: 50%" readonly/>
											<?php } else { ?>
												<input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo $credit_account['savings_account_no'];?>" style="width: 50%" readonly/>
											<?php } ?>
											 &nbsp <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#simpananlist">Search</a> 
										</td>
									</tr>
									<tr>
										<td width="35%">Nama<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text"  class="easyui-textbox" size="4" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $credit_account['member_name']);?>" style="width: 50%" readonly/> </td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
								<input type="text"  class="easyui-textbox" size="4" name="member_address" id="member_address" autocomplete="off" value="<?php echo set_value('member_address', $credit_account['member_address']);?>" style="width: 50%" readonly/> 
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Tabungan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off"  style="width: 70%" value="<?php echo set_value('savings_account_last_balance', number_format($saving_account['savings_account_last_balance']));?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jumlah Angsuran<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="jumlah_angsuran_view" id="jumlah_angsuran_view" autocomplete="off"  style="width: 70%" value="<?php echo number_format($credit_account['credits_account_payment_amount'], 2);?>"/>
											<input type="text" class="easyui-textbox" name="jumlah_angsuran" id="jumlah_angsuran" autocomplete="off"  style="width: 70%" value="<?php echo $credit_account['credits_account_payment_amount'];?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Angsuran Pokok (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_payment_principal_view" id="credits_payment_principal_view" autocomplete="off" value="<?php echo number_format($credit_account['credits_account_principal_amount'], 2);?>" style="width: 70%"/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_principal" id="credits_payment_principal" autocomplete="off" value="<?php echo $credit_account['credits_account_principal_amount'];?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Angsuran Margin (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_payment_margin_view" id="credits_payment_margin_view" autocomplete="off" value="<?php echo number_format($credit_account['credits_account_margin_amount'], 2);?>" style="width: 70%"/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_margin" id="credits_payment_margin" autocomplete="off" value="<?php echo $credit_account['credits_account_margin_amount'];?>" />
										</td>
									</tr>
									
									<tr>
										<td width="35%">Total(Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="total_view" id="total_view" autocomplete="off" value="<?php echo number_format($credit_account['credits_account_payment_amount'], 2);?>" style="width: 70%" readonly/>
											<input type="hidden" class="easyui-textbox" name="total" id="total" autocomplete="off" value="<?php echo $credit_account['credits_account_payment_amount'];?>" readonly/>
										</td>
									</tr>
	
									<input type="hidden" class="form-control" name="harga_pokok" id="harga_pokok" autocomplete="off" readonly value="<?php echo $harga_pokok;?>"/>
									<input type="hidden" class="form-control" name="harga_margin" id="harga_margin" autocomplete="off" readonly value="<?php echo $credit_account['credits_account_margin'];?>"/>
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									
									<tr>
										<td width="35%">Tanggal Realisasi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_date" id="credits_account_date" autocomplete="off" value="<?php echo tgltoview($credit_account['credits_account_date']);?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_payment_date" id="credits_account_payment_date" autocomplete="off" value="<?php echo tgltoview($credit_account['credits_account_payment_date']);?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Angsuran<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_date" id="credits_payment_date" autocomplete="off" value="<?php echo date('d-m-Y');?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Angsuran Ke</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_to" id="credits_payment_to" autocomplete="off"  style="width: 70%" value="<?php echo $angsuranke;?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Keterlambatan (Hari)</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_day_of_delay" id="credits_payment_day_of_delay" autocomplete="off"  style="width: 70%" value="<?php echo set_value('pembayaran_ke',$credits_payment_day_of_delay);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Denda (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_payment_fine_view" id="credits_payment_fine_view" autocomplete="off" style="width: 70%" value="<?php echo number_format($credits_payment_fine, 2); ?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_fine" id="credits_payment_fine" autocomplete="off" value="<?php echo $credits_payment_fine; ?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Sisa Pokok<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_last_balance_principal" id="credits_account_last_balance_principal" autocomplete="off" value="<?php echo set_value('credits_account_last_balance_principal', number_format($credit_account['credits_account_last_balance_principal']));?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Sisa Margin<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" value="<?php echo set_value('credits_account_last_balance_margin', number_format($credit_account['credits_account_last_balance_margin']));?>" style="width: 70%" readonly/></td>
									</tr>
									
									
								</table>
									<div class="col-md-12" style='text-align:left'>
									<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
									<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
								</div>
							</div> 
						</div>


						<!-- 
							<div class="col-md-12">
								<div class="form-group">
									<input type="text" class="form-control" name="member_heir" id="member_heir" autocomplete="off" value="<?php echo set_value('member_heir',$coremember['member_heir']);?>"/>
									<label class="control-label">Ahli Waris<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<input type="text" class="form-control" name="member_family_relationship" id="member_family_relationship" autocomplete="off" value="<?php echo set_value('member_family_relationship',$coremember['member_family_relationship']);?>"/>
									<label class="control-label">Hub. Keluarga<span class="required">*</span></label>
								</div>
							</div>
						</div> -->

						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar Pembayaran
					</div>
				</div>
			
					<div class="portlet-body">
						<div class="form-body">
							<div id="tabelpembayaran">
								<table class="table table-striped table-hover">
								<tr>
									<th>Ke</th>
									<th>Tgl Angsuran</th>
									<th>Angsuran Pokok</th>
									<th>Angsuran Margin</th>
									<th>Saldo Pokok</th>
									<th>Saldo Margin</th>
								</tr>
								</table>
							</div>
							
						</div>
					</div>
				
			 </div>
		</div>
	</div>
</div>
<!-- 
DataTable
!-->
<div id="akadlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Member List</h4>
      </div>
      <div class="modal-body">
<table id="akadtable">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>No. Akad</th>
            <th>Member Nama</th>
            <th>Member No</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>

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
<div id="simpananlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Member List</h4>
      </div>
      <div class="modal-body">
<table id="simpantable">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>Member No</th>
            <th>Member Nama</th>
            <th>No Rekening</th>
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
<?php echo form_close(); ?>

<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#akadtable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('AcctCashPayments/akadlist/'.$segement3.'/'.$segement4)?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
	table = $('#simpantable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
		
            "url": "<?php echo site_url('AcctCashPayments/simpananlist/'.$segement3.'/'.$segement4)?>",
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
