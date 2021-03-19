// CARD.PHP - НАЧАЛО

function clearCookie() {
	var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
	window.location.reload();
}

function printImage(imagePath) {
    var width = $(window).width() * 0.5;
    var height = $(window).height() * 0.5;
    var content = '<!DOCTYPE html>' + 
                  '<html>' +
                  '<head><title></title></head>' +
                  '<body onload="window.focus(); window.print(); window.close();">' + 
                  '<img src="' + imagePath + '" style="width: 100%;" />' +
                  '</body>' +
                  '</html>';
    var options = "toolbar=no,location=no,directories=no,menubar=no,scrollbars=yes,width=" + width + ",height=" + height;
    var printWindow = window.open('', 'print', options);
    printWindow.document.open();
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.focus();
}

function confirmDelete() {
    if (confirm("Вы действительно хотите выгнать пользователя с терминала?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}
function confirmBlock1() {
    if (confirm("Вы действительно хотите заблокировать доступ пользователя на терминал?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}

function confirmAllBlock() {
    if (confirm("Вы действительно хотите заблокировать доступ всем пользователям магазина на терминал?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}

function confirmAllUnblock() {
    if (confirm("Вы действительно хотите разблокировать доступ всем пользователям магазина на терминал?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}

function confirmBlock0() {
    if (confirm("Вы действительно хотите снять блокировку доступа пользователя на терминал?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}

function confirmAtol() {
    if (confirm("Вы действительно хотите перезагрузить АТОЛ?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}

var array = new Array();
var speed = 10;
var timer = 10;
 
// Loop through all the divs in the slider parent div //
// Calculate seach content divs height and set it to a variable //
function slider(target,showfirst) {
 var slider = document.getElementById(target);
 var divs = slider.getElementsByTagName('div');
 var divslength = divs.length;
	for(i = 0; i < divslength; i++) {
		var div = divs[i];
		var divid = div.id;
		if(divid.indexOf("header") != -1) {
			div.onclick = new Function("processClick(this)");
		} else if(divid.indexOf("content") != -1) {
			var section = divid.replace('-content','');
			array.push(section);
			div.maxh = div.offsetHeight;
			if(showfirst == 1 && i == 1) {
				div.style.display = 'block';
			} else {
				div.style.display = 'none';
			}
		}
	}
}
 
// Process the click - expand the selected content and collapse the others //
function processClick(div) {
	var catlength = array.length;
	for(i = 0; i < catlength; i++) {
		var section = array[i];
		var head = document.getElementById(section + '-header');
		var cont = section + '-content';
		var contdiv = document.getElementById(cont);
		clearInterval(contdiv.timer);
		if(head == div && contdiv.style.display == 'none') {
		contdiv.style.height = '0px';
		contdiv.style.display = 'block';
		initSlide(cont,1);
		} else if(contdiv.style.display == 'block') {
		initSlide(cont,-1);
		}
	}
}
 
// Setup the variables and call the slide function //
function initSlide(id,dir) {
	var cont = document.getElementById(id);
	var maxh = cont.maxh;
	cont.direction = dir;
	cont.timer = setInterval("slide('" + id + "')", timer);
}
 
// Collapse or expand the div by incrementally changing the divs height and opacity //
function slide(id) {
	var cont = document.getElementById(id);
	var maxh = cont.maxh;
	var currheight = cont.offsetHeight;
	var dist;
	if(cont.direction == 1) {
		dist = (Math.round((maxh - currheight) / speed));
	} else {
		dist = (Math.round(currheight / speed));
	}
	if(dist <= 1) {
		dist = 1;
	}
	 cont.style.height = currheight + (dist * cont.direction) + 'px';
	 cont.style.opacity = currheight / cont.maxh;
	 cont.style.filter = 'alpha(opacity=' + (currheight * 100 / cont.maxh) + ')';
	if(currheight < 2 && cont.direction != 1) {
		cont.style.display = 'none';
		clearInterval(cont.timer);
	} else if(currheight > (maxh - 2) && cont.direction == 1) {
		clearInterval(cont.timer);
	}
}


function checkProizVes(ip) {
	$.ajax({
		type: "POST",
		url: "../function/function_object.php",
		data: {
			'ip_ves': ip
		}
	});
}

// CARD.PHP - КОНЕЦ
// ######################################################
// IP_PELICAN.PHP - НАЧАЛО


// IP_PELICAN.PHP - КОНЕЦ
// ######################################################
// EMAIL.PHP - НАЧАЛО

function onChangeMail() {
	var change = document.getElementById('select').value;
	if (change != 'all') { $("#all_shops").css("display","none"); }
	else { $("#all_shops").css("display","inline-block"); }
	$.ajax({			
		type: "POST",			
		url: "selectEmail.php",
		data: {
			'change': change
		},		
		success: function(html){  
			$("#selectEmail").html(html);  
		}  
	});
}

function onChangeMailOffice() {
	var office = 'office';
	$.ajax({			
		type: "POST",			
		url: "selectEmail.php",
		data: {
			'change': office
		},		
		success: function(html){  
			$("#selectEmail").html(html);  
		}  
	});
}

function example_all() {
	$("input:checkbox").prop("checked", true);
}
function example_noone() {
	$("input:checkbox").prop("checked", false);
}

// EMAIL.PHP - КОНЕЦ
// ######################################################
// TERMINALS.PHP - НАЧАЛО

function confirmDelete() {
    if (confirm("Вы действительно хотите выгнать пользователя с терминала?")) {
        return true;
    } else {
        return false;
    }
	dhx.alert({
		title: "Close",
		message: "You can't close this window!",
		callback: someFunction
	})	
}
function confirmDelete_term() {
    if (confirm("Вы действительно хотите выгнать всех пользователей с терминала?")) {
        return true;
    } else {
        return false;
    }
}
function confirmDelete_term_all() {
    if (confirm("Вы действительно хотите выгнать всех пользователей с терминалов Неотрейда?")) {
        return true;
    } else {
        return false;
    }
}
function confirmBlock_term1() {
    if (confirm("Вы действительно хотите заблокировать доступ всем пользователям на этот терминал Неотрейда?")) {
        return true;
    } else {
        return false;
    }
}
function confirmBlock_term0() {
    if (confirm("Вы действительно хотите снять блокировку доступа всех пользователей на этот терминал Неотрейда?")) {
        return true;
    } else {
        return false;
    }
}

function confirmsubmitStopAccessSouth() {
    if (confirm("Вы действительно хотите заблокировать доступ к SOUTH всем пользователям?")) {
        return true;
    } else {
        return false;
    }
}

function confirmsubmitStartAccessSouth() {
    if (confirm("Вы действительно хотите открыть доступ к SOUTH всем пользователям?")) {
        return true;
    } else {
        return false;
    }
}

// ######################################################
// DOMAIN_USER.PHP - НАЧАЛО

function deleteDomainUser() {
    if (confirm("Вы действительно хотите удалить все данные доменного пользователя?")) {
        return true;
    } else {
        return false;
    }
}

// DOMAIN_USER.PHP - КОНЕЦ
// ######################################################