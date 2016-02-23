<script type="text/javascript">
    $(document).ready(function () {
        $("#jqGrid").jqGrid({
            //url: 'http://trirand.com/blog/phpjqgrid/examples/jsonp/getjsonp.php?callback=?&qwery=longorders',
            url: '<?php echo base_url(); ?>index.php/kk/jsonJG',
            mtype: "GET",
            //mtype : "POST",
            styleUI : 'Bootstrap',
            //datatype: "jsonp",
            datatype: "json",
            //colNames:['ID KK','NM KK'],
            colModel: [
                        /*{ name:'id_kk',index:'id_kk', align:"center" },
                        { name:'nm_kk',index:'nm_kk'},
                        { name: 'OrderID', key: true, width: 75 },*/
                        /*{ name: 'id_kk', width: 30 },
                        { index:'nm_kk', name: 'nm_kk', key: true }*/
                        {   label: 'ID KK', 
                            name: 'id_kk', 
                            index: 'id_kk', 
                            key: true,
                            width: 75, 
                        },
                        { label: 'Kebutuhan Khusus', name: 'nm_kk', index: 'nm_kk'}
                    ],
            //loadonce: true,
            viewrecords: true,
            height: 'auto',
            width: 500,
            rowNum: 10,
            rowList:[10,25,50,100],
            pager: "#jqGridPager",
            jsonReader: { repeatitems : false },
            //caption:"Daftar Kebutuhan Khusus"
        });
        $('#jqGrid').jqGrid('filterToolbar', {
            searchOperators: true,
            stringResult: true
        });
        /*$('#jqGrid').jqGrid('filterToolbar', {
            searchOnEnter: false,
            searchOperators: true,
            //multipleSearch: true,
            stringResult: true,
            //groupOps: [{ op: "AND", text: "all" }, { op: "OR", text: "any" }],
            //defaultSearch: 'cn', ignoreCase: true
        });*/
        $('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });
        $(window).bind('resize', function() {
            //$("#jqGrid").jgGrid()setGridWidth($(".container-fluid").width()-30,true);
            $("#jqGrid").jqGrid( 'setGridWidth', $(".container-fluid").width()-30 );
        }).trigger('resize');
        
    });
</script>