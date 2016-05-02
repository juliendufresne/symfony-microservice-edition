<?php

namespace AppBundle\Command;

use Exception;
use OldSound\RabbitMqBundle\Command\ConsumerCommand as BaseConsumerCommand;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsumerCommand extends BaseConsumerCommand
{
    const LOG_MESSAGE_START = '[{command}] {consumer_name}:';

    protected function configure()
    {
        parent::configure();
        $this->setDescription(
            sprintf('Executes a consumer.'.PHP_EOL.'<info>This command is overridden in %s</info>', static::class)
        );
        $this->addOption(
            'sleep-on-failure',
            's',
            InputOption::VALUE_REQUIRED,
            'Allow to wait an amount of seconds before exiting when an error occurred'
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
        $context = [
            'command'       => $this->getName(),
            'consumer_name' => $input->getArgument('name'),
        ];
        try {
            parent::execute($input, $output);
        } catch (AMQPTimeoutException $exception) {
            $this->getContainer()->get('logger')->info(
                sprintf('%s timeout. No more messages to treat', static::LOG_MESSAGE_START),
                $context
            );
        } catch (Exception $exception) {
            $this->handleException($exception, $input, $context);

            return 1;
        }

        return 0;
    }

    /**
     * @param Exception      $exception
     * @param InputInterface $input
     * @param array          $context
     */
    private function handleException(Exception $exception, InputInterface $input, array $context)
    {
        $context['exception.class']   = get_class($exception);
        $context['exception.message'] = $exception->getMessage();
        $this->getContainer()->get('logger')->critical(
            sprintf(
                '%s failed. Exception of type {exception.class} with message "{exception.message}"',
                static::LOG_MESSAGE_START
            ),
            $context
        );
        $sleepOnFailure = (int) $input->getOption('sleep-on-failure');
        if ($sleepOnFailure > 0) {
            sleep($sleepOnFailure);
        }
    }
}
