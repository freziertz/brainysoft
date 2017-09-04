/*! DataTables 1.10.4
 * ©2008-2014 SpryMedia Ltd - datatables.net/license
 */

jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 10,
                time: 1000
            });
        });


var table = $('#example').DataTable( {
    buttons: [
        'copy', 'csv','excel', 'pdf','print'
    ],
   
    paging: true,
    ordering: true,
    "order": [[ 0, "asc" ]],
    colReorder: true,
    //rowReorder: true,
    info:     false,
    //fixed column
    

    
    //"dom": '<"top"i>rt<"bottom"flp><"clear">'
} );
  
table.buttons().container()
    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );

/*table.on( 'row-reorder', function ( e, diff, edit ) {
    for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
        $(diff[i].node).addClass("reordered");
    }
} )*/;


//*********************************************************

//DATE PICKER

$('.datepicker').datepicker({
	format: 'yyyy-mm-dd',
    autoclose: true
});



//****************************************************************



