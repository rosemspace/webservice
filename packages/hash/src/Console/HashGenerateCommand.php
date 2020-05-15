<?php

namespace Rosem\Component\Hash\Console;

use Rosem\Component\Hash\ArgonHasher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HashGenerateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('hash:generate')
            ->setDescription('Generate a hash')
            ->addArgument(
                'string',
                InputOption::VALUE_REQUIRED,
                'How many bytes of entropy do you want in your key? (defaults to 32 bytes or 256 bits)',
            )
//            ->addOption(
//                'format',
//                'f',
//                InputOption::VALUE_OPTIONAL,
//                'What format would do you want your key provided? (hex or base64, defaults to base64)',
//                'base64'
//            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getArgument('string');
//        $format = $input->getOption('format');
//
//        $formatters = [
//            'base64' => 'base64_encode',
//            'hex' => 'bin2hex',
//        ];
//
//        if (!isset($formatters[$format])) {
//            throw new \InvalidArgumentException('Unrecognized format: ' . $format);
//        }

        $hasher = new ArgonHasher();
        $output->writeln($hasher->hash($string));

        return 0;
    }
}
