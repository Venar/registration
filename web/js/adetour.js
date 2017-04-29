function submitenter(myfield,e) {
	var keycode;
	if (window.event) {
		keycode = window.event.keyCode;
	} else if (e) {
		keycode = e.which;
	} else {
		return true;
	}
	
	if (keycode == 13) {
		myfield.form.submit();
		return false;
	} else {
		return true;
	}
}

function hL(E) {
	var ie = document.all;
	if (ie) {
		while (E.tagName!="TR") {
			E=E.parentElement;
		}
		
		E.className = "Highlight";
	}
}

function dL(E, CN, haveCB) {
	var ie = document.all;
	if (ie) {
		while (E.tagName!="TR") {
			E=E.parentElement;
		}
		
		if (haveCB) {
			checkCS(E, CN);
		} else {
			E.className = CN;
		}
	}
}

function checkCS(E, CN) {
	var ie = document.all;
	var CB = E;
	var i = 0;
	if (ie) {
		while (CB.tagName!="INPUT") {
			CB=E.all[i];
			i++;
		}
		
		//if(!CB.checked)
			E.className = CN;
		//else
		//	E.className = "Selected";
	}
}

function ismaxlength(obj) {
	var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
	if (obj.getAttribute && obj.value.length>mlength) {
		obj.value=obj.value.substring(0,mlength);
	}
}

/**
 * Begin AD EM Code
 */
var XaddrX;
var XsubX;

SiteString='@'+'animedetour'+'.'+'com';
function SetAddress(input) {
	switch(input) {
		case "ad1":				XaddrX = "ad_com";				XsubX="[AD_ADS]";				break;
		case "amv1":			 XaddrX = "ad_prog";			 XsubX="[AD_AMV]";				break;
		case "art1":			 XaddrX = "ad_art";				XsubX="[AD_ART]";				break; 
		case "com1":			 XaddrX = "ad_com";				XsubX="[AD_COM]";				break; 
		case "cosplay1":	 XaddrX = "ad_cosplay";		XsubX="[AD_COSPLAY]";		break;
		case "dealers1":	 XaddrX = "ad_dealers";		XsubX="[AD_DEALERS]";		break;
		case "fanfic1":		XaddrX = "ad_fanfic";		 XsubX="[AD_FANFIC]";		 break;
		case "feedback1":	XaddrX = "ad_feedback";	 XsubX="[AD_FEEDBACK]";	 break;
		case "gaming1":		XaddrX = "ad_gaming";		 XsubX="[AD_GAMING]";		 break;
		case "guests1":		XaddrX = "ad_guests";		 XsubX="[AD_GUESTS]";		 break;
		case "hotel1":		 XaddrX = "ad_hotel";			XsubX="[AD_HOTEL]";			break;
		case "info1":			XaddrX = "ad_info2";			XsubX="[AD_INFO]";			 break;
		case "karaoke1":	 XaddrX = "ad_karaoke";		XsubX="[AD_PROG]";			 break;
		case "ops1":			 XaddrX = "ad_operations"; XsubX="[AD_OPS]";				break;
		case "prog1":			XaddrX = "ad_prog";			 XsubX="[AD_PROG]";			 break;
		case "parties1":	 XaddrX = "ad_parties";		XsubX="[AD_PARTIES]";		break;
		case "reg1":			 XaddrX = "ad_register";	 XsubX="[AD_REG]";				break;
		case "tres1":			XaddrX = "ad_tres";			 XsubX="[AD_TRES]";			 break;
		case "sec1":			 XaddrX = "ad_security";	 XsubX="[AD_SECURITY]";	 break;
		case "video1":		 XaddrX = "ad_video";			XsubX="[AD_VIDEO]";			break;
		case "vol1":			 XaddrX = "ad_vol";				XsubX="[AD_VOL]";				break;
		case "webteam1":	 XaddrX = "webteam";			 XsubX="[AD_WEB]";				break;
		default: break;
	}
}

function GetAddress(input,XsubX1) {
	SetAddress(input);
	
	if(XsubX1 != "") {
		XsubX=XsubX1;
	}
	
	window.location='mail'+'to:'+ XaddrX +SiteString+'?'+'subject'+'='+ XsubX;
}
/**
 * End AD EM Code
 */

