/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 80 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill8(i) {
e8= document.getElementById('search-q8');
e8.value=i;
document.getElementById('res8').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject8() {
	var request_type8;
	var browser8 = navigator.appName;
	if(browser8 == "Microsoft Internet Explorer"){
	request_type8 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type8 = new XMLHttpRequest();
	}
		return request_type8;
}

var http8 = createObject8();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest8() {
q8 = document.getElementById('search-q8').value;
nocache = Math.random();
http8.open('get', 'lib/searchvendedor8.php?q1='+q8+'&nocache = '+nocache);
http8.onreadystatechange = autosuggestReply8;
http8.send(null);
}


// Cambiar resX
function autosuggestReply8() {
if(http8.readyState == 4){
	var response8 = http8.responseText;
	e8 = document.getElementById('res8');
	if(response8!=""){
		e8.innerHTML=response8;
		e8.style.display="block";
	} else {
		e8.style.display="none";
	}
}
}