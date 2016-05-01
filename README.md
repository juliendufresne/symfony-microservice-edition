symfony-microservice-edition
============================

Components
----------

### RabbitMQ

[bundle](https://github.com/php-amqplib/RabbitMqBundle) | [configuration](app/config/bundle/old_sound_rabbit_mq.yml)

When an application requires this project to update a resource own by this service, it request it using an AMQP message.
This service will then consume the message an treat it.

The project override `php bin/console rabbitmq:consumer` command (see [ConsumerCommand](src/AppBundle/Command/ConsumerCommand.php)) in order to:
- wait for 5 seconds before quiting if there were not enough message to consume
- catch every errors and log an error message if an exception occurs

#### Setup

1. Update all rabbitmq.* parameters in [parameters.yml.dist](app/config/parameters.yml.dist)
2. Define some consumer in [old_sound_rabbit_mq.yml](app/config/bundle/old_sound_rabbit_mq.yml)

### Dev tools

- [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) - [config file](.php_cs): `./vendor/bin/php-cs-fixer fix -vvv`
- [phpmetrics](https://github.com/phpmetrics/phpmetrics) - [config file](.phpmetrics.yml): `./vendor/bin/phpmetrics --config=.phpmetrics.yml src`
- [phpunit](https://github.com/sebastianbergmann/phpunit) - [config file](phpunit.xml.dist): `./vendor/bin/phpunit`

Remove a component
------------------

Each component is installed using a separate pull request.
If you want to remove a component, just have a look at the pull request and see the diff.
If this is not enough, do not hesitate to post an issue.
