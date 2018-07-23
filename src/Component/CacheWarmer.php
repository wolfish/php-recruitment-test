<?php

namespace Snowdog\DevTest\Component;

use Snowdog\DevTest\Model\Varnish;

class CacheWarmer extends \Old_Legacy_CacheWarmer_Warmer
{

    /**
     * @param string $url
     * @param Varnish $varnish
     */
    public function warm($url, $varnish = null) {
        if ($varnish instanceof Varnish) {
            $ip = $varnish->getIP();
        } else {
            $ip = $this->resolver->getIp($this->hostname);
        }

        sleep(1); // this emulates visit to http://$hostname/$url via $ip
        $this->actor->act($this->hostname, $ip, $url);
    }
}
