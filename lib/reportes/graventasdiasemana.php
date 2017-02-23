<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_bar.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];

$vsql = "SELECT DATE_FORMAT(fechadoc, '%w') dia , SUM( total ) valor
		 FROM documentos
		 WHERE tipodoc = 'FVE' AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00'
		 GROUP BY 1 ASC";

$result = mysql_query($vsql,$conex); 
while($row = mysql_fetch_array($result))
{
      $dia = $row[0];
      if($dia == 0)
        $dia = 6;
      else
	    $dia -= 1;  

      if($dia == 0)
        $diax = "Lunes";
      if($dia == 1)
        $diax = "Martes";
      if($dia == 2)
        $diax = "Miercoles";
      if($dia == 3)
        $diax = "Jueves";
      if($dia == 4)
        $diax = "Viernes";
      if($dia == 5)
        $diax = "Sabado";
      if($dia == 6)
        $diax = "Domingo";
    
  $datosx[$dia]= $diax;
  $datosy[$dia]= $row['valor'];
}

  $graph = new Graph(900,230,'auto');	
  $graph->SetScale("textlin");
  $graph->img->SetMargin(100,20,20,30);

  $graph->xaxis->SetTickLabels($datosx);
  $graph->xaxis->title->SetColor('white');
  $bplot1 = new BarPlot($datosy);
  $bplot1->SetFillColor('orange@0.4');

  $gbarplot = new GroupBarPlot(array($bplot1));
  $gbarplot->SetWidth(0.6);
  $graph->Add($gbarplot);

  $graph->Stroke();

?>