<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("../lib/libdocumentos.php");  
  include("configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "excel")
  {
	$ordalf = $_POST['ordalf'];
	
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=archivo.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
    
	$vsql="SELECT I.codigo , I.descripcion , P2.codigo CodMateria, P2.descripcion DesMateria, CP.cantidad
		   FROM item I INNER JOIN productos P ON (I.itemid = P.itemid)
		   INNER JOIN comboproductos CP ON (CP.productoid = P.itemid)
		   LEFT JOIN item P2 ON (CP.materiapid = P2.itemid)
		   ORDER BY 1,3";		   

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
             <th>Codigo</th>
	         <th>Nombre Combo</th>
			 <th>Cod MP</th>
			 <th>Materia Prima</th>			 
			 <th>Cantidad</th>
			 <th>Factor</th>
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['codigo'].' </td>
              <td>'.$row['descripcion'].' </td>
              <td>'.$row['CodMateria'].' </td>			  
              <td>'.$row['DesMateria'].' </td>			  
              <td>'.$row['cantidad'].' </td>			  
              <td>   </td>			  
            </tr>';
	}
	
	echo'</table>';	  
	exit();
  }

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$codproducto = $_POST['codproducto'];
	$codgrupo    = $_POST['codgrupo'];

	$where = "WHERE 1 ";
	 
	if($codproducto != "")
	{
	  $where.= " AND I.codigo LIKE'".$codproducto."%' ";
	  $nomproducto = $clase->BDLockup($codproducto,"item","codigo","descripcion");
	}

	if($codgrupo != "")
	{
	  $where.= " AND GP.codigo LIKE'".$codgrupo."%' ";
	  $nomgrupo = $clase->BDLockup($codgrupo,"gruposprod","codigo","descripcion");
	}
		
	$vsql="SELECT I.codigo , I.descripcion , P2.codigo CodMateria, P2.descripcion DesMateria, CP.cantidad , PP2.unidad
		   FROM item I INNER JOIN productos P ON (I.itemid = P.itemid)
		   INNER JOIN comboproductos CP ON (CP.productoid = P.itemid)
		   INNER JOIN item P2 ON (CP.materiapid = P2.itemid)
		   INNER JOIN productos PP2 ON (PP2.itemid = P2.itemid)
		   INNER JOIN gruposprod GP ON (P.gruposprodid = GP.gruposprodid)";
	$vsql.=  $where;	   
	$vsql.=" ORDER BY 1,3";		   
    
    $codactual="";
    
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Conformacion de Combos de Productos");	
	
	if($nomproducto != "")
      $titulo = "Producto : ".$nomproducto;
  
    if($nomgrupo != "")
      $titulo.= "  Grupo : ".$nomgrupo;
  
	$cont.='<center><b>'.$titulo.'</b></center>
	        <div style="overflow:auto; height:560px;width:910px;">';

    $i=0;
    $codactual="";

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$saldo=0;
	
	while($row = mysql_fetch_array($result))
	{
      
      $ncodigo = $row['codigo'];
      
	  if($i == 0)
	  {
	    $codactual = $ncodigo;	
        $cont.='<br><table width="700"> <tr> 
				   <td width="500"> Producto : <b>'.$row['descripcion'].'</b> </td>
				   <td width="200"> Codigo : <b>'.$row['codigo'].'</b></td>
				</tr></table><br>';
	  }  
	  else
	  {
	     if($ncodigo != $codactual)
      	 {
            $cont.='<br><table width="700"> <tr> 
				   <td width="500"> Producto : <b>'.$row['descripcion'].'</b> </td>
				   <td width="200"> Codigo : <b>'.$row['codigo'].'</b></td>
				</tr></table><br>';
		    $codactual = $ncodigo;
		 }		
	  }  

      $cont.='<table width="700"> <tr> 
     		    <td width="100"> </td>
			    <td width="80"> '.$row['CodMateria'].' </td>
			    <td width="200"> '.$row['DesMateria'].' </td>			   
                <td width="120" align="right"> '.$row['unidad'].' '.number_format($row['cantidad']).'</td>	
     		    <td width="200"> </td>                
			  </tr></table>';

	  $i++;		
	}
	
	$cont.='</div>';	  
	echo $cont;
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Conformacion de Combos de Productos");	

    $cont.='<br><br><table width="700">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Conformacion de Combos <td>
			  </tr>
			 </table>
			 
			 <form action="?opcion=ver" method="POST" name="y" onsubmit="return ValidarForm(this);">
			 
			 <script type="text/javascript">
			 <!--
				function ValidarForm(formulario) {
					return true; //Si ha llegado hasta aquí, es que todo es correcto
				}
			 -->	
			 </script>	
			 
			 <table width="700">
	           <tr class="BarraDocumentos"> 
			     <td width="50"> </td>
			     <td width="90"> Producto : </td>
			     <td width="200"> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="../lib/ajax_framework12.js"></script>
<style type="text/css">
#search-wrap12 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res12{width:150px; border:solid 1px #DEDEDE; display:none;}
#res12 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res12 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res12 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res12 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res12 li a:hover{background:#FFFFFF;}
#res12 ul {padding:4px;}
</style>
<div id="search-wrap12">
<input name="codproducto" id="search-q12" type="text" onkeyup="javascript:autosuggest12();" maxlength="12" size="10" tabindex="5"/>
<div id="res12"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ --> 

</td>				 
				 <td width="50"> </td>
			     <td width="190"> Grupo de Producto :  </td>
			     <td width="230"> '.$clase->CrearCombo("codgrupo","gruposprod","descripcion","codigo","","S").' </td>				 
				 <td width="50"> </td>
			  </tr>
			 </table>
			 <br>
			  <table width="600">
	           <tr> 
			     <td align="center"> <input type="submit" value="Ver Reporte" tabindex="4"> </td>
			   </tr>
			  </table>';
     echo $cont;  
  }

?> 

