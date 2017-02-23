/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 20 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill2(i) {
e2= document.getElementById('search-q2');
e2.value=i;
document.getElementById('res2').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject2() {
	var request_type2;
	var browser2 = navigator.appName;
	if(browser2 == "Microsoft Internet Explorer"){
	request_type2 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type2 = new XMLHttpRequest();
	}
		return request_type2;
}

var http2 = createObject2();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest2() {
q2 = document.getElementById('search-q2').value;
nocache = Math.random();
http2.open('get', 'lib/searchgruposprod2.php?q1='+q2+'&nocache = '+nocache);
http2.onreadystatechange = autosuggestReply2;
http2.send(null);
}


// Cambiar resX
function autosuggestReply2() {
if(http2.readyState == 4){
	var response2 = http2.responseText;
	e2 = document.getElementById('res2');
	if(response2!=""){
		e2.innerHTML=response2;
		e2.style.display="block";
	} else {
		e2.style.display="none";
	}
}
}