<?PHP
  function EncabezadoReporte($titulo)
  {
	 $cont = '<center>
	          <table width="950">
			    <tr height="10" class="TablaDocsTitulosReportes">
				  <td width="30"> &nbsp; </td> 
				  <td width="200" align="left"> <img src="../images/principal.png" border="0"> 
				      <a href="../informes.php"> Menu Principal </a> </td>				  
				  <td width="200" align="left"> <img src="../images/iconos/informes.png" width="16" height="16" border="0"> 
				      <a href="../informes.php"> Menu de Informes </a> </td>				  
				  <td width="150" align="left"> <img src="../images/iconvolver.png" border="0"> 
				      <a href="javascript:history.back(-1);"> Atras </a> </td>				  
				</tr>  
			  </table>
			  <table> 	
				<tr height="70" valign="middle">
				  <td align="center"> 
				      <br>
					  <b> <font size="3"> '.$_SESSION['G_NOMBREEMP'].' </font> </b> 
  			          <br>
					  <font size="1"> '.$titulo.' </font>
				  </td>
			  </table>';	
	 return($cont);
  }

  function EncabezadoReporte2($titulo)
  {
	 $cont = '<center>
	          <table width="950">
			    <tr height="10" class="TablaDocsTitulosReportes">
				  <td width="30"> &nbsp; </td> 
				  <td width="200" align="left"> <img src="../images/principal.png" border="0"> 
				      <a href="../informes.php"> Menu Principal </a> </td>				  				  
				  <td width="200" align="left"> <img src="../images/iconos/informes.png" width="16" height="16" border="0"> 
				      <a href="../informes.php"> Menu de Informes </a> </td>				  
				  <td width="150" align="left"> <img src="../images/iconoimprimir.png" border="0"> 
				      <a href="javascript:window.print();"> Imprimir </a> </td>				  
				  <td width="150" align="left"> <img src="../images/iconexcel.png" border="0"> 
				      <a href="?opcion=excel" target="_blank"> Exportar a Excel </a> </td>				  
				  <td width="150" align="left"> <img src="../images/iconpdf.png" border="0"> 
				      <a href="?opcion=enpdf"> Exportar a PDF </a> </td>				  
				  <td width="150" align="left"> <img src="../images/iconvolver.png" border="0"> 
				      <a href="javascript:history.back(-1);"> Atras </a> </td>				  				      
				</tr>  
			  </table>
			  <table> 	
				<tr height="70" valign="middle">
				  <td align="center"> 
				      <br>
					  <b> <font size="3"> '.$_SESSION['G_NOMBREEMP'].' </font> </b> 
  			          <br>
					  <font size="1"> '.$titulo.' </font>
				  </td>
			  </table>';	
	 return($cont);
  }


?>