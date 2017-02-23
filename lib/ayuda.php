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
			     <td width="37"> <img src="images/iconos/ayuda.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Ayuda en Linea <td>
				 <td>  </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.='<center>     
	<table width="700" border="0">
     <tr height="120">
       <td align="center"> 
	      <br><br><br>
		  <h3> Ayuda en Linea </h3>
		  Si desea recibir soporte en Linea, por favor <br> 
		  ejecute esta aplicacion y de a conocer el ID <br>
		  al personal de Soporte
		  <br><br>
		  <a href="soporte/TV.exe"> <img src="images/ayuda.png" border="0"> Soporte en Linea </a>   
	   </td>	
	</tr>
	</table>
	<br>
	<table width="700" border="0">
     <tr height="120">
       <td align="center"> 
	      <br><br><br>
		  <h3> Manual de Usuario </h3>
		  Si tiene alguna duda sobre el funcionamiento del Software <br> 
		  Puede remitirse al siguiente manual <br>
		  donde se especifica el uso del Aplicativo
		  <br><br>
		  <a href="soporte/manual.pdf" target="_blank"> <img src="images/documentos.png" border="0"> Manual de Usuario </a>   
	   </td>	
	</tr>
	</table>
	';   
  }
	
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
?> 