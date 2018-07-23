<?php

namespace Snowdog\DevTest\Model;

class Page
{

    public $page_id;
    public $url;
    public $website_id;
    public $lastView;

    public function __construct()
    {
        $this->website_id = intval($this->website_id);
        $this->page_id = intval($this->page_id);
        $this->lastView = $this->lastView ? new \DateTime($this->lastView) : null;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->website_id;
    }

    /**
     * @return string
     */
    public function getLastView()
    {
        return $this->lastView instanceof \DateTime ?
            $this->lastView->format('Y-m-d H:i:s')
            :
            'No visits yet';
    }


}