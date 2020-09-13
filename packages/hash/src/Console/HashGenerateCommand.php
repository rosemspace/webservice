<?php

declare(strict_types=1);

namespace Rosem\Component\Hash\Console;

use Rosem\Component\Hash\ArgonHasher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{
    InputArgument,
    InputInterface,
};
use Symfony\Component\Console\Output\OutputInterface;

class HashGenerateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('hash:generate')
            ->setDescription('Generate a hash of the value')
            ->addArgument(
                'string',
                InputArgument::REQUIRED,
                'How many bytes of entropy do you want in your key? (defaults to 32 bytes or 256 bits)',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getArgument('string');
        $hasher = new ArgonHasher();
        $output->writeln($hasher->hash($string));

        return 0;
    }
}
