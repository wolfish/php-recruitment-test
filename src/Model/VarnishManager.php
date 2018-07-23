<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllByUser(User $user)
    {
        $query = $this->database->prepare(
            'SELECT varnish_id, INET_NTOA(ip) ip, user_id FROM `varnishes` WHERE `user_id` = :userId'
        );
        $userId = $user->getUserId();
        $query->bindParam(':userId', $userId);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param Varnish $varnish
     * @return array
     */
    public function getWebsites(Varnish $varnish)
    {
        $query = $this->database->prepare(
            'SELECT we.* FROM `websites` we LEFT JOIN `varnish_has_website` vhw ON vhw.website_id = we.website_id WHERE `varnish_id` = :varnishId'
        );
        $varnishId = $varnish->getVarnishId();
        $query->bindParam(':varnishId', $varnishId);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    /**
     * @param Website $website
     * @return array
     */
    public function getByWebsite(Website $website)
    {
        $query = $this->database->prepare(
            'SELECT va.varnish_id, INET_NTOA(ip) ip, user_id FROM `varnishes` va ' .
            'INNER JOIN `varnish_has_website` vhw ON va.varnish_id = vhw.varnish_id ' .
            'WHERE vhw.website_id = :websiteId'
        );
        $websiteId = $website->getWebsiteId();
        $query->bindParam(':websiteId', $websiteId);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param User $user
     * @param string $ip
     * @return bool
     */
    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();

        $exists = $this->database->prepare(
            'SELECT `varnish_id` FROM `varnishes` WHERE `ip` = INET_ATON(:ip) AND `user_id` = :userId'
        );
        $exists->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $exists->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $exists->execute();

        if ($exists->rowCount()) {
            return false;
        }

        $stmt = $this->database->prepare(
            'INSERT INTO `varnishes` (ip, user_id) VALUES (INET_ATON(:ip), :userId)'
        );
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $this->database->lastInsertId() ? true : false;
    }

    /**
     * @param int $varnish
     * @param int $website
     * @return bool
     */
    public function link($varnish, $website)
    {
        $stmt = $this->database->prepare(
            'INSERT INTO `varnish_has_website` VALUES (:varnishId, :websiteId)'
        );
        $stmt->bindParam(':varnishId', $varnish, \PDO::PARAM_INT);
        $stmt->bindParam(':websiteId', $website, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    /**
     * @param int $varnish
     * @param int $website
     * @return bool
     */
    public function unlink($varnish, $website)
    {
        $stmt = $this->database->prepare(
            'DELETE FROM `varnish_has_website` WHERE varnish_id = :varnishId AND website_id = :websiteId'
        );
        $stmt->bindParam(':varnishId', $varnish, \PDO::PARAM_INT);
        $stmt->bindParam(':websiteId', $website, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

}