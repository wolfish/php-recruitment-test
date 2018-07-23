<?php

namespace Snowdog\DevTest\Model;

class Varnish
{

    public $varnish_id;
    public $ip;
    public $website_id;
    public $user_id;

    public function __construct()
    {
        $this->varnish_id = intval($this->varnish_id);
        $this->website_id = intval($this->website_id);
        $this->user_id = intval($this->user_id);
    }

    /**
     * @return int
     */
    public function getVarnishId()
    {
        return $this->varnish_id;
    }

    /**
     * @param int $varnish_id
     */
    public function setVarnishId($varnish_id)
    {
        $this->varnish_id = $varnish_id;
    }

    /**
     * @return string
     */
    public function getIP()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIP($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->website_id;
    }

    /**
     * @param int $website_id
     */
    public function setWebsiteId($website_id)
    {
        $this->website_id = $website_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

}