<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_bar.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$vsql = "SELECT DISTINCT EXTRACT( DAY FROM fechadoc ) dia
	     FROM documentos WHERE tipodoc =  'FVE' OR tipodoc = 'FCO'
	 	 AND fecasentado <>  '0000-00-00 00:00:00' ORDER BY 1";

$result = mysql_query($vsql,$conex); 

$i = 0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= $row[0];
  $labelx[$i]= 'Dia '.$row[0];  
  
  $datosy1[$i]= $clase->SeleccionarUno("SELECT SUM(total) FROM documentos WHERE fecasentado <> '0000-00-00 00:00:00' AND tipodoc = 'FVE' AND Extract(DAY FROM fechadoc) =".$row[0]);
  $datosy2[$i]= $clase->SeleccionarUno("SELECT SUM(total) FROM documentos WHERE fecasentado <> '0000-00-00 00:00:00' AND tipodoc = 'FCO' AND Extract(DAY FROM fechadoc) =".$row[0]);  
   
  $i++; 
}

if($i > 0)
{
  $graph = new Graph(750,300,'auto');	
  $graph->SetScale("textlin");
  $graph->img->SetMargin(60,20,20,30);

  $graph->xaxis->SetTickLabels($labelx);
  $graph->xaxis->title->SetColor('white');

  $bplot1 = new BarPlot($datosy1);
  $bplot2 = new BarPlot($datosy2);
  $bplot1->SetFillColor('orange@0.4');
  $bplot2->SetFillColor('orange@0.4');

  $gbarplot = new GroupBarPlot(array($bplot1,$bplot2));
  $gbarplot->SetWidth(0.4);
  $graph->Add($gbarplot);

  $graph->Stroke();
}

?>