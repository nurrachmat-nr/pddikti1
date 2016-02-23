// tree filter

function initFilterTree() {
	$(".navigation").treeview({
		persist: "location"
	});
	
	$(".navigation a").click(function() {
		id1 = $(this).parents("ul.navigation:eq(0)").attr("id");
		id2 = $(this).parents("li:eq(0)").attr("id");
		
		goFilterAddTree(id1,id2);
	});
	
	var cookieval = $.cookie(cookiename);
	if(typeof(cookieval) == "undefined")
		cookieval = 0;
	
	$("#div_filtertree").accordion({
		heightStyle: "fill",
		active: parseInt(cookieval),
		activate: function(event,ui) {
			$.cookie(cookiename,$("#div_filtertree h3").index(ui.newHeader));
		}
	});
	
	$("#tr_treeon a").click(function() {
		arrid = this.id.split(":");
		
		goFilterAddTree(arrid[0],arrid[1]);
	});
}

function goFilterAddTree(key,str) {
	document.getElementById("filter_string").value = key + ":" + str;
	
	goSubmit('do_filter_tree');
}

// tree controller

function initControlTree() {
	$(".navigation").treeview({
		persist: "location"
	});
	
	$(".navigation a").click(function() {
		id = $(this).parents("li:eq(0)").attr("id");
		
		goControlTree(id);
	});
	
	var cookieval = $.cookie(cookiename);
	if(typeof(cookieval) == "undefined")
		cookieval = 0;
	
	$("#div_filtertree").accordion({
		heightStyle: "fill",
		active: parseInt(cookieval),
		activate: function(event,ui) {
			$.cookie(cookiename,$("#div_filtertree h3").index(ui.newHeader));
		}
	});
}

function goControlTree(method) {
	location.href = control+method;
}