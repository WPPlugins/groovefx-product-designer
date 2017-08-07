var is_pres = false;

function groovefx_open(type,param)  {
	is_pres = true;
	//NOM DE LA DIV POUR INSERTION DE LA POPIN
	 var nom_div = 'main';
	//NOM DU CLIENT
	
	var is_ok=true;
	if (jQuery('#groovefx_id_groo').val()!='') {
		if (typeof confirm_text !== 'undefined') {
			if (!confirm(confirm_text)) is_ok=false;;
		}
		
	}
	
	var lang = jQuery('#groovefx_language').val();
	if (lang=='EN' || lang == 'US') lang = 'GB';
	
	url_def = url_site_groo+nom_groo+'/index.php?type='+type+param+'&fx_langue='+lang+'&idClient='+ jQuery('#groovefx_id_client').val();
	
	if (is_ok){
		//Id de l'image du produit à remplacer par le nouveau rendu
		var id_image = 'id_image';
		
		var before = '<div id="div_groo_cont">	<div id="div_groo_wrapper" class="popup_wrapper" style="opacity: 1; visibility: visible; position: fixed;  z-index: 100001; transition: all 0.3s; -webkit-transition: all 0.3s; width: 100%; top: 0px; left: 0px; text-align: center; display: block;">';
		var after = '</div><div id="div_groo_background" class="popup_background" style="opacity: 0.9; visibility: visible; position: fixed; top: 0px; right: 0px; bottom: 0px; left: 0px; z-index: 100000; transition: all 0.3s; -webkit-transition: all 0.3s; background-color: black;"></div></div>';
		
		jQuery('#ajax-loading-screen').remove();
			
		if (!Modernizr.cssvwunit){				
			var wis = dw_getWindowDims();	
			jQuery(document.body).append(before+'<div id="div_groo" name="div_groo" class="groovefx_well_ss_ww" style="background-color:white;height:'+wis.height+'px;width:'+wis.width+'px;opacity: 1; visibility: visible; display: inline-block; outline: none;  text-align: left; position: relative; vertical-align: middle;"  ><iframe src="'+url_def+'" id="groovefx" name="groovefx" style="border:0;max-width:1200px;padding:0px;margin:0px;height:'+wis.height+'px;width:'+wis.width+'px;opacity:1;visibility: visible;"></iframe></div>'+after);
			window.onresize = groovefx_resize_iframe;
		} else {				
			jQuery(document.body).append(before+'<div id="div_groo" name="div_groo" class="groovefx_well" style="background-color:white;opacity: 1; visibility: visible; display: inline-block; outline: none;  text-align: left; position: relative; vertical-align: middle;" ><iframe src="'+url_def+'" id="groovefx" name="groovefx" style="border:0;max-width:1200px;padding:0px;margin:0px;height:100vh;width:100vw;opacity:1;visibility: visible;"></iframe></div>'+after);
		}
		
		var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
		var eventer = window[eventMethod];
		var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
		eventer(messageEvent, function (e) {
				
				
			if (e.origin == url_site_groo.substring(0,e.origin.length)) {
				 jQuery('#div_groo_cont').remove(); 		
				message = e.data;
				
				if (message!=''){
					arra = message.split("-");
					var src_img = 'http://img.gfxc2.com/'+nom_groo+'/'+arra[1]+'.jpg'; 
					
					var id = arra[1];
					jQuery('#groovefx_id_groo').val(id).trigger('change');;
					
					if (arra.length>2) {
						var couleur = arra[2];
						if (couleur.length==6) couleur = '#'+couleur;
						jQuery('#groovefx_coul_groo').val(couleur); 
					}
					
					jQuery(".images img" ).attr('width','');;
					jQuery(".images img" ).attr('height','');;
					jQuery(".images img" ).attr('srcset','');;
					jQuery(".images img" ).attr('src',src_img);;
				//	jQuery("#groovefx_rendu" ).css("background-color",couleur);;
					//jQuery('#personnalise_groovefx_bouton').css("display","none");
					
					if (jQuery('.cart_groovefx_perso').length) jQuery(".cart").not(".cart_groovefx_perso").css("display","");
					else jQuery('.groove_single_add_to_cart_button,.button,.alt').css('display','');	
					is_pres = false;
				}
			 }
			
		}, false);    			
		
		var ua = navigator.userAgent;
		if ( /iPhone|iPad|iPod/.test( navigator.platform )) {
			var iframe = document.getElementById('groovefx');
			if (iframe.attachEvent) {
				iframe.attachEvent("onload", groovefx_resize_iframe);
			} else {
				iframe.onload=groovefx_resize_iframe;
			}
			window.onresize = groovefx_resize_iframe;					
		}
	}
}

 function dw_getWindowDims() {
	var doc = document, w = window;
	var docEl = (doc.compatMode && doc.compatMode === 'CSS1Compat')?
	doc.documentElement: doc.body;
	scroll(0, 0);
	var header = jQuery(".header:visible");
	var footer = jQuery(".footer:visible");
	var content = jQuery(".content:visible");
	var ua = navigator.userAgent;
	if ( /iPhone|iPad|iPod/.test( navigator.platform )) var viewport_height = window.innerHeight;
	else var viewport_height = jQuery(window).height();
	var content_height = viewport_height - header.outerHeight() - footer.outerHeight();

	content_height -= (content.outerHeight() - content.height());
	var zoomLevel = document.documentElement.clientWidth / window.innerWidth;
	var height =  content_height * zoomLevel;
	var width = docEl.clientWidth;
	
	if (width>1200) width = 1200;
	if (height > 1200) height = 1200;
	return {width: width, height: height};
}
			


function groovefx_resize_iframe() {
	if (is_pres) {
		var wis = dw_getWindowDims();
		document.getElementById('groovefx').style.height = (wis.height) + 'px';
		document.getElementById('groovefx').style.width = (wis.width) + 'px';
		document.getElementById('div_groo').style.height = wis.height + 'px';
		document.getElementById('div_groo').style.width = wis.width + 'px';
	}
}

(function(w){
	var ua = navigator.userAgent;
	if( !( /iPhone|iPad|iPod/.test( navigator.platform ) && /OS [1-5]_[0-9_]* like Mac OS X/i.test(ua) && ua.indexOf( "AppleWebKit" ) > -1 ) ){
		return;
	}
    var doc = w.document;
	if( !doc.querySelector ){ return; }
   var meta = doc.querySelector( "meta[name=viewport]" ),
        initialContent = meta && meta.getAttribute( "content" ),
        disabledZoom = initialContent + ",maximum-scale=1",
        enabledZoom = initialContent + ",maximum-scale=10",
        enabled = true,
		x, y, z, aig;

    if( !meta ){ return; }
    function restoreZoom(){
        meta.setAttribute( "content", enabledZoom );
        enabled = true;
    }
    function disableZoom(){
        meta.setAttribute( "content", disabledZoom );
        enabled = false;
    }	
    function checkTilt( e ){
		aig = e.accelerationIncludingGravity;
		x = Math.abs( aig.x );
		y = Math.abs( aig.y );
		z = Math.abs( aig.z );
				
	  if( (!w.orientation || w.orientation === 180) && ( x > 7 || ( ( z > 6 && y < 8 || z < 8 && y > 6 ) && x > 5 ) ) ){
			if( enabled ){
				disableZoom();
			}        	
        }
		else if( !enabled ){
			restoreZoom();
        }
    }	
	w.addEventListener( "orientationchange", restoreZoom, false );
	w.addEventListener( "devicemotion", checkTilt, false );

})( this );

