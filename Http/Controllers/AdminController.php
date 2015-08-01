<?php

namespace Jarboe\Component\Structure\Http\Controllers;

use Jarboe;


class AdminController extends \App\Http\Controllers\Controller
{
    
    public function tree()
    {
        return Jarboe::tree('\Jarboe\Component\Structure\Model\Structure');
    } // end tree
        
}
