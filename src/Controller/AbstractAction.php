<?php

namespace Snowdog\DevTest\Controller;

abstract class AbstractAction
{
    protected function forbidden()
    {
        return http_response_code(403);
    }
}
