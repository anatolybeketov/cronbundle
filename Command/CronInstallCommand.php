<?php

namespace CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronInstallCommand
 */
class CronInstallCommand extends ContainerAwareCommand
{

    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('cron:install')->setDescription('CronJob installation.');
    }

    /**
     * @param string $cronOutput
     *
     * @return array
     */
    protected function extractCronTasks($cronOutput)
    {
        $tasks = explode(PHP_EOL, $cronOutput);

        $extractedTasks = array();

        foreach($tasks as $task) {
            $task = trim($task);

            if (empty($task) || 0 === strpos($task, '#')) {
                continue;
            }

            $extractedTasks[] = $task;
        }

        return $extractedTasks;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cronPath = realpath(__DIR__ . '/../../../bin');

        if (!$cronPath) {
            throw new \Exception('Error cron path.');
        }

        $cronPath .= '/console cron:start';
        $outputExec = shell_exec('crontab -l');

        if (false === stripos($outputExec, $cronPath)) {
            // installed
            $tasks = $this->extractCronTasks($outputExec);
            $tasks[] = '* * * * * /usr/bin/php -d memory_limit=2048M ' . $cronPath;
            file_put_contents('/tmp/crontab.txt', implode(PHP_EOL, $tasks) . PHP_EOL);
            exec('crontab /tmp/crontab.txt');

            $output->writeln('<info>Cron installed.</info>');
        } else {
            // already installed
            $output->writeln('<info>Cron already installed.</info>');
        }
    }
}
