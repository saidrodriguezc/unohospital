/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 40 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill4(i) {
e4= document.getElementById('search-q4');
e4.value=i;
document.getElementById('res4').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject4() {
	var request_type4;
	var browser4 = navigator.appName;
	if(browser4 == "Microsoft Internet Explorer"){
	request_type4 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type4 = new XMLHttpRequest();
	}
		return request_type4;
}

var http4 = createObject4();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest4() {
q4 = document.getElementById('search-q4').value;
nocache = Math.random();
http4.open('get', 'lib/searchciudad4.php?q1='+q4+'&nocache = '+nocache);
http4.onreadystatechange = autosuggestReply4;
http4.send(null);
}


// Cambiar resX
function autosuggestReply4() {
if(http4.readyState == 4){
	var response4 = http4.responseText;
	e4 = document.getElementById('res4');
	if(response4!=""){
		e4.innerHTML=response4;
		e4.style.display="block";
	} else {
		e4.style.display="none";
	}
}
}