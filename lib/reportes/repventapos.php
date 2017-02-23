<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

 
  ////////////////////////////////////////////////

  ////////////////////////////////////////////////
  if($opcion == "enpdf")
  {
    
	require('../lib/fpdf/fpdf.php');

	$desde   = $_GET['desde'];
	$hasta   = $_GET['hasta'];
	$usuario = $_GET['usuario'];
	
	$vsql = "SELECT COUNT(*) cant , SUM(total) total FROM documentos
			 WHERE (tipodoc = 'FVE' OR tipodoc = 'RSA') AND (fechadoc >= '".$desde." 00:00:00' AND fechadoc <= '".$hasta." 23:29:00') 
			 AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00' ";

	if($usuario != "")		 
  	   $vsql.= "AND creador='".$usuario."' ";
  	   
	$vsql.= "ORDER BY fechadoc , tipodoc , prefijo , numero ASC";

    if($usuario == "")
      $usuario = " TODOS ";
      
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))  	
	{

     $pdf=new FPDF();
	 $pdf->AddPage();
     
     $x=5;
     $pdf->SetFont('Arial','B',16);
	 $cont= 'INFORME DE VENTA POS';
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln(). $pdf->Ln(); $pdf->Ln();
	 
	 $pdf->SetFont('Arial','B',14);
     $cont= 'Fecha Inicial  : '.$desde;
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln();
     
     $cont= 'Fecha Final    : '.$hasta;
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln();
     
     $cont= 'Cant. Facturas : '.number_Format($row['cant']).' Documentos';
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln();

     $cont= 'Usuario de Doc : '.strtoupper($usuario);
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();

	 $pdf->SetFont('Arial','B',18);     
     $cont= 'Total Vendido  : '.number_format($row['total']);
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();

	 $pdf->SetFont('Arial','B',14);
     $cont= 'Impreso el '.date("d/m/Y").' '.date("h:i a");
     $pdf->Write($x,$cont);	 $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();

	 $pdf->Output("repventapos.pdf");
	 $pdf->Output();
	
	} // Fin de IF    
	
  } // Fin de enimpresora

  ////////////////////////////////////////////////
  if($opcion == "enimpresora")
  {
	$desde   = $_GET['desde'];
	$hasta   = $_GET['hasta'];
	$usuario = $_GET['usuario'];
	
	$vsql = "SELECT tipodoc , prefijo , numero , total total FROM documentos
			 WHERE (tipodoc = 'FVE' OR tipodoc = 'RSA') AND (fechadoc >= '".$desde." 00:00:00' AND fechadoc <= '".$hasta." 23:29:00') 
			 AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00' ";

	if($usuario != "")		 
  	   $vsql.= "AND creador='".$usuario."' ";
  	   
	$vsql.= "ORDER BY fechadoc , tipodoc , prefijo , numero ASC";
    
    if($usuario == "")
      $usuario = "TODOS ";
      
	
	$cantfacturas = 0; 
	$totalventa = 0;
	$detalle="";
	 
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
        $detalle.= $row['tipodoc'].$row['prefijo'].$row['numero'].str_pad(number_format($row['total']),20," ",STR_PAD_LEFT).Chr(13).Chr(10);               
    	$totalventa = $totalventa + $row['total'];
		$cantfacturas ++;
	}    	
	
	 // Genero la Impresion Modo Texto
	 if($encab1!= "")
       $cont = $encab1.Chr(13).Chr(10);
	 if($encab2!= "")
	   $cont.= $encab2.Chr(13).Chr(10);
     
	 $cont.= Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10);
     $cont.= '----------------------------------'.Chr(13).Chr(10);	 	 
	 $cont.= '       Informe de Venta POS'.Chr(13).Chr(10);
     $cont.= '----------------------------------'.Chr(13).Chr(10).Chr(13).Chr(10);	 
     $cont.= 'Fecha Inicial    : '.$desde.Chr(13).Chr(10);
     $cont.= 'Fecha Final      : '.$hasta.Chr(13).Chr(10);
     $cont.= Chr(13).Chr(10);
     $cont.= 'Cant Facturas    : '.number_Format($cantfacturas).' Documentos'.Chr(13).Chr(10);     
     $cont.= 'Usuario del Doc  : '.strtoupper($usuario).Chr(13).Chr(10);          
     $cont.= '                   --------------'.Chr(13).Chr(10);
     $cont.= 'Total Vendido    : '.str_pad(number_format($totalventa),14," ",STR_PAD_LEFT).Chr(13).Chr(10);               
     $cont.= '                   --------------'.Chr(13).Chr(10);     
     $cont.= $detalle;   
     $cont.= '                   --------------'.Chr(13).Chr(10);	        
     $cont.= 'Sumatoria Total  : '.str_pad(number_format($totalventa),14," ",STR_PAD_LEFT).Chr(13).Chr(10).Chr(13).Chr(10);               
     $cont.= 'Impreso el '.date("d/m/Y").' '.date("h:i a").Chr(13).Chr(10).Chr(13).Chr(10);
     $cont.= Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10);
     $cont.= '.';
     
	 // Genero el Archivo para Enviarlo a Impresora
	 $archivo= "../print/fichero.txt"; // el nombre de tu archivo
     $fch= fopen($archivo, "w"); // Abres el archivo para escribir en él
     fwrite($fch, $cont); // Grabas
     fclose($fch); // Cierras el archivo
	 
	 header("Location: repventapos.php");				
  } // Fin de enimpresora

  ////////////////////////////////////////////////
  if($opcion == "enpantalla")
  {
	$desde   = $_GET['desde'];
	$hasta   = $_GET['hasta'];
	$usuario = $_GET['usuario'];
	
	$vsql = "SELECT tipodoc , prefijo , numero , total total FROM documentos
			 WHERE (tipodoc = 'FVE' OR tipodoc = 'RSA') AND (fechadoc >= '".$desde." 00:00:00' AND fechadoc <= '".$hasta." 23:29:00') 
			 AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00' ";

	if($usuario != "")		 
  	   $vsql.= "AND creador='".$usuario."' ";
  	   
	$vsql.= "ORDER BY fechadoc , tipodoc , prefijo , numero ASC";
  
    if($usuario == "")
      $usuario = "Todos los Usuarios";
    
	$cantfacturas = 0; 
	$totalventa = 0;
	$detalle="";
	 
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
        $detalle.= '<tr><td>'.$row['tipodoc'].$row['prefijo'].$row['numero'].'</td>
		            <td align="right">'.number_format($row['total']).'</td></tr>';
    	$totalventa = $totalventa + $row['total'];
		$cantfacturas ++;
	}    

    	$cont = $clase->HeaderReportes();
    	$cont.= EncabezadoReporte2("Informe de Venta POS");		 
    	$cont.='<br>
		        <h3> Informe de Venta POS</h3>
				<table width="310">
	    	      <tr><td> Fechas Entre <b>'.$desde.'</b> y <b>'.$hasta.'</b></td></tr>
				  <tr><td> Cantidad de Facturas : <b>'.$cantfacturas.' Documentos</b> </td></tr>
				  <tr><td> Ventas del Usuario : <b>'.$usuario.'</b> </td></tr>  
				  <tr><td> <h3> Total Vendido : '.number_format($totalventa).' </h3> </td></tr> 
				  <tr><td> Relacion de Documentos </td></tr>
  				  <tr><td> .</td></tr>';
		$cont.= $detalle;
		$cont.= ' <tr><td> <b>Total</b> </td><td align="right"> <b>'.number_format($totalventa).'</b> </td></tr>
		          <tr><td> Generado el '.date("d/m/Y").' '.date("h:i a").' </td></tr>
 				</table>'; 			
  }

  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$formato = $_POST['formato'];
	
	$fdX = $_POST['fecdesde'];
	$fhX = $_POST['fechasta'];
	$usuario   = $_POST['usuario'];
	
	$desde = substr($fdX,6,4)."-".substr($fdX,3,2)."-".substr($fdX,0,2);
	$hasta = substr($fhX,6,4)."-".substr($fhX,3,2)."-".substr($fhX,0,2);		

    ////////////////////////////////////////
	// Visualizacion del Reporte
	////////////////////////////////////////
    if($formato == 'html')
      header("Location: ?opcion=enpantalla&desde=".$desde."&hasta=".$hasta."&usuario=".$usuario." ");	
    
	if($formato == 'print')
      header("Location: ?opcion=enimpresora&desde=".$desde."&hasta=".$hasta."&usuario=".$usuario." ");	
    
	if($formato == 'pdf')
	{
	  echo"<script type=\"text/javascript\">		    
          	 window.open(\"repventapos.php?opcion=enpdf&desde=".$desde."&hasta=".$hasta."&usuario=".$usuario."\",\"pdf1\",\"width=400,height=500,left=50,top=50\");
			 document.location.href = 'repventapos.php';							       					
           </script>";	
	}
       
  } // Fin de Ver
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Informe de Venta POS");		 
    
	 $cont.='<form action="?opcion=ver" method="POST" name="x">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Informe de Venta POS <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80"> Fecha Desde :  </td>
			     <td width="160"> 
				  <input type="text" name="fecdesde" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">
				 </td>      
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80"> Fecha Hasta :  </td>
			     <td width="160"> 
				  <input type="text" name="fechasta" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">  </td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80"> Usuario :  </td>
			     <td width="160">'.$clase->CrearCombo("usuario","usuarios","nombre","username",$_SESSION['USUARIO'],"S").'</td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80">  </td>
			     <td width="160"> <input type="checkbox" name="detallarventas" value="S" checked> Mostrar Documentos de Venta Uno a Uno</td>			     
				 <td width="50"> <td>
			  </tr>
			  </table>
			  <table width="600">
	           <tr class="BarraDocumentos"> 
				 <td width="60">  <td>	           
			     <td width="170"> <input type="radio" name="formato" value="html" checked> <img src="../images/iconrep.png" width="16" height="16"> Ver en Pantalla </td>
			     <td width="170"> <input type="radio" name="formato" value="print"> <img src="../images/iconoimprimir.png"> Impresion POS </td>
			     <td width="170"> <input type="radio" name="formato" value="pdf"> <img src="../images/iconpdf.png"> Generar en PDF </td>
				 <td width="30">  <td>
			  </tr>
			 </table>
			 <br>
			 <table width="600"> 
              <tr> 
 			     <td align="center"> <input type="submit" value="Ver Reporte"> </td>				 
			  </tr>
			 </table> </form>';
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  echo $cont;  

?> 