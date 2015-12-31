function wpAjaxHelperRequest( action, payload, settings ) {

    action = action.replace( /-/g, "_" );

    var ajaxParams = {
        method: 'POST',
        data: {
            action:  action,
            nonce:   wpAjaxHelper.handles[ action ],
            payload: payload
        }
    };

    ajaxParams = jQuery.extend( ajaxParams, settings );

    return jQuery.ajax( wpAjaxHelper.ajaxUrl, ajaxParams );
}
