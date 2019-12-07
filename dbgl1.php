<!DOCTYPE html>

<?php
include 'Configs.php';
session_start();
if (isset($_SESSION['db'])) 
{
	$DatabaseName = $_SESSION['db'];
	$db = mysqli_connect($HostName, $HostUser, $HostPass, $DatabaseName)  or die(mysqli_error());
	$sql = mysqli_query($db, "SHOW TABLES FROM $DatabaseName"); //запрос

	$data = array();
	$tablelist = array();
	$list = array();
	$methodlist = array();
	$i = 1;

	while ($rows = mysqli_fetch_array($sql)) 
	{ // массив с данными
		$rs = mysqli_query($db, "SHOW COLUMNS FROM ".$rows[0]."");
		$table = $rows[0];
		$pk = 'PK';
		$fk = 'FK';
		$pknum =1;
		$fknum =1;
		$fknums = '';
		$pknm = 1;
		$rers = mysqli_query($db, "SELECT *
		FROM information_schema.KEY_COLUMN_USAGE
			WHERE TABLE_SCHEMA ='".$DatabaseName."'  
				AND CONSTRAINT_NAME <>'PRIMARY' 
					AND TABLE_NAME ='".$rows[0]."';"); 
				
		while ($row_rs = mysqli_fetch_array($rs)) 
		{
			$rowddew = mysqli_fetch_assoc($rers);
			if($row_rs['Key'] == 'PRI') 
			{
				$pk = "PK(".$pknum.")";
				$pknum = $pknum +1;
			}
			else 
			{	 
				$pk = '';
			}
			if($row_rs['Field'] == $rowddew['COLUMN_NAME'] || $row_rs['Key'] =='MUL')
			{
			 $fk = "FK".$fknum;
			 $fknum = $fknum +1;
			}
			else
			{
				 $fk = ' ';
			}
			$rowdd = array ( 'pk' => $pk , 'fk' => $fk, 'name' => $row_rs['Field']);
			array_push($tablelist, $rowdd);
			$listtable = mysqli_query($db, "SELECT 
			DISTINCT 
			  TABLE_NAME,
				REFERENCED_TABLE_NAME
			FROM
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE
			WHERE
				REFERENCED_TABLE_SCHEMA = 'study'
				AND TABLE_NAME = '".$rows[0]."' AND REFERENCED_COLUMN_NAME = '".$row_rs['Field']."' ;");
				
			if($row_rs['Key'] == 'PRI' && $pknm ==1) 
			{
				$rowdt = array ( 'key' => 'PK' , 'tbname' => $row_rs['Extra']);
				array_push($methodlist, $rowdt);
				$pknm = 0;
			}
			while($rowlist = mysqli_fetch_array($listtable))
			{	
				$haystack = $rowlist['CONSTRAINT_NAME'];
				$needle   = 'FK';
				$pos  = strripos($haystack, $needle);
				$key = '';
				if($pos  == false) 
				 {
					 $key = "FK".$fknums;
					 $fknums = $fknums +1;
				 }
				else
				 {
					$key = ' ';
				 }
				$tbname = "PK(".$rowlist['REFERENCED_TABLE_NAME'].")";
				$rowdt = array ( 'key' => $key , 'tbname' => $tbname);
				array_push($methodlist, $rowdt);
				$masslist = array ( 
				 "from"=>  $rowlist['TABLE_NAME'], 
				 "fromPort"=> $key, 
				 "to"=>  $rowlist['REFERENCED_TABLE_NAME'], 
				 "toPort"=>"PK", 
				 "relationship"=> "generalization" 
				 );
				 array_push($list, $masslist);
			}
		}
		$row = array (
		   "key" => $table,
			"fields" => $tablelist ,
			"method" => $methodlist ,
		);
		array_push($data, $row); 
		$tablelist = array();
		$methodlist = array();
		$d = 1;
		$i++;
	}
}
?>

<html>
<head>
  <meta charset="UTF-8">
  <title>Record Mapper</title>
  <meta name="description" content="A diagram for displaying and editing the N to M relationships from one set of objects to another set of objects." />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Copyright 1998-2019 by Northwoods Software Corporation. -->
  <script src="go.js"></script>
  <script id="code">
    function init() {
      if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
      var $ = go.GraphObject.make;  // for conciseness in defining templates
      myDiagram =
        $(go.Diagram, "myDiagramDiv",
          {
            validCycle: go.Diagram.CycleNotDirected,  // don't allow loops
            // For this sample, automatically show the state of the diagram's model on the page
            "undoManager.isEnabled": true
          });
      // This template is a Panel that is used to represent each item in a Panel.itemArray.
      // The Panel is data bound to the item object.
      var fieldTemplate =
        $(go.Panel, "TableRow",   // this Panel is a row in the containing Table
		$(go.TextBlock,
            { margin: new go.Margin(0, 5), column: 1, font: "13px sans-serif", alignment: go.Spot.Center },
            new go.Binding("text", "pk")),
			   $(go.TextBlock,
            { margin: new go.Margin(0, 5), column: 2, font: "13px sans-serif", alignment: go.Spot.Center },
            new go.Binding("text", "fk")),
			   $(go.TextBlock,
            { margin: new go.Margin(0, 5), column: 2, font: "13px sans-serif", alignment: go.Spot.Center },
            new go.Binding("text", "tbname")),
          $(go.TextBlock,
            {
              margin: new go.Margin(0, 5), column: 3, font: "bold 13px sans-serif",stroke: "black",
              alignment: go.Spot.Left,
              // and disallow drawing links from or to this text:
              fromLinkable: false, toLinkable: false
            },
            new go.Binding("text", "name")),
        ) ;
		    var methodTemplate =
        $(go.Panel, "TableRow",   // this Panel is a row in the containing Table
		 new go.Binding("portId", "key"),  // this Panel is a "port"
          {
            background: "transparent",  // so this port's background can be picked by the mouse
            fromSpot: go.Spot.Right,  // links only go from the right side to the left side
            toSpot: go.Spot.Left,
            // allow drawing links from or to this port:
            fromLinkable: true, toLinkable: true
          },
  $(go.TextBlock,
            { column: 1, font: "13px sans-serif", alignment: go.Spot.Center },
            new go.Binding("text", "key")),
			   $(go.TextBlock,
            { margin: new go.Margin(0, 18), column: 2, font: "13px sans-serif", alignment: go.Spot.Center },
            new go.Binding("text", "tbname"))
        );
		
      // This template represents a whole "record".
      myDiagram.nodeTemplate =
        $(go.Node, "Auto",
          { copyable: false, deletable: false },
          new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
          // this rectangular shape surrounds the content of the node
          $(go.Shape,
            { fill: "#EEEEEE",  stroke: "black" }),
          // the content consists of a header and a list of items
		            $(go.Panel, "Vertical",
            // this is the header for the whole node
			            $(go.Panel, "Auto",
              { stretch: go.GraphObject.Horizontal },  // as wide as the whole node
              $(go.Shape,
                { fill: "#C0C0C0",  stroke: "black",}),
              $(go.TextBlock,
                {
                  alignment: go.Spot.Center,
                  margin: 3,
                  stroke: "black",
                  textAlign: "center",
                  font: "bold 14pt sans-serif"
                },
                new go.Binding("text", "key"))),
				
            // this Panel holds a Panel for each item object in the itemArray;
            // each item Panel is defined by the itemTemplate to be a TableRow in this Table
            $(go.Panel, "Table",
              {
				defaultColumnSeparatorStroke: "gray",
                defaultRowSeparatorStroke: "gray",
                minSize: new go.Size(210, 10),
                defaultStretch: go.GraphObject.Horizontal,
                itemTemplate: fieldTemplate
              },
              new go.Binding("itemArray", "fields"),
			   $(go.Panel, "TableRow",
          { isPanelMain: true,background:"	#1E90FF", },  // needed to keep this element when itemArray gets an Array
          $(go.TextBlock, "PK",
            { column: 1, margin: new go.Margin(2, 2, 0, 2), font: "bold 10pt sans-serif", alignment: go.Spot.Center }),
          $(go.TextBlock, "FK",
            { column: 2, margin: new go.Margin(2, 2, 0, 2), font: "bold 10pt sans-serif", alignment: go.Spot.Center }),
          $(go.TextBlock, "Имя столбца",
            { column: 3, margin: new go.Margin(2, 2, 0, 2), font: "bold 10pt sans-serif", alignment: go.Spot.Center })
        ) 
            ),
			          
 $(go.Panel, "Table",
              {
             defaultStretch: go.GraphObject.Horizontal,
                minSize: new go.Size(210,10),
                itemTemplate: methodTemplate,
				background:"orange",
				   defaultColumnSeparatorStroke: "gray",
                defaultRowSeparatorStroke: "gray",
              },
			   new go.Binding("itemArray", "method"),
            )  // end Table Panel of items			// end Table Panel of items
			// end Table Panel of items			// end Table Panel of items
          ),  // end Vertical Panel

        );  // end Node

       function convertIsTreeLink(r) {
        return r === "generalization";
      }

      function convertFromArrow(r) {
        switch (r) {
          case "generalization": return "";
          default: return "";
        }
      }

      function convertToArrow(r) {
        switch (r) {
          case "generalization": return "Triangle";
          case "aggregation": return "StretchedDiamond";
          default: return "";
        }
      }

      myDiagram.linkTemplate =
        $(go.Link,
          { routing: go.Link.Orthogonal },
          new go.Binding("isLayoutPositioned", "relationship", convertIsTreeLink),
          $(go.Shape),
          $(go.Shape, { scale: 1.3, fill: "white" },
            new go.Binding("fromArrow", "relationship", convertFromArrow)),
          $(go.Shape, { scale: 1.3, fill: "white" },
            new go.Binding("toArrow", "relationship", convertToArrow))
        );

  var nodedata = <?php echo json_encode($data);?>;
    var linkData = <?php echo json_encode($list);?>;
      myDiagram.model =
        $(go.GraphLinksModel,
          {
            copiesArrays: true,
            copiesArrayObjects: true,
            linkFromPortIdProperty: "fromPort",
            linkToPortIdProperty: "toPort",
            nodeDataArray:  nodedata,
			linkDataArray: linkData
          });

    
    }
</script>
</head>
<body onload="init()">
    <header>
		<div class="logo">
			<a href="index.php">Database Diagram</a>
		</div><!-- end logo -->

		<nav>
			<ul>
				<li><a href="Bachman.php" class="selected">Диаграмма Бахмана</a></li>
				<li><a href="table.php">Таблица</a></li>
				<li><a href="eer.php">ER-диаграмма</a></li>
			<li><a href="dbgl.php">DBGL-диаграмма</a></li>
			</ul>
		</nav><!-- end navigation menu -->


	</header>
<div id="sample" class="main">
  <div id="myDiagramDiv" clas style="border: solid 1px black; width:100%; height:1000px"></div>
    
</div>
</body>
</html>
<Style>
    header {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    min-height: 100%;
    padding: 0 0 0 50px;
    background: #fff;
    float: left;
    overflow: hidden;
    z-index: 9999;
}
header .logo {
    margin-top: 100px;
}
a {
    margin: 0;
    padding: 0;
    font-size: 100%;
    vertical-align: baseline;
    background: 0 0;
    color: #454545;
}
header nav ul {
    display: block;
    overflow: hidden;
    margin-top: 100px;
    list-style: none;
}
header nav ul li {
    display: block;
    margin-bottom: 30px;
}
header nav ul li a {
    color: #454545;
    font-family: raleway-regular,arial;
    font-size: 14px;
    text-decoration: none;
    letter-spacing: 1px;
}

.main {
    width: 100%;
    height: 100%;
    padding-left: 300px;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    position: relative;
    z-index: 55;
    background: #f6f6f6;
    clear: both;
}
</Style>
