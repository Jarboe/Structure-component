<?php

namespace Jarboe\Component\Structure\Model;

use Cache;
use Jarboe;


class Structure extends \Baum\Node 
{
     
    protected $table = 'structure';
    protected $parentColumn = 'parent_id';

    protected $nodeUrl = null;
    
    public static function flushCache()
    {
        Cache::tags('tree-'. mb_strtolower(static::class))->flush();
    } // end flushCache
    
    public function setSlugAttribute($value)
    {
        // FIXME:
        $slug = Jarboe::urlify($value);
        
        $slugs = $this->where('parent_id', $this->parent_id)
                      ->where('id', '<>', $this->id)
                      ->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")
                      ->lists('slug');

        $slugCount = '';
        if ($slugs->count()) {
            $slugCount = 0;
            
            foreach ($slugs as $existedSlug) {
                if (preg_match('~(\d+)$~', $existedSlug, $matches)) {
                    $slugCount = $slugCount > $matches[1] ? $slugCount : $matches[1];
                }
            }
            $slugCount++;
        }
        
        $slug = $slugCount ? $slug .'-'. $slugCount : $slug;
        
        $this->attributes['slug'] = $slug;
    } // end setSlugAttribute

    public function hasTableDefinition()
    {
        $templates = config('jarboe.c.structure.templates');
        $template = config('jarboe.c.structure.default');
        if (isset($templates[$this->template])) {
            $template = $templates[$this->template];
        }

        return $template['type'] == 'table';
    } // end hasTableDefinition

    public function setUrl($url)
    {
        $this->nodeUrl = $url;
    } // end setUrl

    public function getUrl()
    {
        if (is_null($this->nodeUrl)) {
            $this->nodeUrl = $this->getGeneratedUrl();
        }
        return $this->nodeUrl;
    } // end getUrl

    public function isActive($setIdent = false)
    {
        $activeField = config('jarboe.c.structure.node_active_field.field');
        $options = config('jarboe.c.structure.node_active_field.options', array());
        
        if (!$options) {
            return $this->$activeField == 1;
        }
        
        if ($setIdent) {
            return !!preg_match('~'. preg_quote($setIdent) .'~', $this->$activeField);
        }
        
        foreach ($options as $ident => $caption) {
            if (preg_match('~'. preg_quote($ident) .'~', $this->$activeField)) {
                return true;
            }
        }
        
        return false;
    } // end isActive

    public function getGeneratedUrl()
    {
        $all = $this->getAncestorsAndSelf();

        $slugs = array();
        foreach ($all as $node) {
            if ($node->slug == '/') {
                continue;
            }
            $slugs[] = $node->slug;
        }

        return implode('/', $slugs);
    } // end getGeneratedUrl

}