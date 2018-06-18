<?php include_once('../config.php'); ?>

<?php
if(!isset($_GET['pair']) || !in_array($_GET['pair'], PAIRS_LIST))
	$_GET['pair'] = "ALL";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>bitstamp vacuum</title>
	<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="node_modules/echarts/dist/echarts.min.js"></script>

    <script type="text/javascript" src="jsfiddle/Eonasdan-d59h8jpj/moment-with-locales.js"></script>
    <script type="text/javascript" src="jsfiddle/Eonasdan-d59h8jpj/bootstrap-datetimepicker.js"></script>
    <link rel="stylesheet" href="jsfiddle/Eonasdan-d59h8jpj/bootstrap-datetimepicker.css">
</head>
<body>
	<h1><?php echo $_GET['pair']; ?></h1>

	<?php foreach (PAIRS_LIST as $pair): ?>
		<a href="?pair=<?php echo $pair ?>">
			<?php echo $pair; ?>
		</a>
	<?php endforeach; ?>

	<input type="hidden" name="pair" value="<?php echo $_GET['pair']; ?>">
	<div id="candlesticks" style="width: 1600px;height:400px;"></div>

    <!-- padding for jsfiddle -->
<div class="row">
    <div class="col-md-12">
         <h6>datetimepicker1</h6>

        <div class="form-group">
            <div class="input-group date" id="datetimepicker1">
                <input type="text" class="form-control" />	<span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
            </div>
        </div>
        <button id="btn1">Set</button>
    </div>
</div>

<script type="text/javascript">
// based on prepared DOM, initialize echarts instance
var myChart = echarts.init(document.getElementById('candlesticks'));
var selectedPair = document.getElementsByName("pair")[0].value;

$.get('https://luteciacorp.ovh/candlesticks.php?range=60&from_timestamp=1529316000&to_timestamp=1529319600&pair='+selectedPair).done(function(data){
	data = JSON.parse(data);

    console.log(data);
    var x = data.map((value, index, array) => {
        let date = new Date(value.timestamp*1000);
        return date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    });
    console.log(x);
    convertedData = data.map((value, index, array) => {
        return [value.opening, value.closing, value.low, value.high];
    });

    console.log(convertedData);

    option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            }
        },
        xAxis: {
            data: x
        },
        yAxis: {
            scale: true
        },
        series: [{
            type: 'k',
            data: convertedData
        }]
    };

	myChart.setOption(option);
});
</script>

</body>
</html>
