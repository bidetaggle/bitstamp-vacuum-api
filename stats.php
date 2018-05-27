<?php include_once('config.php'); ?>

<?php
if(!isset($_GET['pair']) || !in_array($_GET['pair'], PAIRS_LIST))
	$_GET['pair'] = "btc_usd";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>bitstamp vacuum</title>
	<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="node_modules/echarts/dist/echarts.min.js"></script>
</head>
<body>
	<h1><?php echo $_GET['pair']; ?></h1>

	<?php foreach (PAIRS_LIST as $pair): ?>
		<a href="?pair=<?php echo $pair ?>">
			<?php echo $pair; ?>
		</a>
	<?php endforeach; ?>

	<input type="hidden" name="pair" value="<?php echo $_GET['pair']; ?>">
	<div id="stored-transactions" style="width: 1200px;height:400px;"></div>
	<div id="storage-rate" style="width: 1200px;height:400px;"></div>

<script type="text/javascript">
// based on prepared DOM, initialize echarts instance
var myChart = echarts.init(document.getElementById('stored-transactions'));
var chart_storageRate = echarts.init(document.getElementById('storage-rate'));
var selectedPair = document.getElementsByName("pair")[0].value;

var options = function(x,y,title){
	return {
		tooltip: {
			trigger: 'axis',
			position: function (pt) {
				return [pt[0], '10%'];
			}
		},
		title: {
			left: 'center',
			text: title,
		},
		toolbox: {
			feature: {
				dataZoom: {
					yAxisIndex: 'none'
				},
				restore: {},
				saveAsImage: {}
			}
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: x
		},
		yAxis: {
			type: 'value',
			//axisLine: {onZero: 0},
			onZero: 0,
			boundaryGap: ['0%', '0%'],
			scale: true
		},
		series: [
			{
				type:'line',
				smooth:true,
				symbol: 'none',
				sampling: 'average',
				itemStyle: {
					normal: {
						color: 'rgb(255, 70, 131)'
					}
				},
				areaStyle: {
					normal: {
						color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
							offset: 0,
							color: 'rgb(255, 158, 68)'
						}, {
							offset: 1,
							color: 'rgb(255, 70, 131)'
						}])
					}
				},
				data: y
			}
		]
	}
};

var data;
const nb_X = 100;
var transactionsRateX = [];
let now = Math.floor(Date.now() / 1000);
for(let i=now-nb_X ; i<now ; i++){
	transactionsRateX.push(i);
}

var transactionsRateY = Array(nb_X).fill(0);

$.get('https://luteciacorp.ovh/api.php?pair='+selectedPair).done(function(data){
	data = JSON.parse(data);

	var x = data.reverse().map((value, index, array) => {
		return value.id_local;
	});
	var y = data.map((value, index, array) => {
		return value.price;
	});

	myChart.setOption(options(x,y, 'last stored transactions (price)'));

	setInterval(function(){
		console.log(parseInt(data[data.length-1].timestamp)+1);
		let time = parseInt(data[data.length-1].timestamp)+1;
		$.get('https://luteciacorp.ovh/api.php?pair='+selectedPair+'&from_timestamp='+time).done(function(d){
			let nbTransactions = 0;
			JSON.parse(d).forEach(function(i,n){
				data.shift();
				data.push(i);
				console.log(data);
				nbTransactions++;
			});

			var x = data.map((value, index, array) => {
				return value.id_local;
			});
			var y = data.map((value, index, array) => {
				return value.price;
			});

			myChart.setOption(options(x,y, 'last stored transactions (price)'));

			transactionsRateY.shift();
			transactionsRateY.push(nbTransactions);
			transactionsRateX.shift();
			transactionsRateX.push(Math.floor(Date.now() / 1000));

			chart_storageRate.setOption(options(transactionsRateX,transactionsRateY, 'storage rate (transactions/sec)'));
		});
	}, 1000);
});
</script>

</body>
</html>
