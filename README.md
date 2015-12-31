# WP Ajax Helper
*A library to simplify AJAX requests in WordPress plugins and themes.*

## What is This?

WP Ajax Helper exists to simplify the process of performing Ajax requests in WordPress.

With a simple, easy-to-understand syntax, it handles nonces and validation automatically, freeing you up to worry about more important things.

It also interprets the Ajax payload, passing it to your callback function as an array or string as appropriate, as well as interpreting the callback's response and returning it to the browser either as a string or as JSON.

With an simple validation component, you can easily restrict the user permissions under which the callback function will run.

Finally, it provides a JavaScript front-end component which interfaces with the backend library.

## Getting Started

### Backend

Include the package in your project using Composer, or simply include wp-ajax-helper.php from somewhere in your project.

The most basic usage is to call `wp_ajax_helper()` with a handle and a callback function, like this:

`wp_ajax_helper()->handle( 'my-handle' )->with_callback( 'my_callback' );`

The handle is the unique identifier for this instance, and the callback function is the function which should handle the Ajax request.

### Frontend

Whenever a handle is registered, the JavaScript function `wpAjaxHelperRequest()` will be available to use in your scripts. Pass in the handle of the callback you want to call, and include an optional payload as the second parameter, which will be passed into your callback function.

`wpAjaxHelperRequest( 'my-handle', 'payload' );`

The payload can be either a string or an object. If an object is sent, it will be converted to a PHP array when passed to your callback function.

You'll probably want to do something with the response to your Ajax request; that is detailed below.

## Advanced Usage

### Backend: Callbacks

The callback can be a function name, a class method, or a closure.

Here are several examples illustrating various ways a callback can be registered:

```
wp_ajax_helper->handle( 'my-handle' )->with_callback( 'function_name' );
```

```
$args = array( // Some arguments here. );
wp_ajax_helper->handle( 'my-handle' )->with_callback( 'function_name', $args );
```

```
wp_ajax_helper->handle( 'my-handle' )->with_callback( array( $class, 'method_name' ) );
```

Two parameters will be provided to the callback function when it is executed: `$ajax_payload` and `$args`. The first parameter is the Ajax payload from the request. The second is an array of values which were (optionally) specified as the second parameter when registering the handle.

```
function my_ajax_callback( $ajax_payload, $args ) {

    // Do something with the ajax payload.
    do_something_with( $ajax_payload['my-value-1'] );
    do_something_with( $ajax_payload['my-value-2'] );

    // Return an array, which will be converted to JSON before being sent back to the browser.
    return array(
        'key1' => 'value1',
        'key2' => 'value2',
    );
}
```

If the callback function returns a string, the Ajax response will be a string. If the callback returns an array, the Ajax response will be that array, converted to a JSON object. If the callback function returns anything else, i.e. null, a WP_Error object, or throws an exception, the response will be an error.

### Backend: Validation

To specify validations which need to pass before the callback function can run, pass in an array of validations to the `with_validation()` method, with the values to test in the following format.

```
$validations = array(
    'validation_name' => 'value to test'
);
wp_ajax_helper->handle( 'my-handle' )->with_validation( $validations );
```

The following validations are available:

 * logged_in (boolean)
 * user_can (capability name)
 * user_is (role name)

Example usage:

```
$validations = array(
    'logged_in' => true,
    'user_can'  => 'manage_options',
    'user_is'   => 'administrator',
);
wp_ajax_helper->handle( 'my-handle' )->with_validation( $validations );
```

All validations are optional.

### Backend: Filters

The following filters are available, which will allow you to filter the Ajax payload before being sent to a callback function, as well as filtering the output of the callback function before being sent back to the browser.

 * Filter: `WP_Ajax_Helper\ajax_payload`
      **Parameters:**
       * $payload: The Ajax payload.
       * $handle: The handle name.
   
 * Filter: `WP_Ajax_Helper\callback_response`
      **Parameters:**
       * $response: The callback response.
       * $handle: The handle name.
       * $callback_args: The arguments which were passed to the callback function.
       * $ajax_payload: The Ajax payload which was passed to the callback function.

### Frontend: Handling the Response

The `wpAjaxHelperRequest` function will return a jQuery $.POST response, which you can utilize just like a normal $.POST request to handle the response:

```
wpAjaxHelperRequest( "my-handle", payload )
    .success( function( response ) {
        console.log( "Woohoo!" );
        // 'response' will be the response from the handle's callback function, as either a string or JSON.
        console.log( response );
    })
    .error( function( response ) {
        console.log( "Uh, oh!" );
        console.log( response.statusText );
    });
```