  <?php 
    @session_start();
	include("Sistema.php");
	
	$clase = new Sistema();
	
	$db_host  = $clase->ServidorBD;
    $db_name  = $clase->RutaBD;
    $username = $clase->UsuarioBD;

    $db_con = $clase->Conectar(); 

	$searchq =	strip_tags($_GET['q13']);
	$searchq = str_replace(" ","%",$searchq);  
    
	// Cambiar Aqui SQL de Busqueda
    $getRecord_sql	=	'SELECT * FROM cie10 WHERE codcie LIKE "%'.$searchq.'%" Or nomcie LIKE "%'.$searchq.'%" limit 0,5';
	///////////////////////////////
	$getRecord		=	mysql_query($getRecord_sql);
	if(strlen($searchq)>0){

	while ($row = mysql_fetch_array($getRecord)) {?>
  	    <!-- Cambiar Aqui campo a retornar de la consulta -->
		<li><a href="#" onClick="fill13('<?php echo $row['codcie']; ?>');return false;"> 
		<!-- Cambiar Aqui icono de la consulta -->
		<img src="images/iconos/terceros.png" border="0" width="22" height="22"> 
		<!-- Cambiar Aqui Label de la consulta -->
		<?php echo $row['codcie']; ?> <small> <?php echo substr($row['nomcie'],0,25); ?></small></a></li>
	<?php } 
	echo '</ul>';
	?>
<?php } ?>