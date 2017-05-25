<?php

namespace CronBundle\Task;

/**
 * Class TaskException
 */
class TaskException extends \Exception
{
    /**
     * @var string
     */
    protected $message = '
Please implement next class:
<?php
namespace CronBundle\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Task
 */
class Tasks implements ContainerAwareInterface
{
    /**  */@var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getTasks()
    {
        return array(
            // Run every one minute
            \'* * * * *\' => function(InputInterface $input, OutputInterface $output) {
                // do something
            }
        );
    }
}';
}
