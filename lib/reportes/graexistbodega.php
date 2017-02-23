<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_pie.php');
require_once ('../lib/jpgraph/jpgraph_pie3d.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];

$vsql='SELECT B.descripcion bodega, E.existencia
FROM existencias E
LEFT JOIN bodegas B ON ( E.bodegaid = B.bodegaid ) 
WHERE itemid ='.$itemid;

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= $row[0];
  $datosy[$i]= $row[1];
  $i++;
}

if($i > 0)
{
 $graph = new PieGraph(380,250);	
 $graph->title->Set("Existencias en Bodega");
 $graph->title->SetColor("black");
 $graph->legend->Pos(0.02,0.2); 

 // Create pie plot
 $p1 = new PiePlot3d($datosy); 
 //$p1->SetTheme("sand");
 $p1->SetCenter(0.30);
 //$p1->SetAngle(60);
 //$p1->value->SetFont(FF_ARIAL,FS_NORMAL,12);
 $p1->SetLegends($datosx);

 $graph->Add($p1);
 $graph->Stroke();
}

?>