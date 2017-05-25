<?php

namespace CronBundle\Command;

use CronBundle\Cron\CronExpression;
use CronBundle\Task\TaskException;
use CronBundle\Task\Tasks;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommand
 */
class CronCommand extends ContainerAwareCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('cron:start')->setDescription('CronJob service.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws TaskException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!class_exists('CronBundle\Task\Tasks')) {
            throw new TaskException;
        }

        if ($input->getOption('env') === 'prod') {
            $output = new NullOutput();
        }

        $tasks = new Tasks;
        $tasks->setContainer($this->getContainer());

        foreach($tasks->getTasks() as $cronExpression => $cronTask) {
            if (is_callable($cronTask) && CronExpression::factory($cronExpression)->isDue()) {
                $cronTask($input, $output);
            }
        }
    }
}