<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_line.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();
$opcion = $_GET['opcion'];

$mesActual = date("n");
$anoActual = date("Y");

$vsql = "SELECT DISTINCT EXTRACT( DAY FROM fechadoc ) dia
	     FROM documentos WHERE (tipodoc =  'FVE' OR tipodoc = 'FCO' OR tipodoc = 'RCA')
	 	 AND fechadoc >= SUBDATE( CURDATE(), INTERVAL 20 DAY)
		 AND fecasentado <>  '0000-00-00 00:00:00'
		 ORDER BY 1 ASC LIMIT 0,20";
		 
$result = mysql_query($vsql,$conex); 
$cantdias = mysql_num_rows($result);

$i = 0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= $row[0];
  $labelx[$i]= 'Dia '.$row[0];  

  $val1 = $clase->SeleccionarUno("SELECT SUM(total) FROM documentos WHERE fecasentado <> '0000-00-00 00:00:00' AND tipodoc = 'FVE' AND Extract(DAY FROM fechadoc) =".$row[0]);
  $val2 = $clase->SeleccionarUno("SELECT SUM(total) FROM documentos WHERE fecasentado <> '0000-00-00 00:00:00' AND tipodoc = 'FCO' AND Extract(DAY FROM fechadoc) =".$row[0]);  
  $val3 = $clase->SeleccionarUno("SELECT SUM(total) FROM documentos WHERE fecasentado <> '0000-00-00 00:00:00' AND tipodoc = 'RCA' AND Extract(DAY FROM fechadoc) =".$row[0]);     
  
  if($val1 == "")
    $val1 = 0;

  if($val2 == "")
    $val2 = 0;

  if($val3 == "")
    $val3 = 0;
	
  $datosy1[$i]= $val1;
  $datosy2[$i]= $val2;
  $datosy3[$i]= $val3;

  $i++; 
}

if($i > 0)
{

 if($opcion == "g")
    $graph = new Graph(800,480,'auto');
 else
    $graph = new Graph(600,350,'auto');

 $graph->SetScale("textlin");
 $theme_class = new UniversalTheme;

 $graph->SetTheme($theme_class);
 $graph->img->SetAntiAliasing(false);
 $graph->img->SetMargin(80,20,20,30);
 $graph->SetBox(false);

$graph->img->SetAntiAliasing();

$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xaxis->SetTickLabels($labelx);
$graph->xgrid->SetColor('#E3E3E3');

// Create the first line
$p1 = new LinePlot($datosy1);
$graph->Add($p1);
$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
$p1->mark->SetColor('#006600');
$p1->mark->SetFillColor('#006600');
$p1->SetColor('#006600');
$p1->SetLegend('Ventas');
$p1->SetWeight(5);

// Create the second line
$p2 = new LinePlot($datosy2);
$graph->Add($p2);
$p2->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
$p2->mark->SetColor('#FF0000');
$p2->mark->SetFillColor('#FF0000');
$p2->SetColor("#FF0000");
$p2->SetLegend('Compras');

// Create the third line
$p3 = new LinePlot($datosy3);
$graph->Add($p3);
$p3->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
$p3->mark->SetColor('#0066CC');
$p3->mark->SetFillColor('#0066CC');
$p3->SetColor("#0066CC");
$p3->SetLegend('Cobros');

$graph->legend->SetFrameWeight(0);

// Output line
$graph->Stroke();

}

?>