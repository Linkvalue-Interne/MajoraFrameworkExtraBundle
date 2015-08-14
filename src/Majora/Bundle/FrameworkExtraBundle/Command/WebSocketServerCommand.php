<?php

namespace Majora\Bundle\FrameworkExtraBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Location server running command.
 */
class WebSocketServerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('majora:web_socket_server:run')
            ->setDescription('Web socket server running command.')
            // ->addArgument('name', InputArgument::REQUIRED, 'Tournament name')
            // ->addArgument('nb_rounds', InputArgument::REQUIRED, 'Number of rounds')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // run server
        $this->getContainer()
            ->get('majora.web_socket.server')
            ->run()
        ;
    }
}
