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
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Ventas detalladas en Excel");		 
    
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	         <form action="xlslistaventas.php" method="POST" name="x">
			 <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Analisis de Ventas detalladas en Excel<td>
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
				 <input type="text" name="fechasta" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
			  </table>
			  <table width="600">
	           <tr class="CabezoteTabla"> 
				 <td width="20">  <td>	           
			     <td width="380"> <br>Este informe genera una hoja de MS Excel con el listado detallado de las Ventas. Alli usted podra filtrar y manejar la informacion 
				                  como desee. Sin embargo le sugerimos utilizar <a href="plantilla1.xls"> esta plantilla</a> para un mejor Analisis <br><br> </td>
				 <td width="10">  <td>
			  </tr>
			 </table>
			 <br>
			 <table width="600"> 
              <tr> 
 			     <td align="center"> <input type="submit" value="Generar Datos en Excel"> </td>				 
			  </tr>
			 </table> </form>';
    echo $cont;  
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  


?> 
