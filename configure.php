<?php
	// 自訂變數(Variable)
	$hostname = "localhost";		/* MySQL的主機名稱 */
	$username = "root";		/* MySQL的使用者名稱 */
	$password = "";		/* MySQL的使用者密碼 */
	$database = "project";			/*資料庫名稱*/

	//	mysqli_connect連線動作可在此檔案中完成
	//	$link = mysqli_connect($hostname, $username, $password, $database) OR die("Error: Unable to connect to MySQL.");

	// 亦可自行定義常數(Constant)，請參考 http://php.net/manual/en/function.define.php
	define('HTTP_SERVER', 'http://www.ntunhs.edu.tw/');

?>
