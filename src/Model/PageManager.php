<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function updatePageLastView($hostname, $url, \DateTime $visitDateTime)
    {
        $select = $this->database->prepare(
            'SELECT `website_id` FROM `websites` WHERE `hostname` = :hostname'
        );
        $select->bindParam(':hostname', $hostname, \PDO::PARAM_STR);
        $select->execute();
        $websiteId = $select->fetchColumn();

        $lastView = $visitDateTime->format('Y-m-d H:i:s');
        $stmt = $this->database->prepare(
            'UPDATE `pages` SET `lastView` = :dateTime WHERE `url` = :url AND `website_id` = :websiteId'
        );
        $stmt->bindParam(':dateTime', $lastView, \PDO::PARAM_STR);
        $stmt->bindParam(':url', $url, \PDO::PARAM_STR);
        $stmt->bindParam(':websiteId', $websiteId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}