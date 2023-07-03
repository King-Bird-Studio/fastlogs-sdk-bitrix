<?php

namespace KingbirdFastlog;

class Config
{
    /** @var $url string */
    protected $url;

    /** @var $slug string */
    protected $slug;

    public function __construct($slug, $url)
    {
        $this->slug = $slug;
        $this->url = $url;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
