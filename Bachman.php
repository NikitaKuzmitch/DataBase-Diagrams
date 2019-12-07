<!DOCTYPE html>
<?php
include 'Configs.php';
session_start();
if (isset($_SESSION['db']))
	{
		$DatabaseName = $_SESSION['db'];
   		$db = mysqli_connect($HostName, $HostUser, $HostPass, $DatabaseName) or die(mysqli_error()); //подключение к БД
		$sql = mysqli_query($db, "SHOW TABLES FROM $DatabaseName"); //запрос
		$data = array();
		$datalist = array();
		$TABLE_NAME ;
		while ($rows = mysqli_fetch_array($sql)) 
			{ // массив с данными
				//заполнение массива таблиц
				$row = array (
				"key" => $rows[0],
				"color" => 'lightblue');
					array_push($data, $row);
 //цикл заполение массива метда  
  $listtable = mysqli_query($db, "SELECT DISTINCT TABLE_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE	REFERENCED_TABLE_SCHEMA = '".$DatabaseName."' AND REFERENCED_TABLE_NAME = '".$rows[0]."';");
		while ($rowlist = mysqli_fetch_array($listtable)) 
			{ 
				$masslist = array (
				"from" => $rowlist['REFERENCED_TABLE_NAME'],
				"to" => $rowlist['TABLE_NAME']);
					array_push($datalist, $masslist);
			}
			}
	}
 
?>
<html>
<head>
  <meta charset="UTF-8">
  <title>Диаграмма Бахмана</title>
  <meta name="description" content="An almost minimal diagram using a very simple node template and the default link template." />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Copyright 1998-2019 by Northwoods Software Corporation. -->

  <script src="go.js"></script>

  <script id="code">
    function init() {
     

      var $ = go.GraphObject.make;  // for conciseness in defining templates

      myDiagram = $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
        {
          "undoManager.isEnabled": true  // enable undo & redo
        });

      // define a simple Node template
      myDiagram.nodeTemplate =
        $(go.Node, "Auto",  // the Shape will go around the TextBlock
		          $(go.Shape, "RoundedRectangle", { strokeWidth: 2, fill: "white",margin: 40 },
            // Shape.fill is bound to Node.data.color
            new go.Binding("fill", "color")),
          $(go.TextBlock,
            { margin: 55 },  // some room around the text
            // TextBlock.text is bound to Node.data.key
            new go.Binding("text", "key"))
        );

      // but use the default Link template, by not setting Diagram.linkTemplate
var nodeDataArray =<?php echo json_encode($data);?>;
var nodeDatalist =<?php echo json_encode($datalist);?>;
      // create the model data that will be represented by Nodes and Links
           myDiagram.model = new go.GraphLinksModel(nodeDataArray,nodeDatalist);
    }
  </script>
</head>
<body onload="init()">

<header>
<div class="head">
    <div class="block">
  <p>База данных: <b class="Namebd"><?php echo $DatabaseName;?></b></p>
  <p>Диаграмма Бахмана</p>
  </div>
<div style="text-align:center;">
  <a href="index.php" class="logo"><img src="diagram.jpg" style="margin-right:10px;"> Database Diagrams</a></div>

  <nav>
    <ul class="topmenu">
   				<li><a href="Bachman.php" class="selected">Диаграмма Бахмана</a></li>
				<li><a href="table.php">Таблица</a></li>
				<li><a href="eer.php">ER-диаграмма</a></li>
				<li><a href="dbgl.php">DBDGL-диаграмма</a></li>
    </ul>
  </nav>
</div>
</header>
 

<div id="sample" class="main">
  <div id="myDiagramDiv" clas style=" width:100%; height:1000px"></div>
    
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

    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
position: relative;
    z-index: 55;
   
    background: white;
    clear: both;
}
</Style>