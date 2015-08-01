<?php 

namespace Jarboe\Component\Structure\Http\Controllers;

use App;
use Input;


class TreeController extends \App\Http\Controllers\Controller
{

    protected $node;

    public function init($node, $method)
    {
        // FIXME: move paramter to config
        if (!$node->isActive(App::getLocale()) && !Input::has('show')) {
            App::abort(404);
        }

        $this->node = $node;

        return $this->$method();
    } // end init

    public function showThemeMain()
    {
        return view('admin::welcome');
    } // end showThemeMain

}