<?php

namespace spec\Updater;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Updater');
    }
}
