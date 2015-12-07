<?php

namespace Jarboe\Component\Structure;

use App;
use Route;
use Cache;


class Util extends \Yaro\Jarboe\Component\AbstractUtil
{
    
    public static function install($command)
    {
        self::copyIfNotExist($command, 'resources/definitions/tree/node.php', __DIR__);
    } // end install
    
    public static function getNavigationMenuItem() 
    {
        return array(
            'title' => 'Структура сайта',
            'icon'  => 'navicon',
            'link'  => '/tree',
            'check' => function() {
                return true;
            }
        );
    } // end getNavigationMenuItem
    
    public static function check()
    {
        $errors = array();
        if (!class_exists('Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider')) {
            // TODO:
            //$errors[] = 'Structure component requires [Mcamara\LaravelLocalization]: "mcamara/laravel-localization": "1.0.*"';
        }
        
        return $errors;
    } // end check

    private static function recurseTree(&$current, $tree, $node, &$slugs = array())
    {
        if (!$node['parent_id']) {
            return $node['slug'];
        }
    
        $slugs[] = $node['slug'];
        $idParent = $node['parent_id'];
        if ($idParent) {
            $parent = $tree[$idParent];
            $current->addBreadcrumb($parent);
            self::recurseTree($current, $tree, $parent, $slugs);
        }
    
        return implode('/', array_reverse($slugs));
    } // end recurseTree
    
    private static function registerSingleRoute($urlPath, $node)
    {
        Route::get($urlPath, function() use($node)
        {
            $templates = config('jarboe.c.structure.templates');
            if (!isset($templates[$node->template])) {
                // just to be gentle with web crawlers
                abort(404);
            }
            list($controller, $method) = explode('@', $templates[$node->template]['action']);
            $controller = '\\'. ltrim($controller, '\\');

            return app()->make($controller)->callAction(
                'init', 
                [$node, $method]
            );
        });
    } // end registerSingleRoute

    public static function registerRoutes($model = false)
    {
        $model = $model ? : config('jarboe.c.structure.model');
        $tags = array('jarboe', 'j_tree', 'tree-'. mb_strtolower($model));
        
        // FIXME: make a little bit pretty
        $tree = Cache::tags($tags)->get('tree');
        if ($tree) {
            foreach ($tree as $node) {
                self::registerSingleRoute($node->getUrl(), $node);
            }
        } else {
            $nodeUrl = '';
            
            // HACK: if we dont have table, so dont crash whole site
            try {
                $tree = $model::all(); 
            } catch (\Exception $e) {
                return;
            }
            
            
            $clone = clone $tree;
            $clone = $clone->toArray();
            //
            $clone = array_combine(array_column($clone, 'id'), $clone);
        
            foreach ($tree as $node) {
                $nodeUrl = self::recurseTree($node, $clone, $node);
                $node->setUrl($nodeUrl);
                
                self::registerSingleRoute($nodeUrl, $node);
            }
        
            Cache::tags($tags)->put('tree', $tree, 1440);
        }
        
        
        unset($clone);
        unset($tree);
    } // end registerRoutes

}

    