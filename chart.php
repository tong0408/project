<?php
	session_start();
	include("configure.php");

	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
		echo "<script>alert('請先登入唷！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	} else {
		$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
		$userID=$_SESSION['userID'];
		$query = "SELECT * FROM `user` WHERE `userID`='$userID'";
		$result = $link->query($query);
	
		#獲取現在登入者的帳號密碼
		foreach ($result as $row) {
			$userID = $row["userid"];
			$Name = $row["name"];
		}
	}
?>

<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.2/dist/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/plugin/weekday.min.js"></script>
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
	
	body, html {
		height: 100%;
		line-height: 1.8;
		text-align:center;
		background-color:#FFD79B;
	}
	.container1{
		width:850px; 
		height:500px; 
		background:#FFF; 
		margin:auto;
		border-radius:20px;
	}
	
	.btn{
		border-radius:10px;
		background-color: #FFF;
		border:none;
		margin:20px;
		padding:10px 30px;
	}
	
	.btn:hover{
		background-color: #FFB03B;
		cursor:pointer;
	}
	
	.btn2{
		border-radius:10px;
		background-color: #FFF;
		border:1px solid #FFB03B;
		margin:10px;
		padding:10px 20px;
	}
	
	.btn2:hover{
		background-color: #FFB03B;
		cursor:pointer;
	}
</style>
</head>
<body>
<div class="nav nav-tabs" id="nav-tab" role="tablist">
	 <!--<div style="display:inline-block;">這個語法會讓切換失效，排版定位可能要找別的方法，或是把boostrap裡面tab切換的css寫進我們自己的css-->
		<button class="btn nav-link" data-bs-toggle="tab" data-bs-target="#nav-month" type="button" role="tab" aria-controls="nav-month" onclick="clickMonth()">月</button>
		<button class="btn nav-link" data-bs-toggle="tab" data-bs-target="#nav-week" type="button" role="tab" aria-controls="nav-week" onclick="clickWeek()">週</button>
		<div class="container1">
			<button class="btn2" onclick="click1()" id="1">全榖雜糧類</button>
			<button class="btn2" onclick="click2()" id="2">豆魚蛋肉類</button>
			<button class="btn2" onclick="click3()" id="3">乳品類</button>
			<button class="btn2" onclick="click4()" id="4">蔬菜類</button>
			<button class="btn2" onclick="click5()" id="5">水果類</button>
			<button class="btn2" onclick="click6()" id="6">油脂與堅果種子類</button>
				<div class="tab-pane fade show active" id="nav-month" role="tabpanel" aria-labelledby="nav-cookie0-tab" display="none">
					<canvas id="myChart" ></canvas>
					月的圖表
				</div>
				<div class="tab-pane fade show active" id="nav-week" role="tabpanel" aria-labelledby="nav-cookie0-tab" display="block">
					<canvas id="myChart" ></canvas>
					週ㄉ圖表
				</div>
		</div>
	<!--</div>-->
</div>
<script>
var sqldate=[];
var sqlportion=[];
var iID_NID=[];
</script>
<?php
    //連接歷史紀錄資料表
	$count=0;
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $query = "SELECT `dishID` ,`date` FROM `history` WHERE `UID`='$userID' ORDER BY `ID` DESC";
    $result = $link->query($query);
	
    foreach($result as $row){
        
		$dishID = $row['dishID'];
		$sqldate=$row['date'];
		
    //取得所有需要的資料
		$query = "SELECT count(`ID`),`iID`, `portion` FROM recipe where dishID='$dishID'";
		$res = $link->query($query);
		$c = $res->fetchColumn();
		
		$count+=$c;
		
		//取得dishID使用的iID&portion
		$query = "SELECT `iID`, `portion` FROM recipe where dishID='$dishID'";
		$re = $link->query($query);
			
		foreach ($re as $r){
			$iID=$r['iID'];
			$portion=$r['portion'];
			
			//從iID取得NID和食材名稱<使用>
			$query = "SELECT NID FROM ingredients where iID='$iID'";
			$re = $link->query($query);
			foreach ($re as $r){
				$iID_NID=$r['NID'];
				
?>
				<script>
				iID_NID.push('<?php echo $iID_NID;?>');
				</script>
<?php
			}
?>
			<script>
			sqlportion.push('<?php echo $portion;?>');
			</script>
<?php
		}
?>
		<script>
		sqldate.push('<?php echo $sqldate;?>');
		</script>
<?php
	}
?>

<script>
var count='<?=$count?>';
//document.write(count+'===');

//日期自動變化
var Today = new Date();
var t1 = new Date(Today.setDate(Today.getDate())).toLocaleDateString().replaceAll("/","-");
var t2 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t3 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t4 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t5 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t6 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t7 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");

var Today1 = new Date();
var m1 =new Date(Today1.setMonth(Today1.getMonth())).toLocaleDateString().replaceAll("/","-");
var m2 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m3 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m4 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m5 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m6 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m7 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");

var mmdd=[];
var portion=[];
var sum=0;

//切換
//問題2:切換月or週&營養素

//clickMouth
function clickMonth(){
	//放日期
	mmdd.push(m1.substr(5,1));
	mmdd.push(m2.substr(5,1));
	mmdd.push(m3.substr(5,1));
	mmdd.push(m4.substr(5,1));
	mmdd.push(m5.substr(5,1));
	mmdd.push(m6.substr(5,1));
	mmdd.push(m7.substr(5,1));
	function click1(){
		for(var i=0;i<7;i++){
			if(mmdd[i]<10){
				if(mmdd[i]==sqldate[i].substr(6,1)){
					for(var m=0;m<count;m++){
						if(iID_NID[m]==1){
							
							//問題1:無法加總
							if(portion[i]==null){
								portion[i]=sqlportion[m];
							}else{
								portion[i]=portion[i]+sqlportion[m];
							}
							document.write(portion[0]);
						}
					}
				}
			}else{
				if(mmdd[i]==sqldate[i]){
					for(var m=0;m<count;m++){
						if(iID_NID[m]==1){
							
							//問題1:無法加總
							if(portion[i]==null){
								portion[i]=sqlportion[m];
							}else{
								portion[i]=portion[m]+sqlportion[m];
							}
						}
					}
				}
			}
		}
	}
	function click2(){
		
	}
	function click3(){
		
	}
	function click4(){
		
	}
	function click5(){
		
	}
	function click6(){
		
	}

	const ctx = document.getElementById('myChart').getContext('2d');
	const myChartMonth = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: [mmdd[0],mmdd[1], mmdd[2], mmdd[3], mmdd[4], mmdd[5], mmdd[6]],//改日期
			datasets: [{
				label: '營養素圖表-月',
				data: [12, 19, 13, 5, 12, 13,15],//改數值
				backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)',
					'rgba(220, 31, 224, 0.2)'
				],
				borderColor: [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)',
					'rgba(220, 31, 224, 1)'
				],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	myChartMonth.render();
}

//clickWeek
function clickWeek(){

	mmdd.push(t1);
	mmdd.push(t2);
	mmdd.push(t3);
	mmdd.push(t4);
	mmdd.push(t5);
	mmdd.push(t6);
	mmdd.push(t7);
	function click1(){
		for(var i=0;i<7;i++){
			if(mmdd[i]<10){
				if(mmdd[i].substr(5,4)==sqldate[i].substr(6,4)){
					document.write(mmdd[i].substr(5,4));
					for(var m=0;m<count;m++){
						if(iID_NID[m]==1){
							if(portion[i]==null){
								portion[i]=sqlportion[m];
							}else{
								portion[i]=portion[i]+sqlportion[m];
							}
						}
					}
				}
			}else{
				if(mmdd[i]==sqldate[i]){
					for(var m=0;m<count;m++){
						if(iID_NID[m]==1){
							if(portion[i]==null){
								portion[i]=sqlportion[m];
							}else{
								portion[i]=portion[i]+sqlportion[m];
							}
						}
					}
				}
			}
		}
	}
	function click2(){
		
	}
	function click3(){
		
	}
	function click4(){
		
	}
	function click5(){
		
	}
	function click6(){
		
	}

	const ctx = document.getElementById('myChart').getContext('2d');
	const myChartWeek = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: [mmdd[0],mmdd[1], mmdd[2], mmdd[3], mmdd[4], mmdd[5], mmdd[6]],//改日期
			datasets: [{
				label: '營養素圖表-週',
				data: [20, 19, 1, 6, 3, 9,15],//改數值
				backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)',
					'rgba(220, 31, 224, 0.2)'
				],
				borderColor: [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)',
					'rgba(220, 31, 224, 1)'
				],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	myChartWeek.render();
}
</script>
</body>
</html>