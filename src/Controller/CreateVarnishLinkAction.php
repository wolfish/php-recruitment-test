<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\Website;

class CreateVarnishLinkAction extends AbstractAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var VarnishManager
     */
    private $varnishManager;

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
        if (!$this->user) {
            $this->forbidden();
        }

        if ($_POST['enabled'] === 'true') {
            $result = $this->varnishManager->link(intval($_POST['varnish']), intval($_POST['website']));
        } else {
            $result = $this->varnishManager->unlink(intval($_POST['varnish']), intval($_POST['website']));
        }
        echo json_encode(['result' => $result]);
        return json_encode(['result' => $result]);
    }
}