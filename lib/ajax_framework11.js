/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 70 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill11(i) {
e11= document.getElementById('search-q11');
e11.value=i;
document.getElementById('res11').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject11() {
	var request_type11;
	var browser11 = navigator.appName;
	if(browser11 == "Microsoft Internet Explorer"){
	request_type11 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type11 = new XMLHttpRequest();
	}
		return request_type11;
}

var http11 = createObject11();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest11() {
q11 = document.getElementById('search-q11').value;
nocache = Math.random();
http11.open('get', 'lib/searchdiagnostico11.php?q11='+q11+'&nocache = '+nocache);
http11.onreadystatechange = autosuggestReply11;
http11.send(null);
}


// Cambiar resX
function autosuggestReply11() {
if(http11.readyState == 4){
	var response11 = http11.responseText;
	e11 = document.getElementById('res11');
	if(response11!=""){
		e11.innerHTML=response11;
		e11.style.display="block";
	} else {
		e11.style.display="none";
	}
}
}