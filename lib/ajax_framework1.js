/* ---------------------------- */
/* XMLHTTPRequest Enable 		*/
/* ---------------------------- */

function fill1(i) {
e1= document.getElementById('search-q1');
e1.value=i;
document.getElementById('res1').style.display="none";
}


function createObject1() {
	var request_type1;
	var browser1 = navigator.appName;
	if(browser1 == "Microsoft Internet Explorer"){
	request_type1 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type1 = new XMLHttpRequest();
	}
		return request_type1;
}

var http1 = createObject1();

/* -------------------------- */
/* SEARCH					 */
/* -------------------------- */
function autosuggest1() {
q1 = document.getElementById('search-q1').value;
// Set te random number to add to URL request
nocache = Math.random();
http1.open('get', 'lib/searchciudad1.php?q1='+q1+'&nocache = '+nocache);
http1.onreadystatechange = autosuggestReply1;
http1.send(null);
}

function autosuggestReply1() {
if(http1.readyState == 4){
	var response1 = http1.responseText;
	e1 = document.getElementById('res1');
	if(response1!=""){
		e1.innerHTML=response1;
		e1.style.display="block";
	} else {
		e1.style.display="none";
	}
}
}