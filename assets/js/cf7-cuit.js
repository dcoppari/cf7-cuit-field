    jQuery(document).ready(function($){
        var $fields = $('.wpcf7-cuit');

        if ( ! $fields.length ) return false;

        $fields.each(function(){
            var $this = $(this);
            var mask = $this.data('mask');

            if (!mask) return;

            $this.mask(mask, { 'autoclear': false } );

        });
    });
