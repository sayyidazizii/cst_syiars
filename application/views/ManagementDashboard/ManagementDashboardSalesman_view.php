
<script type="text/javascript">
	base_url = '<?php echo base_url();?>';
		/*  setTimeout(function(){
       location.reload();
   },18000); */

   	
</script>
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

	<?php 
		foreach ($salessalesman  as $keySalesman => $valSalesman){
	?>
		#chartdivcolumnsalesman<?php echo $valSalesman['salesman_id']?> {
			width: 100%;
			height: 350px;
		}
	<?php 
		}
	?>
</style>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		

<!-- <h3 class="page-title">
	Dashboard
</h3> -->

<?php 
	$auth 	= $this->session->userdata('auth');

	

	/* print_r("week_to_date_difference ");
	print_r($week_to_date_difference);
	print_r("<BR> ");
	print_r("<BR> ");
	print_r("week_to_date_percentage ");
	print_r($week_to_date_percentage);
	print_r("<BR> ");
	print_r("<BR> ");
	exit; */
?>

<script>
	<?php 
		

		foreach ($salessalesman  as $keySalesman => $valSalesman){
			$today = date("Y-m-d");
			$data_salesinvoiceyearsalesman		= $this->ManagementDashboard_model->getSalesInvoiceYearSalesman($today, $valSalesman['salesman_id']);

			if (!empty($data_salesinvoiceyearsalesman)){
				foreach ($data_salesinvoiceyearsalesman  as $keyInvoiceYear => $valInvoiceYear){
					$sales_invoice_period 			= $valInvoiceYear['sales_invoice_period'];

					$total_amount_period1 			= $valInvoiceYear['total_amount_period'];

					$total_target_period1			= $this->ManagementDashboard_model->getTotalTargetPeriodSalesman($sales_invoice_period, $valSalesman['salesman_id']);

					if (empty($total_target_period1)){
						$total_target_period1 		= 0;
						$target_invoice_percentage	= 0;
					} else {
						$target_invoice_percentage 	= $total_amount_period1 / $total_target_period1 * 100;
					}

					$total_amount_period 			= $total_amount_period1 / 1000000;

					$total_target_period			= $total_target_period1 / 1000000;

					$salesinvoiceyearsalesman[$keyInvoiceYear] = array (
						'sales_invoice_period'		=> $sales_invoice_period,
						'total_amount_period'		=> $total_amount_period,
						'total_target_period'		=> $total_target_period,
						'target_invoice_percentage'	=> $target_invoice_percentage,
					);
				}
			} else {
				$salesinvoiceyearsalesman = array();
			}
	?>
		am4core.ready(function() {
			// Themes begin
			am4core.useTheme(am4themes_animated);
			am4core.useTheme(am4themes_moonrisekingdom);
			// Themes end

			// Create chart instance
			var chart = am4core.create("chartdivcolumnsalesman<?php echo $valSalesman['salesman_id']?>", am4charts.XYChart);

			var chartDataColumnOmzet = [
			<?php 
				if (!empty($salesinvoiceyearsalesman)){
					foreach ($salesinvoiceyearsalesman as $key => $val) { ?>
			{
				sales_invoice_period		: "<?php echo $val['sales_invoice_period']; ?>",
				total_amount_period			: <?php echo $val['total_amount_period']; ?>,
				total_target_period			: <?php echo $val['total_target_period']; ?>,
				target_invoice_percentage	: <?php echo $val['target_invoice_percentage']; ?>
			},
			<?php }} ?>
			];

			// Add data
			chart.data = chartDataColumnOmzet;

			// Create axes
			var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
			//dateAxis.renderer.grid.template.location = 0;
			//dateAxis.renderer.minGridDistance = 30;

			var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis1.title.text = "Sales";

			var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis2.title.text = "Percentage";
			valueAxis2.renderer.opposite = true;
			valueAxis2.renderer.grid.template.disabled = true;

			// Create series
			var series1 = chart.series.push(new am4charts.ColumnSeries());
			series1.dataFields.valueY = "total_target_period";
			series1.dataFields.dateX = "sales_invoice_period";
			series1.yAxis = valueAxis1;
			series1.name = "Target Sales";
			series1.tooltipText = "{name}\n[bold font-size: 16]${valueY}M[/]";
			series1.fill = chart.colors.getIndex(0);
			series1.strokeWidth = 0;
			series1.clustered = false;
			series1.columns.template.width = am4core.percent(40);

			var series2 = chart.series.push(new am4charts.ColumnSeries());
			series2.dataFields.valueY = "total_amount_period";
			series2.dataFields.dateX = "sales_invoice_period";
			series2.yAxis = valueAxis1;
			series2.name = "Actual Sales";
			series2.tooltipText = "{name}\n[bold font-size: 16]${valueY}M[/]";
			series2.fill = chart.colors.getIndex(0).lighten(0.5);
			series2.strokeWidth = 0;
			series2.clustered = false;
			series2.toBack();

			var series3 = chart.series.push(new am4charts.LineSeries());
			series3.dataFields.valueY = "target_invoice_percentage";
			series3.dataFields.dateX = "sales_invoice_period";
			series3.name = "Percentage";
			series3.strokeWidth = 2;
			series3.tensionX = 0.7;
			series3.yAxis = valueAxis2;
			series3.tooltipText = "{name}\n[bold font-size: 16]{valueY}[/] %";

			var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
			bullet3.circle.radius = 3;
			bullet3.circle.strokeWidth = 2;
			bullet3.circle.fill = am4core.color("#fff");



			// Add cursor
			chart.cursor = new am4charts.XYCursor();

			// Add legend
			chart.legend = new am4charts.Legend();
			chart.legend.position = "top";

			// Add scrollbar
			chart.scrollbarX = new am4charts.XYChartScrollbar();
			chart.scrollbarX.series.push(series1);
			chart.scrollbarX.series.push(series3);
			chart.scrollbarX.parent = chart.bottomAxesContainer;

			});
	<?php 
		}
	?>
</script>



<div class="row" id="load_content">
	<?php 
		/* print_r("salessalesman ");
		print_r($salessalesman); */

		foreach ($salessalesman  as $keySalesman => $valSalesman){
	?>
		<div class="col-md-6">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Sales Performance <?php echo $valSalesman['salesman_name']?>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class = "row">
							<div class = "col-md-12">
								<div id="chartdivcolumnsalesman<?php echo $valSalesman['salesman_id']?>"></div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-md-12">
								<table class="table table-striped table-bordered table-hover table-full-width">
									<tbody>
									<!-- 	<tr>
											<td class="tg-9wq8" rowspan="2">67%</td>
											<td class="tg-0pky">Omzet</td>
											<td class="tg-0pky">1000</td>
											<td class="tg-0pky"></td>
											<td class="tg-9wq8" rowspan="2">10%</td>
										</tr>
										<tr>
											<td class="tg-0pky">Target</td>
											<td class="tg-0pky">2000</td>
											<td class="tg-0pky"></td>
										</tr>
										<tr>
											<td class="tg-0pky">175000</td>
											<td class="tg-0pky">EC</td>
											<td class="tg-0pky">100</td>
											<td class="tg-0pky"></td>
											<td class="tg-9wq8" rowspan="2">45</td>
										</tr>
										<tr>
											<td class="tg-0pky">ratio</td>
											<td class="tg-0pky">EA</td>
											<td class="tg-0pky">45</td>
											<td class="tg-0pky"></td>
										</tr> -->
										<tr >
											<td width="20%" rowspan="2">
												<?php 
													$year_to_date_amount_percentage = $valSalesman['year_to_date_amount_percentage'];

													if ($year_to_date_amount_percentage < 0){
												?>
													<div style="text-align:center;font-weight:bold;font-size:16px;color:red">
														<?php echo number_format($year_to_date_amount_percentage, 2)?> %
													</div>
												<?php
													} else { 
												?>
													<div style="text-align:center;font-weight:bold;font-size:16px;color:green">
														<?php echo number_format($year_to_date_amount_percentage, 2)?> %
													</div>
												<?php 
													}
												?>
											</td>
											<td width="20%">
												<div style="text-align:left;font-weight:bold;font-size:12px">	
													Omzet
												</div>
											</td>
											<td width="25%">
												<div style="text-align:right;font-weight:bold;font-size:12px">
													<?php echo number_format($valSalesman['month_to_date_amount_salesman'], 2)?>
												</div>
											</td>
											<td width="15%"></td>
											<td width="20%" rowspan="2">
												<?php 
													$month_to_date_amount_salesman_percentage = $valSalesman['month_to_date_amount_salesman_percentage'];

													if ($month_to_date_amount_salesman_percentage < 0){
												?>
													<div style="text-align:center;font-weight:bold;font-size:16px;color:red">
														<?php echo number_format($month_to_date_amount_salesman_percentage, 2)?> %
													</div>
												<?php
													} else { 
												?>
													<div style="text-align:center;font-weight:bold;font-size:16px;color:green">
														<?php echo number_format($month_to_date_amount_salesman_percentage, 2)?> %
													</div>
												<?php 
													}
												?>
											</td>
										</tr>
										<tr>
											<td >
												<div style="text-align:left;font-weight:bold;font-size:12px">	
													Target
												</div>
											</td>
											<td >
												<div style="text-align:right;font-weight:bold;font-size:12px">
													<?php echo number_format($valSalesman['total_target_period'], 2)?>
												</div>
											</td>
											<td ></td>
										</tr>
										<tr>
											<td >
												<div style="text-align:right;font-weight:bold;font-size:12px">
													<?php echo number_format($valSalesman['customer_ecea_ratio'], 2) ?> 
												</div>			
											</td>
											<td >
												<div style="text-align:left;font-weight:bold;font-size:12px">	
													EC
												</div>
											</td>
											<td >
												<div style="text-align:right;font-weight:bold;font-size:12px">
													<?php echo $valSalesman['total_customer_ec']?>
												</div>
											</td>
											<td ></td>
											<td  rowspan="2">
												<div style="text-align:center;font-weight:bold;font-size:16px">
													<?php echo number_format($valSalesman['customer_ecea_percentage'], 2)?> %
												</div>		
											</td>
										</tr>
										<tr>
											<td >
												<div style="text-align:center;font-weight:bold;font-size:12px">	
													ratio
												</div>
											</td>
											<td >
												<div style="text-align:left;font-weight:bold;font-size:12px">	
													EA
												</div>
											</td>
											<td >
												<div style="text-align:right;font-weight:bold;font-size:12px">
													<?php echo $valSalesman['total_customer_ea']?>
												</div>
											</td>
											<td ></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php 
		}
	?>	
</div>