<?php

declare(strict_types=1);

namespace Tab\UserInterface\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tab\Application\Command\SellSnack\SellSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;

final class SellSnackCommand extends Command
{
    public function __construct(private readonly CommandBusInterface $commandBus)
    {
        parent::__construct('app:snacks:sell');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Users specified in command will be anonymize.')
            ->addArgument('machineId', InputArgument::REQUIRED)
            ->addArgument('snackId', InputArgument::REQUIRED)
            ->addArgument('position', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $machineId = $input->getArgument('machineId');
        $snackId = $input->getArgument('snackId');
        $position = $input->getArgument('position');

        if (!\is_numeric($machineId) || !\is_numeric($snackId)) {
            $output->writeln('<error>MachineId or SnackId is not numeric</error>');
            $output->writeln("<error>{$machineId}</error>");
            $output->writeln("<error>{$snackId}</error>");
            $output->writeln("<error>{$position}</error>");

            return 1;
        }
        $positionVo = SnackPosition::fromString($position);

        $sellSnack = new SellSnack(
            (int) $machineId,
            (int) $snackId,
            $positionVo->toString(),
        );
        $this->commandBus
            ->handle($sellSnack)
        ;

        $output->writeln('<info>Success</info>');

        return 0;
    }
}
