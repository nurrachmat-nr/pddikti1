var g_param;

function setFilterTable(param) {
	g_param = param;
	
	$('#filtertable_block').css('top', g_mouse_coords.top + 'px');
	$('#filtertable_block').css('left', g_mouse_coords.left + 'px');
	$('#filtertable_block').show();
	
	pushPopWindow('filtertable_block');
}

function generateFilterTable() {
	jQuery("<div />")
		.attr("id", "filtertable_block")
		.appendTo(jQuery("body"));
	
	var str = '<table width="100" class="menu-body" cellpadding="3" cellspacing="3">';
		str += '<tr class="menu-button" onMouseMove="this.className = \'hover\';" onMouseOut="this.className = \'\';">'
		str +=	'<td onClick="goSort(\'asc\');" > <span class="sort_asc"> Sort Asc</span></td>';
		str += '</tr>';
		str += '<tr class="menu-button" onMouseMove="this.className = \'hover\';" onMouseOut="this.className = \'\';">';
		str += '	<td onClick="goSort(\'desc\');"> <span class="sort_desc"> Sort Desc</span></td>';
		str += '</tr>';
		str += '<tr>';
		str += '	<td class="separator"><div class="separator-line"></div></td>';
		str += '</tr>';
		str += '<tr class="menu-button" onMouseMove="this.className = \'hover\';" onMouseOut="this.className = \'\';">';
		str += '	<td onClick="goFilter(true);"> <span class="add_filter"> Filter ...</span></td>';
		str += '</tr>';
		str += '<tr class="menu-button" onMouseMove="this.className = \'hover\';" onMouseOut="this.className = \'\';">';
		str += '	<td onClick="goFilter(false);"> <span class="remove_filter"> Remove Filter</span></td>';
		str += '</tr>';
		str += '</table>';
	
	$('#filtertable_block').append(str);

	str = '<div id="filtertext_block" name="filtertext_block" class="filter_dialog" onBlur="$(\'#filtertext_block\').hide()" > ';
	str += 'Filter Criteria <br>';
	str +=	'<input class="filter_text" type="text" name="txt_filter" id="txt_filter" size="20" onKeyDown="return doFilter(event);" onBlur="$(\'#filtertext_block\').hide()">';
	str += '</div>';

	$('body').append(str);

}

$(document).ready(function() {
	generateFilterTable();

	$(document).click(function(e) {
		// if(e.target.className != "filtertable_header")
		if(!$(e.target).is(".filtertable_header"))
			$("#filtertable_block").css("display","none");
	});

});

function goSort(direction) {
	if (g_param) {
		arrParam = g_param.split(":");
		document.getElementById("filter_sort").value = arrParam[0] + ' ' + direction;
		goSubmit('do_sort');
	}
}

function goFilter(filterOn) {
	if (!filterOn) {
		goSubmit('reset_filter');
	}
	else {
		$('#filtertext_block').css('top', $('#filtertable_block').css('top'));
		$('#filtertext_block').css('left', $('#filtertable_block').css('left'));
		$('#filtertext_block').css('display','inline');
		$('#filtertext_block').show();
		$('#txt_filter').val('');
		$('#txt_filter').focus();
	}
}

function doFilter(e) {
	var ev = (window.event) ? window.event: e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key==13 && g_param) // jika ditekan tombol enter
	{
		// processing filter
		arrParam = g_param.split(":");
		var columnFilter = arrParam[0];
		var columnType = arrParam[1];
		var retval;
		retval = document.getElementById("txt_filter").value;
		if (retval)
		{
			if (document.getElementById("filter_string").value)  // tambah kriteria filter
				document.getElementById("filter_string").value = document.getElementById("filter_string").value + ':';
			document.getElementById("filter_string").value = document.getElementById("filter_string").value + columnFilter  + ':' + retval + ':' + columnType;
			document.getElementById("filter_page").value = 1;	
			goSubmit('do_filter');
		}
	} 
	else if (key==27) // jika ditekan tombol escape
		$("#filtertext_block").hide();
}

function removeFilter(idx) {
	goSubmit('remove_filter_'+idx);
}

function goRefresh() {
	goSubmit('do_refresh');
}

function goReset() {
	goSubmit('reset_filter');
}
