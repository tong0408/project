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
<div style="display:inline-block;">
<button class="btn">月</button>
<button class="btn">周</button>
<div class="container1">
<button class="btn2">全榖雜糧類</button>
<button class="btn2">豆魚蛋肉類</button>
<button class="btn2">乳品類</button>
<button class="btn2">蔬菜類</button>
<button class="btn2">水果類</button>
<button class="btn2">油脂與堅果種子類</button>
<canvas id="myChart" ></canvas>
</div>
</div>
<?php
    //連接歷史紀錄資料表（限制日期）
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $query = "SELECT `dishID` FROM `history` WHERE `UID`='$userID' limit 7";
    $result = $link->query($query);	
    foreach($result as $row){
        if($row["date"]!=null){
			$dishID = $row['dishID'];
        }
    //取得所有需要的資料
		
		//取得菜ID使用的食材ID
		$query = "SELECT `iID`, `portion` FROM recipe where dishID='$dishID'";
		$re = $link->query($query);
		foreach ($re as $r){
			$iID=$r['iID'];

			//從食材ID取得食材類別ID和食材名稱<使用>
			$query = "SELECT NID FROM ingredients where iID='$iID'";
			$re = $link->query($query);
			foreach ($re as $r){
				$iID_NID=$r['NID'];

				//從食材類別ID取得食材類別名稱<使用>
				$query = "SELECT * FROM nutrient where NID='$iID_NID'";
				$re = $link->query($query);
				foreach ($re as $r){
					$iID_NID_Name=$r['category'];	
				}							
			}
		}
	}
?>
<script>
//日期自動變化
var Today=new Date();
var month=Today.getMonth()+1;
var d=Today.getDate();
var date=[];
for(var i=1;i<32;i++){
	date.push(i);
}
//改善的地方為:如果是1號與31號 怎麼半?
	var mmdd1=Today.getMonth()+1+'/'+(Today.getDate()-6);
	var mmdd2=Today.getMonth()+1+'/'+(Today.getDate()-5);
	var mmdd3=Today.getMonth()+1+'/'+(Today.getDate()-4);
	var mmdd4=Today.getMonth()+1+'/'+(Today.getDate()-3);
	var mmdd5=Today.getMonth()+1+'/'+(Today.getDate()-2);
	var mmdd6=Today.getMonth()+1+'/'+(Today.getDate()-1);
	var mmdd7=Today.getMonth()+1+'/'+Today.getDate();


const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [mmdd1, mmdd2, mmdd3, mmdd4, mmdd5, mmdd6,mmdd7 ],//改日期
        datasets: [{
            label: '營養素圖表',
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
</script>
</body>
</html>