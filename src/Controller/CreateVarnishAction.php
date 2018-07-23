<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /** @var \Snowdog\DevTest\Model\User $user */
    private $user;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        if(isset($_SESSION['login'])) {
            $this->user = $this->userManager->getByLogin($_SESSION['login']);
        }
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $ip = $_POST['ip'];
        $result = $this->varnishManager->create($this->user, $ip);
        $_SESSION['flash'] = $result ? 'Added new varnish cache' : 'Adding varnish cache failed!';

        header('Location: /varnish');
    }
}