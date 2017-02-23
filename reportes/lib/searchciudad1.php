  <?php 
    @session_start();
	include("Sistema.php");
	
	$clase = new Sistema();
	
	$db_host  = $clase->ServidorBD;
    $db_name  = $clase->RutaBD;
    $username = $clase->UsuarioBD;
    $password = "sysuno";

    $db_con = $clase->Conectar(); 

	$searchq =	strip_tags($_GET['q1']);
	$searchq = str_replace(" ","%",$searchq);  

    $getRecord_sql	=	'SELECT * FROM ciudades WHERE codigo LIKE "'.$searchq.'%" Or ciudad LIKE "%'.$searchq.'%" limit 0,5';
	$getRecord		=	mysql_query($getRecord_sql);
	if(strlen($searchq)>0){

	echo '<ul>';
	while ($row = mysql_fetch_array($getRecord)) {?>
		<li><a href="#" onClick="fill1('<?php echo $row['codigo']; ?>');return false;"> <?php echo $row['codigo']; ?> <small><?php echo $row['ciudad'].' '.$row['departamento']; ?></small></a></li>
	<?php } 
	echo '</ul>';
	?>
<?php } ?>
