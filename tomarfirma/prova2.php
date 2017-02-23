<?php 
session_start();
$filename = $_SESSION['USERNAME'].'.png';
$somecontent = base64_decode($_REQUEST['png']);

    if ($handle = fopen("images/".$filename, 'w+'))
    if (!fwrite($handle, $somecontent) ===FALSE) 	
    	fclose($handle);
    	
    echo "imageurl=".$filename;
?>