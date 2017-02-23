<?PHP
  session_start(); 
  include("lib/Sistema.php");
  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $opcion = "";
  $opcion = $_GET["opcion"];
  
  $id = $_GET["id"];
  $_SESSION['docuid'] = $id;  

  /////////////////////////////////////////  
  if($opcion == "detallesprod")
  {
	 $cont = EncabezadoAyuda();
	 $itemid = $_GET['itemid'];
	 $vsql = 'SELECT I.* , P.* , GP.descripcion grupoprod ,LP.descripcion linea 
	          FROM item I INNER JOIN productos P ON (I.itemid = P.itemid) 
              INNER JOIN gruposprod GP ON (P.gruposprodid = GP.gruposprodid)
              INNER JOIN lineasprod LP ON (P.lineaprodid = LP.lineaprodid) WHERE I.itemid ='.$itemid;
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     if($row = mysql_fetch_array($result));
	 
	 $codbarra = $row['codigobarra'];
	 if($codbarra == "") 
	    $codbarra = "SIN COD. BARRAS";
	 		 
	 $cont.='
        <table width="400">
	     <tr valign="middle" height="35"> 
	      <td width="400" class="TituloTablaProductosSel" align="center"> 
		      <a href="javascript:history.back(-1);"><img src="images/iconvolver.png" border="0"></a>
			  <b> '.$row['descripcion'].' </b> </td>
	     </tr>
		</table>
		<table width="400">
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="120" align="left"> 
		    <img src="images/codigobarras.png" border="0"> '.$codbarra.'
		  </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/productos.png" border="0" width="20" height="20"> '.$row['unidad'].' / '.$row['unimayor'].'
		  </td>
	     </tr>		 
	     <tr valign="middle" height="35" bgcolor="white"> 
          <td width="10"> &nbsp; </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/gruposprod.png" border="0" width="20" height="20"> '.$row['grupoprod'].'
		  </td>		  
          <td width="120" align="left"> 
		    <img src="images/iconos/lineasprod.png" border="0" width="20" height="20"> '.$row['linea'].'
		  </td>
		 </tr>
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/productos.png" border="0" width="20" height="20"> <b> Exist Total : '.$row['existenciatotal'].' Unid </b> 
		  </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/productos.png" border="0" width="20" height="20"> <b> Costo Promed : '.$row['existenciatotal'].' </b> 
		  </td>
	     </tr>		 
	     <tr valign="middle" height="35" bgcolor="white"> 
          <td width="10"> &nbsp; </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/productos.png" border="0" width="20" height="20"> Exist M&iacute;nima : <b>'.$row['eximin'].' Unid </b> 
		  </td>
	      <td width="120" align="left"> 
		    <img src="images/iconos/productos.png" border="0" width="20" height="20"> Exist M&aacute;xima : <b>'.$row['eximax'].' Unid </b> 
		  </td>
		 </tr>
		</table>
		<table width="400">
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
            <td width="10"> &nbsp; </td>
 	        <td width="120" align="center"> <b> LISTA DE PRECIOS </b> </td>
 	        <td width="120" align="center"> <b>(1)</b> '.FormatoNumero($row['precio1']).' </td>
 	        <td width="120" align="center"> <b>(2)</b> '.FormatoNumero($row['precio2']).' </td>
	      </tr>		 
	      <tr valign="middle" height="35" bgcolor="white"> 
            <td width="10"> &nbsp; </td>
 	        <td width="120" align="center"> <b>(3)</b> '.FormatoNumero($row['precio3']).' </td>
 	        <td width="120" align="center"> <b>(4)</b> '.FormatoNumero($row['precio4']).' </td>
 	        <td width="120" align="center"> <b>(5)</b> '.FormatoNumero($row['precio5']).' </td>
	      </tr>
	   </table>
	   <table width="400">	   
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="120" align="center"> 
		    <img src="images/iconos/terceros.png" border="0" width="20" height="20"> 
			<a href="#" OnClick="window.open(\'reportes/proveedoresprod.php\',\'RepProvProd\',\'width=600,height=300\');"> Proveedores </a>
		  </td>
	      <td width="120" align="center"> 
		    <img src="images/iconos/informes.png" border="0" width="20" height="20">
            <a href="#" OnClick="window.open(\'reportes/proveedoresprod.php\',\'RepProvProd\',\'width=600,height=300\');"> Estadisticas </a>
		  </td>
	     </tr>		 
	   </table>
	   <BR>
	   <center>';
	   if($row['existenciatotal'] > 0)
	     $cont.='<img src="http://localhost/uno/sistema/reportes/graexistbodega.php?itemid='.$itemid.'" border="0">';

  }

  /////////////////////////////////////////  
  if($opcion == "agregardet")
  {
	 $id     = $_GET['id'];
	 $itemid = $_GET['itemid'];
	 $vsql   = "SELECT * FROM productos P INNER JOIN item I ON (I.itemid = P.itemid) WHERE I.itemid =".$itemid;
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $row    = mysql_fetch_array($result);
     $tipoproducto = $row['tipoproducto']; 
      
  	 $cont = EncabezadoAyuda();
	 $cont.='
	    <script language="javascript">
        <!--	  
		  function Unomas()
		  {
		    var actual = parseInt(window.document.Form1.cantidad.value);
			window.document.Form1.cantidad.value = (actual + 1);
		  }

		  function Unomenos()
		  {
		    var actual = parseInt(window.document.Form1.cantidad.value);
			if(actual > 1)
   			   window.document.Form1.cantidad.value = (actual - 1);
		  }
		  
		  function CopiarPrecio()
          {
            window.document.Form1.precio.value = window.document.Form1.preciosug.value;
          }
		-->  
		</script>
	    
		<form action="?opcion=guardardet" method="POST" name="Form1">
		<input type="hidden" name="docuid" value="'.$id.'"> 
		<input type="hidden" name="itemid" value="'.$itemid.'"> 
		<input type="hidden" name="porciva" value="'.$row['porciva'].'"> 
        <table width="400">
	     <tr valign="middle" height="35"> 
	      <td width="400" class="TituloTablaProductosSel" align="center"> 
		      <a href="javascript:history.back(-1);"><img src="images/iconvolver.png" border="0"></a>
			  <b> '.$row['descripcion'].' </b> </td>
	     </tr>
		</table>
		 <table width="400"> 
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="70" align="left">Cantidad :</td>
		  <td width="80" align="left"> 
		    <input type="text" name="cantidad" size="8" value="1">
		  </td>
		  <td align="left">
		    <input type="button" name="Mas" value=" + " OnClick="Unomas();"> 
		    <input type="button" name="Menos" value=" - " OnClick="Unomenos();"> 
		  </td>  
		  <td align="left">
		    <input type="radio" name="unidad" checked> '.$row['unidad'].'
		    <input type="radio" name="unidad"> '.$row['unimayor'].'			
		  </td>  
	     </tr>		 
        </table>
	    <table width="400">
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="70" align="left">Precio :</td>
		  <td width="80" align="left"> 
		    <input type="text" name="precio" size="8" value="'.$row['precio1'].'">
		  </td>
		  <td width="230" align="left"> 
		    <select name="preciosug" OnChange="CopiarPrecio();"> 
 			  <option value="'.$row['precio1'].'">'.FormatoNumero($row['precio1']).'</option>	
 			  <option value="'.$row['precio2'].'">'.FormatoNumero($row['precio2']).'</option>	
 			  <option value="'.$row['precio3'].'">'.FormatoNumero($row['precio3']).'</option>	
 			  <option value="'.$row['precio4'].'">'.FormatoNumero($row['precio4']).'</option>	
 			  <option value="'.$row['precio5'].'">'.FormatoNumero($row['precio5']).'</option>				  			  			  			
		   </select>	  
		  </td>
	     </tr>		
		 </table>
	    <table width="400">
		 <tr valign="middle" height="35" bgcolor="#EEF1F6"> 
          <td width="10"> &nbsp; </td>
	      <td width="70" align="left">Bodega :</td>
		  <td width="180" align="left">'; 
          
 	 $vsql2  = "SELECT * FROM bodegas ORDER BY 2";
     $result2 = mysql_query($vsql2,$conex);     
	 $cont.='<table width="250">';
	 while($row2 = mysql_fetch_array($result2))
	 {
		   if($row2['codigo'] == $_SESSION['U_BODEGAPRED'])
		       $cont.=' <tr  valign="middle" height="20"> <td> <input type="radio" name="bodegaid" value="'.$row2['bodegaid'].'" checked> '.$row2['descripcion'].' </td> </tr> ';    
		   else
		       $cont.=' <tr  valign="middle" height="20"> <td> <input type="radio" name="bodegaid" value="'.$row2['bodegaid'].'"> '.$row2['descripcion'].' </td> </tr> ';    		   	   
	 }      

		$cont.=' </table> </td>  
	     </tr>		
		 </table>';
	

	 // Verifico si el producto es una Promocion que tiene Variables
	 
	 $vsql2 = "SELECT COUNT(*) FROM promociones P INNER JOIN depromociones DP ON (P.promoid = DP.promoid) 
	           WHERE P.itemid = ".$itemid." AND DP.tipo = 'V'";
	 $EsPromoVariable = $clase->SeleccionarUno($vsql2);
	 
	 if($EsPromoVariable > 0)
	 {
	     $vsql='SELECT PRO.descripcion producto , DP.depromoid , I.itemid , I.descripcion posible 
				FROM promociones P
				INNER JOIN item PRO ON (PRO.itemid = P.itemid)
				INNER JOIN depromociones DP ON ( P.promoid = DP.promoid ) 
				INNER JOIN posibledepromo PDP ON (PDP.depromoid = DP.depromoid)
				INNER JOIN item I ON (I.itemid = PDP.itemid)
				WHERE P.itemid ='.$itemid.' AND DP.tipo =  "V" ORDER BY I.descripcion ASC';

	     $conex  = $clase->Conectar();
		 $result2 = mysql_query($vsql,$conex);     
	     $cont.='<table width="100%">';
   	     while($row2 = mysql_fetch_array($result2))
   	     {
		   $cont.='<tr> <td><b>'.$row2['producto'].'</b></td> </tr>';		
		   $cont.='<tr> <td>'.$row2['posible'].'</td> </tr>';		   		
	     }
		 $cont.='</table>';	   		
	 }
	 
/************* PENDIENTE ******************************/
		 
	 $cont.='<table width="400">
		   <tr valign="middle" height="50" bgcolor="#EEF1F6"> 
		     <td align="center"> <center> <input type="submit" value="Adicionar Producto"> </td> </td>
		   </tr>
		 </table>
		 </form>'; 	 

  }

  /////////////////////////////////////////  
  if($opcion == "guardardet")
  {
	 $docuid   = $_POST['docuid'];
	 $itemid   = $_POST['itemid'];
 	 $porciva  = $_POST['porciva'];
 	 $cantidad = $_POST['cantidad'];	 
	 $unidad   = $_POST['unidad'];
	 $precio   = $_POST['precio'];
	 $bodegaid = $_POST['bodegaid'];
	 
	 $vsql = "INSERT INTO dedocumentos(docuid,itemid,bodegaid,cantidad,valunitario,valdescuento,porciva) 
	          VALUES(".$docuid.",".$itemid.",".$bodegaid.",".$cantidad.",".$precio.",0,".$porciva.")";
	 $clase->EjecutarSQL($vsql);    
	 echo'<script type="text/javascript">
           setTimeout(\'top.location.reload()\',0);
          </script>';  
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT * FROM item I INNER JOIN productos P ON (I.itemid = P.itemid) 
	         WHERE I.tipoitemid = 1 And P.tipoproducto <> 'MAP' AND (
	         I.codigo like '%".$criterio."%' OR I.descripcion like '%".$criterio."%' OR P.codigobarra like '%".$criterio."%')
			 ORDER BY descripcion ASC Limit 0,20";
    $_SESSION['SQL_AYUDADOCUMENTOS'] = $vsql;
	header("Location: ayudadocumentos.php?id=".$id);
  }
    
  /////////////////////////////////////////  
  if($opcion == "agregar")
  {
	 $itemid = $_GET['itemid'];
 	 
	 $vsql   = "SELECT precio1 , porciva FROM productos WHERE itemid =".$itemid;
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $row    = mysql_fetch_array($result);
	 
	 $precio1 = $row['precio1'];
	 $porciva = $row['porciva'];
	 	 
	 $vsql = "INSERT INTO dedocumentos(docuid,itemid,bodegaid,cantidad,valunitario,valdescuento,porciva) 
	          VALUES(".$id.",".$itemid.",3,1,".$precio1.",0,".$porciva.")";

	 $clase->EjecutarSQL($vsql);    

	 echo'<script type="text/javascript">
           setTimeout(\'top.location.reload()\',0);
          </script>';
  }
  
  /////////////////////////////////////////  
  if($opcion == "")
  {
     $id = $_GET['id'];
	 $cont = EncabezadoAyuda();
	 $vsql = $_SESSION['SQL_AYUDADOCUMENTOS'];
     if($vsql == "")
    	$vsql = "SELECT * FROM item I INNER JOIN productos P ON (I.itemid = P.itemid) 
		         WHERE I.tipoitemid = 1 AND P.tipoproducto <> 'MAP' AND I.codigo <> '00' ORDER BY I.codigo ASC limit 0,50";

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='
	    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="css/estilo.css" type="text/css">
		<title>1Uno.co</title>
		<body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" OnLoad="document.x.default.focus();"> 
		<form action="?opcion=encontrar&amp;id='.$id.'" method="POST" name="x">
        <table width="400">
	     <tr> 
	      <td width="200" class="TituloTablaProductosSel" align="center">
		     <a href="productos.php?opcion=nuevo" class="Link13" target="_blank"> <img src="images/icononuevo.png" border="0"> </a> Productos  
 		  </td>
	      <td width="200" class="TituloTabla" align="center"> 
			 <input type="text" size="26" name="criterio" id="default" tabindex="0"> </form> </td>
	     </tr>
		</table>
		<table width="400">';

	 $i = 0;
     while($row = mysql_fetch_array($result)) 
	 {
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
       $cont.='  <td width="4" align="center"> 
	                 <a href="?opcion=agregardet&id='.$id.'&itemid='.$row['itemid'].'">
	                 <img src="images/iconos/productos.png" width="24" height="24" border="0"> 
					 </a>
				 </td>				  
				 <td width="45" align="left">  '.$row['descripcion'].' </a> </td>
				 <td width="10" align="right">
	                 <a href="?opcion=detallesprod&itemid='.$row['itemid'].'">
	                   <img src="images/funciones.png" border="0"> 
					 </a>
				 </td>				 
				 <td width="10" align="right"> '.FormatoNumero($row['precio1']).'</td>                 
				 <td width="7" align="center"> '.ExistenciaGrafica($row['existenciatotal'],$row['eximin'],$row['eximax']).' </td>				 
				 <td width="7" align="center"> <b>'.$row['existenciatotal'].' </b> </td>
				 <td width="1"> </td>				 
			    </tr>';	      		
	}	
	
	$cont.='</table>   </body>   </html>';
		
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont;  
  

  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////    
  function EncabezadoAyuda()
  {
     $cont.='<html>
	     <head>
           <link rel="stylesheet" href="css/estilo.css" type="text/css">
		 </head>
		 <body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" style="overflow : hidden; overflow : -moz-scrollbars-vertical;">';
     return($cont);
  }	 		 
  
  //////////////////////////////////////////////////////////////
  function FormatoNumero($Numero)
  {
    $cont = number_format($Numero);
    return($cont);
  }

  //////////////////////////////////////////////////////////////
  function ExistenciaGrafica($Existencia,$Minima,$Maxima)
  {    
    if($Maxima == 0)
      $imagen = "existenciax.png";
	else
	{
	  $porcen = ($Existencia * 100) / $Maxima;	
	
		if($Existencia < $Minima)
	       $imagen = "existencia0.png";
		else
		{
		   if($Existencia >= $Maxima)
   	       $imagen = "existenciamaxima.png";
		   else
		   {
		      if(($porcen >= 0)&&($porcen <= 25))
			    $imagen = "existencia25.png";
			  else
			  {
			    if(($porcen > 25)&&($porcen <= 50))
			      $imagen = "existencia50.png";
			    else
       	     {
    			   if(($porcen > 25)&&($porcen <= 50))
			   	      $imagen = "existencia50.png";
			   	    else
	           	    {
	           	      if(($porcen > 50)&&($porcen <= 75))
	   		            $imagen = "existencia75.png";
				     else
					    $imagen = "existencia90.png";
				   }
				} 				
			  }		   
		   } 
		}
	}   
	$cont = '<img src="images/'.$imagen.'" border="0">';
    return($cont);
  }  
  
?>
</body>
</html>