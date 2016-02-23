(function ($) {
	var ajaxtimeout = 20000;
	
	$(document).ajaxError(function(event,request,settings,error) {
		alert("Terjadi kesalahan dalam pengambilan data");
	});
	
	// untuk mendapatakan option select
	$.fn.xhrSetOption = function (kode, param, callback) {
		var ajaxurl = g_abs_url + "ajax/" + kode;
		if (typeof(param) == "object")
			ajaxurl += "/" + param.join("/");
		
		return $(this).each(function() {
			var jqelem = $(this);
			
			var jqxhr = $.ajax({
				url: ajaxurl,
				timeout: ajaxtimeout
			});
			jqxhr.done(function(data) {
				jqelem.html(data);
				
				// panggil fungsi callback
				if (typeof(callback) == "function")
					callback();
			});
		});
	}
	
	// untuk autocomplete dengan ajax (menggunakan jquery ui autocomplete)
	$.fn.xhrAutoComplete = function (idtarget, kode, options) {
		var ajaxurl = g_abs_url + "ajax/" + kode;
		
		var settings = $.extend({
			minlength: 2, onetarget: true, selflabel: false
		}, options);
		
		return $(this).each(function() {
			$(this).autocomplete({
				source: function(request, response) {
					ajaxurlac = ajaxurl + "/" + request.term;
					
					var jqxhr = $.ajax({
						url: ajaxurlac,
						timeout: ajaxtimeout,
						dataType: "json"
					});
					jqxhr.done(function(data) {
						response($.map(data.items,function(item) {
							return {
								//label: (settings.selflabel ? item.label : item.value + ' - ' + item.label),
								label: item.label,
								value: item.value
							}
						}));
					});
				},
				minLength: settings.minlength,
				select: function(event,ui) {
					event.preventDefault();
					
					if(settings.onetarget)
						etarget = $("#"+idtarget);
					else
						etarget = $(this).nextAll("[id='"+idtarget+"']");
					
					etarget.val(ui.item.value);
					$(this).val(ui.item.label);
				} /*,
				change: function(event,ui) {
					$("#"+idtarget).val("");
				} */
			});
		});
		/*
		
		*/
	}
	
	$.fn.xhrAutoSelect = function (kode) {
		var ajaxurl = g_abs_url + "ajax/" + kode;
		
		return $(this).each(function() {
			$(this).autocomplete({
				source: function(request, response) {
					ajaxurlac = ajaxurl + "/" + request.term;
					
					var jqxhr = $.ajax({
						url: ajaxurlac,
						timeout: ajaxtimeout,
						dataType: "json"
					});
					jqxhr.done(function(data) {
						response($.map(data.items,function(item) {
							return {
								label: item.label
							}
						}));
					});
				},
				minLength: 0
			});
			
			$(this).mousedown(function() {
				$(this).autocomplete("search","");
			});
		});
	}
	
	// untuk meload page
	$.fn.xhrSetHTML = function (kode, param, callback) {
		var ajaxurl = g_abs_url + "ajax/" + kode + "/" + param;
		return $(this).each(function() {
			var jqelem = $(this);
			// beri loading
			//jqelem.html('<img src="' + base_assets() + 'images/loading.gif" />');
			jqelem.html('<img src="'+ g_abs_url +'application/assets/images/loading.gif" />');
			var jqxhr = $.ajax({
				url: ajaxurl,
				timeout: ajaxtimeout
			});
			jqxhr.done(function(data) {
				jqelem.html(data);
				// panggil fungsi callback
				if (typeof(callback) == "function")
					callback();
			});
		});
	}
	
	// pesan error
})(jQuery);