<?php

namespace AppBundle\Command;

use Exception;
use OldSound\RabbitMqBundle\Command\ConsumerCommand as BaseConsumerCommand;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends BaseConsumerCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setDescription(
            sprintf('Executes a consumer.'.PHP_EOL.'<info>This command is overridden in %s</info>', static::class)
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
        } catch (AMQPTimeoutException $exception) {
            sleep(5);
        } catch (Exception $exception) {
            $this->getContainer()->get('logger')->critical(
                'Consumer failed. Exception of type {exception.class} with message "{exception.message}"',
                [
                    'process_id'        => getmypid(),
                    'exception.class'   => get_class($this),
                    'exception.message' => 'My exception message',
                ]
            );

            return 1;
        }

        return 0;
    }
}
