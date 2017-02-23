  <?php 
    @session_start();
	include("Sistema.php");
	
	$clase = new Sistema();
	
	$db_host  = $clase->ServidorBD;
    $db_name  = $clase->RutaBD;
    $username = $clase->UsuarioBD;
    $password = "unodb_tyt";

    $db_con = $clase->Conectar(); 

	$searchq =	strip_tags($_GET['q1']);
	$searchq = str_replace(" ","%",$searchq);  
    
	// Cambiar Aqui SQL de Busqueda
    $getRecord_sql	=	'SELECT * FROM terceros WHERE codigo LIKE "'.$searchq.'%" Or nit LIKE "'.$searchq.'%" OR nombre LIKE "%'.$searchq.'%" limit 0,5';
	///////////////////////////////
	$getRecord		=	mysql_query($getRecord_sql);
	if(strlen($searchq)>0){

	echo '<ul> <li> <a href="terceros.php?opcion=nuevo" target="_blank"> <img src="images/icononuevo.png" border="0"> Crear Nuevo Vendedor </a> </li>';
	while ($row = mysql_fetch_array($getRecord)) {?>
  	    <!-- Cambiar Aqui campo a retornar de la consulta -->
		<li><a href="#" onClick="fill8('<?php echo $row['codigo']; ?>');return false;"> 
		<!-- Cambiar Aqui icono de la consulta -->
		<img src="images/iconos/terceros.png" border="0" width="22" height="22"> 
		<!-- Cambiar Aqui Label de la consulta -->
		<?php echo $row['codigo']; ?> <small> <?php echo substr($row['nombre'],0,25); ?></small></a></li>
	<?php } 
	echo '</ul>';
	?>
<?php } ?>