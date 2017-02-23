/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 60 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill6(i) {
e6= document.getElementById('search-q6');
e6.value=i;
document.getElementById('res6').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject6() {
	var request_type6;
	var browser6 = navigator.appName;
	if(browser6 == "Microsoft Internet Explorer"){
	request_type6 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type6 = new XMLHttpRequest();
	}
		return request_type6;
}

var http6 = createObject6();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest6() {
q6 = document.getElementById('search-q6').value;
nocache = Math.random();
http6.open('get', 'lib/searchclasifica6.php?q1='+q6+'&nocache = '+nocache);
http6.onreadystatechange = autosuggestReply6;
http6.send(null);
}


// Cambiar resX
function autosuggestReply6() {
if(http6.readyState == 4){
	var response6 = http6.responseText;
	e6 = document.getElementById('res6');
	if(response6!=""){
		e6.innerHTML=response6;
		e6.style.display="block";
	} else {
		e6.style.display="none";
	}
}
}