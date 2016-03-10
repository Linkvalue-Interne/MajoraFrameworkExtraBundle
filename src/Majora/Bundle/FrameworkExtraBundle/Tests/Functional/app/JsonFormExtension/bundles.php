<?php

return [
    new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Majora\Bundle\FrameworkExtraBundle\MajoraFrameworkExtraBundle($this),
    new Majora\Bundle\FrameworkExtraBundle\Tests\Functional\Bundle\TestBundle\TestBundle(),
];
