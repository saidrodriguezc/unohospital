<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("reportes/configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: auditoria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT LA.* , E.icono , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 	
	         WHERE LA.descripcion like '%".$criterio."%' OR E.descripcion like '%".$criterio."%' 
			 OR LA.sentencia like '%".$criterio."%' ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

    $filtro = $criterio;    	  
	$_SESSION['FILTRO_AUDITORIA'] = $filtro;
	$clase->Aviso(2,"<b>Criterio de Busqueda Abierto : </b> ".$filtro);     

    $_SESSION['SQL_AUDITORIA'] = $vsql;
	header("Location: auditoria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT LA.* , E.icono , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 
		     ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	
	$_SESSION['FILTRO_AUDITORIA'] = "";
	$_SESSION['SQL_AUDITORIA'] = "";
	header("Location: auditoria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "busquedasimple")
  {
     $fd = $_POST['fecdesde'];
     $fh = $_POST['fechasta'];
     $usuario  = strtoupper($_POST['usuario']);	 	 
	 
	 $fecdesde = substr($fd,6,4)."-".substr($fd,3,2)."-".substr($fd,0,2)." 00:00:00";
	 $fechasta = substr($fh,6,4)."-".substr($fh,3,2)."-".substr($fh,0,2)." 23:59:59";
	 	 
     $vsql = "SELECT LA.* , E.icono , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 
		      WHERE LA.momento >= '".$fecdesde."' AND LA.momento <= '".$fechasta."' ";
	 
	 if($usuario != "")
	    $vsql.= "AND UCASE(LA.usuario) = '".$usuario."' "; 
     else
	   $usuario = "Todos los Usuarios";		  		  
     
	 $vsql.= "ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];    	 
	 
	 $filtro = "Desde : ".$fd." - Hasta : ".$fh." - Usuario : ".$usuario;    	  
	 $_SESSION['FILTRO_AUDITORIA'] = $filtro;
	 $clase->Aviso(2,"<b>Informe : </b> ".$filtro);     
	  
	 $_SESSION['SQL_AUDITORIA'] = $vsql;      
	 header("Location: auditoria.php");
  }

  ////////////////////////////////////////////////
  if($opcion == "enexcel")
  {
	$vsql = $_SESSION['SQL_AUDITORIA']; 
    $filtro = $_SESSION['FILTRO_AUDITORIA'];
	
	if($vsql == "")
    {
	  $vsql = "SELECT LA.* , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 
		       ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	  $filtro = "Fechas Inicio y Fin : Hoy - Usuario : Todos los Usuarios";		   
    }
	
	$cont = '<h2>INFORME LOG AUDITORIA DEL SISTEMA</h2><h3>'.$filtro.'</h3><br>';
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

 	$cont.='<table border="1">
	          <tr>
			     <td bgcolor="gray"> <font color="white"> <b> Evento </td>
				 <td bgcolor="gray"> <font color="white"> <b> Fecha - Hora </td>
				 <td bgcolor="gray"> <font color="white"> <b> Descripcion Evento </td>
				 <td bgcolor="gray"> <font color="white"> <b> Usuario </td>
				 <td bgcolor="gray"> <font color="white"> <b> Terminal </td>
				 <td bgcolor="gray"> <font color="white"> <b> Direccion IP </td>
			  </tr>';

	while($row = mysql_fetch_array($result))  	
	{
       $cont.='<tr>
			     <td>'.$row['activi'].'</td>
			     <td>'.$row['momento'].'</td>				 
			     <td>'.$row['descripcion'].'</td>
			     <td>'.$row['usuario'].'</td>
			     <td>'.$row['equipo'].'</td>
			     <td>'.$row['direccionip'].'</td>
			   </tr>';	 
	} // Fin de While 
	
	$cont.='</table>';
	
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=informeAuditoria.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
    echo $cont;
	exit();
  }

  ////////////////////////////////////////////////
  if($opcion == "enpdf")
  {
    
	require('lib/fpdf/fpdf.php');
	$vsql = $_SESSION['SQL_AUDITORIA']; 
    $filtro = $_SESSION['FILTRO_AUDITORIA'];
	if($vsql == "")
    {
	  $vsql = "SELECT LA.* , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 
		       ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	  $filtro = "Fechas Inicio y Fin : Hoy - Usuario : Todos los Usuarios";		   
    }
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	$pdf=new FPDF();
	$pdf->AddPage();
     
	// Encabezado del Reporte 
    $pdf->SetTextColor(200,200,200);                                           $pdf->SetFont('Arial','B',14);
	$cont= '1Uno.co';                                                          $pdf->Text(186,9,$cont);       
	$pdf->SetFont('Arial','',8);      $cont= 'Sistema Administrativo Web';     $pdf->Text(171,12,$cont);

	// Inicia el Reporte		
	$y=16;
    $pdf->SetTextColor(1,1,1);                                                  $pdf->SetFont('Arial','B',10);	
	$cont = 'INFORME LOG AUDITORIA DEL SISTEMA';                                $pdf->Text(5,$y,$cont);                      $y+=4;
    $pdf->SetFont('Arial','',9);
	$cont = $filtro;                                                            $pdf->Text(5,$y,$cont);                      $y+=10;
	
	// Titulos de Campos del Reporte
	$cont= "EVENTO                                             FECHA - HORA        DESCRIPCION DEL EVENTO                                 
	        USUARIO  TERMNAL        DIRECC IP";
    $pdf->SetFont('Arial','B',8);                $pdf->Text(5,$y,$cont);               $y+=5;             
	$pdf->Line(5,31,206,31);                    $pdf->Line(5,27,206,27);
		
	
	while($row = mysql_fetch_array($result))  	
	{
     $pdf->SetFont('Arial','',7);
     $contd = $row['activi'];                                 $pdf->Text(5,$y,$contd);                     
     $contd = $row['momento'];                                $pdf->Text(52,$y,$contd);                      
     $contd = substr($row['descripcion'],0,48);               $pdf->Text(79,$y,$contd);                      
     $contd = $row['usuario'];                                $pdf->Text(154,$y,$contd);                      	 
     $contd = $row['equipo'];                                 $pdf->Text(166,$y,$contd);                      	 	 
     $contd = $row['direccionip'];                            $pdf->Text(187,$y,$contd);                      	 	 	 
	 $y+=4;
	} // Fin de While   
	
	$pdf->SetFont('Arial','B',14);
    $cont= '1Uno.Co  Sistema Administrativo Web                            Informes del Sistema                      Impreso el '.date("d/m/Y").' '.date("h:i a");
    $pdf->SetFont('Arial','',8);                        $pdf->Text(28,275,$cont);

	$pdf->Output("informeLogAuditoria.pdf");
	$pdf->Output();
	
  } // Fin de enimpresora



  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
	 //$cont = EncabezadoAyuda();
	 $logid = $_GET['logid'];
     $vsql = "SELECT LA.* , E.icono , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) WHERE LA.logid=".$logid;
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     if($row = mysql_fetch_array($result));
	 
	 $cont.='
        <table width="800"><tr valign="middle" height="35"> <td class="TituloTablaProductosSel" align="center"> 
		                   <b> Detalles del Suceso ID No. '.$logid.' </b> </td> </tr> </table>
		<table width="800">
		 <tr valign="middle" height="70" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="20" align="left"> <img src="images/log/'.$row['icono'].'" border="0"> </td>
	      <td width="300" align="left"> '.$row['activi'].' </td>
		  <td width="500" align="left"> <b>'.$row['descripcion'].' </b></td>	
		  <td width="20"> &nbsp; </td>	  
         </tr>
		</table>
		<table width="800">
		 <tr valign="middle" height="35" bgcolor="gainsboro"> 
          <td width="10"> &nbsp; </td>
	      <td width="20" align="left"> <img src="images/log/pc.png" border="0"> </td>
	      <td width="100" align="left"> '.$row['equipo'].' </td>
	      <td width="20" align="left"> <img src="images/log/ip.png" border="0"> </td>
	      <td width="80" align="left"> '.$row['direccionip'].' </td>
	      <td width="20" align="left"> <img src="images/log/usuario.png" border="0"> </td>
	      <td width="70" align="left"> '.$row['usuario'].' </td>
	      <td width="20" align="left"> <img src="images/log/fecha.png" border="0"> </td>
	      <td width="150" align="left"> '.$clase->FormatoFecha($row['momento']).' </td>
         </tr>
		</table>';
		
		if($row['sentencia'] != "")
		{
    	   $cont.='<table width="800">
		 		    <tr valign="middle" height="50" bgcolor="#EEF1F6"> 
			          <td width="10"> &nbsp; </td>
					  <td width="100" align="center"> '.$row['sentencia'].' </td>
			          <td width="10"> &nbsp; </td>
					</tr></table>';
        }
		
	echo $cont;
	exit();
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
			 <form action="?opcion=encontrar" method="POST" name="x">
			  <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/auditoria.png" width="32" height="32" border="0"> </td>
				 <td width="220"> Auditor de Sucesos <td>
				 <td width="180">  </td>
				 <td width="100"> &nbsp; </td>
				 <td width="27"> <a href="?opcion=nuevatarea"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td>
				 </form>';

	 if($_SESSION['SQL_AUDITORIA'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.='<td width="8"> </td>
			   </tr>	 			   
			 </table> 
			 <form action="?opcion=busquedasimple" method="POST" name="y">
			 <table width="100%">	
	          <tr class="BarraDocumentos"> 
			     <td width="65" align="center"> Desde : </td>
			     <td width="40"> <input type="text" name="fecdesde" class="Texto13" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, y.fecdesde,\'dd/mm/yyyy\');"> </td>
			     <td width="50" align="center"> Hasta : </td>
			     <td width="40"> <input type="text" name="fechasta" class="Texto13" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, y.fechasta,\'dd/mm/yyyy\');"> </td>
			     <td width="60" align="center"> Usuario : </td>				 
			     <td width="50"> <input type="text" name="usuario" class="Texto14" size="12"> </td>
			     <td width="30"> <input type="submit" value="Buscar"> </td>
			     <td width="22" align="center"> 
				   <a href="#" OnClick="window.open(\'?opcion=enpdf\',\'AenPDF\',\'width=800,height=600\');"> 
				      <img src="images/iconpdf.png" border="0" title="Exportar Resultado a PDF"> </a> </td>
				 <td width="22" align="center">   
				   <a href="?opcion=enexcel" target="_blank">
				   <img src="images/iconoexcel.png" border="0" title="Exportar Resultado a Excel"> </a> </td>
			     <td width="120" align="center"> Busqueda Avanz </td> 
				 </td>				 
			  </tr></table>';	
	
    $vsql = $_SESSION['SQL_AUDITORIA'];
	if($vsql == "")
    	$vsql = "SELECT LA.* , E.icono , E.descripcion activi FROM logauditoria LA INNER JOIN eventolog E ON (E.codigo = LA.evento) 
		         ORDER BY LA.momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<div style="overflow:auto; height:580px;width:796px;">
	          <table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="5"> </td>
			     <td width="5" align="center"> Tipo </td>				
			     <td width="110"> Fecha y Hora Evento </td> 
				 <td width="220"> Descripcion Actividad</td>				  
			     <td width="60" align="center"> Usuario </td>
				 <td width="20"> </td>
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td width="5"> </td>
				  <td width="5" align="center"> <img src="images/log/'.$row['icono'].'" title="'.$row['activi'].'" border="0"> </td>
				  <td width="110"> '.$clase->FormatoFecha($row['momento']).' </td>				  
				  <td width="220"> '.substr($row['descripcion'],0,52).' ... </td>
				  <td width="60" align="center"> '.$row['usuario'].' </td>
				  <td> <a href="?opcion=detalles&amp;logid='.$row['logid'].'" rel="facebox"> 
				      <img src="images/iconobuscar.png" title="Detalles de la Actividad" border="0"> </td>				
				 </tr>';
	}
	$cont.='</table>
	        <table width="100%">
	           <tr class="PieTabla"> 
			     <td width="10"> </td>
			     <td width="100"> <a href="?opcion=masregistros"> Mas Registros </a> </td>
			     <td width="100"> </td>
				 <td width="100"> <a href="#arriba"> Arriba </a> </td>
			   </tr>
			</table></div>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();    
 
?> 