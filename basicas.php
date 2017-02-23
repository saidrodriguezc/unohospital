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
    $cont = $clase->Header("S","W"); 
	
    $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/basicas.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Tablas B&aacute;sicas <td>
				 <td>  </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.='<center>     
	<table width="700" border="0">
	<tr height="120">
      
      <td align="center"> <a href="niveleducativo.php"> 
	                      <img src="images/iconos/niveleducacion.png" border="0"> <br> Niveles Educativos </a> </td>
	  <td align="center"> <a href="tipoexamen.php"> 
	                      <img src="images/iconos/tipoexamen.png" border="0"> <br> Tipos de Examen </a> </td>
      <td align="center"> <a href="conceptomed.php"> 
	                      <img src="images/iconos/conceptomed.png" border="0"> <br> Conceptos Medicos </a> </td>
	  <td align="center"> <a href="entidades.php">
	                      <img src="images/iconos/entidades.png" border="0"> <br> Entidades EPS </a></td>	
	</tr>
	<tr height="120">
      
      <td align="center"> <a href="especialidades.php"> 
	                      <img src="images/iconos/especialidades.png" border="0"> <br> Especialidades</a> </td>
	  <td align="center"> <a href="estadocivil.php">
	                      <img src="images/iconos/estadocivil.png" border="0"> <br> Estados Civiles </a> </td>	
      <td align="center"> <a href="ayudasmedicas.php">
	                      <img src="images/iconos/ayuda.png" border="0"> <br> Ayudas Medicas </a> </td>
	  <td align="center">  </td>	
	</tr>
	<tr height="120">
	  <td align="center"> <a href="bancos.php">
	                      <img src="images/iconos/cajas.png" border="0"> <br> Bancos </a></td>	
      <td align="center"> <a href="localidades.php"> 
	                      <img src="images/iconos/localidades.png" border="0"> <br> Localidades </a> </td>      
  	  <td align="center"> <a href="gruposprod.php"> 
	                      <img src="images/iconos/gruposprod.png" border="0"> <br> Grupos Servicios </a> </td>	                      
      <td align="center"> <a href="gruposper.php"> 
	                      <img src="images/iconos/gruposper.png" border="0"> <br> Grupos de Personas </a> </td>
	                      
	</tr>
	<tr height="120">
	  <td align="center"> <a href="bodegas.php"> 
	                      <img src="images/iconos/bodegas.png" border="0"> <br> Bodegas Almacenes </a> </td>	  
	  <td align="center"> <a href="zonater.php"> 
	                      <img src="images/iconos/zonater.png" border="0"> <br> Zonas de Ubicacion </a> </td>
	  <td align="center"> <a href="promociones.php">
	                      <img src="images/iconos/promociones.png" border="0"> <br> Promociones </a> </td>
	  <td align="center"> <a href="lineas.php"> 
	                      <img src="images/iconos/lineasprod.png" border="0"> <br> Lineas Productos </a> </td>
	</tr>
	<tr height="120">
	  <td align="center"> <a href="sucursales.php"> 
	                      <img src="images/iconos/sucursales.png" border="0"> <br> Sucursales </a> </td>	  
      <td align="center"> <a href="prefijos.php">
	                      <img src="images/iconos/configdocs.png" border="0"> <br> Configuracion Documentos </a></td>
	  <td align="center"> <a href="mediospago.php">
	                      <img src="images/iconos/mediospago.png" border="0"> <br> Medios de Pago </a></td>	
	  <td align="center"> <a href="observaciones.php"> 
	                      <img src="images/iconos/observaciones.png" border="0"> <br> Observaciones </a> </td>
	</tr>	
	</table>';   
  }
	
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
?> 