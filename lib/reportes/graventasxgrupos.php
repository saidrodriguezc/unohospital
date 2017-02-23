<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_pie.php');
require_once ('../lib/jpgraph/jpgraph_pie3d.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];
$opcion = $_GET['opcion'];

$vsql='SELECT GP.descripcion, SUM( DD.valparcial ) Total
	   FROM documentos D
	   INNER JOIN dedocumentos DD ON ( D.docuid = DD.docuid ) 
	   INNER JOIN productos P ON ( P.itemid = DD.itemid ) 
	   INNER JOIN lineasprod GP ON ( P.lineaprodid = GP.lineaprodid ) 
	   WHERE D.tipodoc = "FVE" AND GP.codigo <> "PRE"
	   GROUP BY 1';

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= substr($row[0],0,7);
  $datosy[$i]= $row[1];
  $i++;
}

if($i > 0)
{
 
 if($opcion == "g")
    $graph = new PieGraph(600,300);	
 else	
    $graph = new PieGraph(380,280);	 
	
 $graph->title->SetColor("black");
 $graph->legend->Pos(0.00,0.00); 

 $p1 = new PiePlot3d($datosy); 
 $p1->SetCenter(0.50);
 $p1->SetLegends($datosx);

 $graph->Add($p1);
 $graph->Stroke();
}

?>