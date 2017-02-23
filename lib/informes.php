<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
  
  /////////////////////////////////////////  
  if($opcion == "")
  {
    $cont = Cabezote($clase,$opcion,"");	

	$cont.='<center>	
	<table width="700" border="0">
	 <tr height="120">
      <td width="25%" align="center"> <a href="reportes/repventasexcel.php"> 
       <img src="images/iconos/infcxp.png" border="0"><br>Ventas Detalladas en Excel</a></td>
      <td width="25%" align="center"> <a href="reportes/repventas.php"> 
       <img src="images/iconos/infcxp.png" border="0"><br>Ventas Diarias</a></td>
      <td width="25%" align="center"> <a href="reportes/repventaxproductos.php">  
	    <img src="images/iconos/informe2.png" border="0"><br>Top Servicios Prestados</a></td>
      <td width="25%" align="center"> <a href="reportes/represumenprestador.php">  
	    <img src="images/iconos/infcomisiones.png" border="0"><br>Resumen Prestadores</a></td>
	 </tr>
	 <tr height="120">
	  <td width="25%" align="center"> <a href="reportes/repventaxproductos.php">  
	  <img src="images/iconos/infventasgrupo.png" border="0"> <br> Prest Grupos de Servicio </a> </td>	
      <td width="20%" align="center"> <a href="reportes/repventaxhoras.php">  
	    <img src="images/iconos/informes.png" border="0"><br> Prestaciones x Horas</a> </td>
	  <td width="20%" align="center"> <a href="reportes/repventaxdiasemana.php"> 	    
	    <img src="images/iconos/informes.png" border="0"><br> Prestaciones x Dias de la Semana</a> </td>
	  <td width="20%" align="center"> <a href="reportes/repserviciosxcont.php"> 	    
	    <img src="images/iconos/informe1.png" border="0"><br> Servicios Prestados x Contrato</a> </td>
	 </tr>
	 <tr height="120">
	  <td align="center"> <a href="reportes/repatencionesxprestador.php"> 	
	    <img src="images/iconos/infcomisiones.png" border="0"> <br> Atenciones x Prestador </td>	
	  <td align="center"> <a href="reportes/repestausuarios.php"> <img src="images/iconos/infcomisiones.png" border="0"> <br>  Trabajo x Usuarios </a> </td>	
	  <td align="center"> <a href="reportes/listaatencion.php"> <img src="images/iconos/informe2.png" border="0"> <br>  Orden de Pacientes <br> del Prestador </a> </td>	
	  <td align="center"> <a href="?opcion=clientes"> <img src="images/iconos/infcomisiones.png" border="0"> <br>  Clientes </a> </td>	
	 </tr>
	 <tr height="120">
	  <td align="center"> <a href="reportes/repcomfa.php" target="_blank"> 	
	    <img src="images/iconos/informes.png" border="0"> <br> Condiciones de Salud<br>entre fechas </a> </td>	
	  <td align="center"> &nbsp; </td>	
	  <td align="center"> &nbsp; </td>	
	  <td align="center"> &nbsp; </td>	
	 </tr>
	</table>';   
  }
	
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
  /////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////
  function Cabezote($clase,$opcion,$subtitulo)
  {
    $cont = $clase->Header("S","W");
    $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Informes <td>
				 <td>  </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>';

	if($opcion != "")
	{		
   	   $cont.='<table width="100%">	
	            <tr class="BarraDocumentos">
	              <td width="2%" align="left"> </td>				  
	              <td width="68%" align="left"> <b> '.$subtitulo.' </b> </td>				  
	              <td width="30%" align="center"> <a href="informes.php"> Listado de Informes </a> </td>
				</tr>
			   </table>';
   	}		 
    return($cont);
  }  
  
?> 