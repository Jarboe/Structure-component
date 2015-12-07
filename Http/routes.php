<?php

Route::group(array('prefix' => config('jarboe.admin.uri'), 'before' => array('auth_admin', 'check_permissions')), function() {

    Route::any('/tree', 'Jarboe\Component\Structure\Http\Controllers\AdminController@tree');

});


//
\Jarboe\Component\Structure\Util::registerRoutes();
