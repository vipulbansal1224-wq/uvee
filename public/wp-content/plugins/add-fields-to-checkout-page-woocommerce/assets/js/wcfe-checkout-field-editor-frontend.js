jQuery(document).ready(function($) {

	var currRequest = null;

	// Frontend Chosen selects
	if ( $().select2 ) {
		$( 'select.checkout_chosen_select:not(.old_chosen), .form-row .select:not(.old_chosen)' ).filter( ':not(.enhanced)' ).each( function() {
			$( this ).select2( {
				minimumResultsForSearch: 10,
				allowClear:  true,
				placeholder: $( this ).data( 'placeholder' )
			} ).addClass( 'enhanced' );
		});
	}

	$( '.checkout-date-picker' ).datepicker({
		numberOfMonths: 1,
		showButtonPanel: true,
		changeMonth: true,
      	changeYear: true,
		yearRange: "-100:+1"
	});

	$('p[id]').each(function () {
        var ids = $('[id=' + this.id + ']');
        if (ids.length > 1 && ids[0] == this) {
            $(ids[1]).remove();
        }
    });
	
	$.fn.getType = function(){
		try{
			return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); 
		}catch(err) {
			return 'E001';
		}
	}
	
   /****************************************
    ------- EXTRA COST FIELD - END ---------
	****************************************/		
});