<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;
use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;

class Version4
{
    /**
     * @var Database|\PDO
     */
    private $database;

    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(
        Database $database,
        VarnishManager $varnishManager,
        UserManager $userManager,
        WebsiteManager $websiteManager
    ) {
        $this->database = $database;
        $this->varnishManager = $varnishManager;
        $this->userManager = $userManager;
        $this->websiteManager = $websiteManager;
    }

    public function __invoke()
    {
        $this->createVarnishesTable();

        $varnishId = $this->addVarnishes();
        if ($varnishId) {
            $this->linkVarnishes($varnishId);
        }
    }

    private function createVarnishesTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes` (
    `varnish_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `ip` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned NOT NULL,
    PRIMARY KEY (`varnish_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `varnish_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `varnish_has_website` (
    `varnish_id` int(11) unsigned NOT NULL,
    `website_id` int(11) unsigned NOT NULL,
    KEY `website_id` (`website_id`),
    CONSTRAINT `varnish_has_website_website_fk` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    KEY `varnish_id` (`varnish_id`),
    CONSTRAINT `varnish_has_website_varnish_fk` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`varnish_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `varnish_has_website_unique` UNIQUE (`varnish_id`, `website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function addVarnishes()
    {
        $user = $this->userManager->getByLogin('test');
        if (!$user instanceof User) {
            return false;
        }

        $this->varnishManager->create($user, '8.8.8.8');
        return $this->varnishManager->create($user, '25.25.25.25');
    }

    private function linkVarnishes($varnishId)
    {
        $website = $this->websiteManager->getById(1);
        if (!$website instanceof Website) {
            return false;
        }

        return $this->varnishManager->link($varnishId, $website->getWebsiteId());
    }
}
