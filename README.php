<!DOCTYPE html>
<body onload="init()">
<?php 
include 'Configs.php';
session_start();
    if (isset($_SESSION['db'])) 
	{
        $DatabaseName = $_SESSION['db'];?>
<header>
<div class="head">
    <div class="block">
  <p>База данных: <b class="Namebd"><?php echo $DatabaseName;?></b></p>
  <p>Таблицы</p>
  </div>
<div style="text-align:center;">
  <a href="index.php" class="logo"><img src="diagram.jpg" style="margin-right:10px;"> Database Diagrams</a></div>

  <nav>
    <ul class="topmenu">
   				<li><a href="Bachman.php" class="selected">Диаграмма Бахмана</a></li>
				<li><a href="table.php">Таблицы</a></li>
				<li><a href="eer.php">ER-диаграмма</a></li>
				<li><a href="dbgl.php">DBDGL-диаграмма</a></li>
    </ul>
  </nav>
</div>
</header>
<div id="sample" class="main">
<?php

		$db = mysqli_connect($HostName, $HostUser, $HostPass, $DatabaseName) or die(mysqli_error()); //подключение к БД
		$sql = mysqli_query($db, "SHOW TABLES FROM $DatabaseName"); //запрос
		$data = array();
			while ($rows = mysqli_fetch_array($sql)) 
				{ // массив с данными
					$rs = mysqli_query($db, "SHOW COLUMNS FROM ".$rows[0]."");
					$num =1;	 //запрос на выборку данных и выбраной таблицы?>
<div style="float: left;
    margin:0px 20px 20px 20px;
 ">

     <table border='1'  bgcolor="#fff" >
	 <tr>
	 <td> <?php echo $rows[0]; ?></td>
	 <td colspan="3"> <?php echo $rows[0]; ?></td>
	 </tr>
	  <tr>
	 <td>Имя столбца</td>
	 <td > Тип данных</td>
	 <td>PK</td>
	  <td >FK</td>
	 	 </tr>

    <?php
	$listtable = mysqli_query($db, "SELECT DISTINCT CONSTRAINT_NAME,COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE	REFERENCED_TABLE_SCHEMA = '".$DatabaseName."'  AND REFERENCED_TABLE_NAME = '".$rows[0]."';");
       $rers = mysqli_query($db, "SELECT * FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA ='".$DatabaseName."'  AND CONSTRAINT_NAME <>'PRIMARY' AND TABLE_NAME ='".$rows[0]."';"); 
				while($row_rs = mysqli_fetch_assoc($rs)) // массив с данными
					{
						$rowdd = mysqli_fetch_assoc($listtable);
						$rowddew = mysqli_fetch_assoc($rers);
    ?>
        <tr>
   	 <td> <?php echo $row_rs['Field']; ?></td>
	 	 <td> <?php echo $row_rs['Type']; ?></td>
		 	 <td> <?php if($row_rs['Key'] !='MUL') echo  $row_rs['Key']; ?></td>
			
			 <?php if($row_rs['Field'] == $rowddew['COLUMN_NAME'] || $row_rs['Key'] =='MUL')
					{?>
			 	 <td>FK<?php echo $num;$num++;?></td>
				<?php }
				else{ ?> <td></td><?php } 
					} ?>
				        </tr>
 </table>
</div>
    <?php 
				}
	}?>
 

   </div>
   </body>
</html>
<Style>
.head
{
   position: fixed;
    height: 100px;
    top: 0;
    width: 100%;
    z-index: 100;
	background:white;
	border-bottom:1px black solid;
}
.logo {
  color: black;
  font-family: 'Playfair Display', serif;
  font-size: 2.5em;
  padding: 20px 0;
  text-decoration-line: none;
  text-align:center;
}
nav {
  display: table;
  margin: 0 auto;
}
nav ul {
  list-style: none;

}
.topmenu:after {
  content: "";
  display: table;
  clear: both;
}
.topmenu > li {

  float: left;
  position: relative;
  font-family: 'Open Sans', sans-serif;
}
.topmenu > li > a {
font-family: 'Playfair Display', serif;
  font-size: 20px;
    font-weight: bold;
    color: #00008b;
	text-decoration-line: none;
		border-bottom: 2px #00008b solid;
  padding: 1px 1px;
  margin-left:15px;
}

.block
{
font-family: 'Playfair Display', serif;
  font-size: 18px;
    font-weight: bold;
    color: #404040;
	text-decoration-line: none;
    position: absolute;
}
.Namebd
{
text-transform: uppercase;
}
.main {
    width: 100%;
    height: 100%;
padding-top:100px;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
position: relative;
    z-index: 55;
   
    background: white;
    clear: both;
}
</Style>

    

