<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_pie.php');
require_once ('../lib/jpgraph/jpgraph_pie3d.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$vsql='SELECT E.descripcion estado, COUNT(M.mantenimid) cantidad
	   FROM mantenimientos M
	   INNER JOIN estados E ON (E.codigo = M.estadoact)
	   GROUP BY 1
	   ORDER BY E.orden';

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
 $graph = new PieGraph(400,280);	
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