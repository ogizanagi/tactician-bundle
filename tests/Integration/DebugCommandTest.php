<?php
declare(strict_types=1);

namespace League\Tactician\Bundle\Tests\Integration;

use League\Tactician\Bundle\Tests\Fake\FakeCommand;
use League\Tactician\Bundle\Tests\Fake\OtherFakeCommand;
use League\Tactician\Bundle\Tests\Fake\SomeHandler;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @runTestsInSeparateProcesses
 */
final class DebugCommandTest extends IntegrationTest
{
    public function test_report()
    {
        $this->givenConfig('tactician', <<<'EOF'
commandbus:
    default:
        middleware:
            - tactician.middleware.command_handler
EOF
        );

        $this->registerService('a.handler', SomeHandler::class, [['name' => 'tactician.handler', 'command' => FakeCommand::class]]);
        $this->registerService('b.handler', SomeHandler::class, [['name' => 'tactician.handler', 'command' => OtherFakeCommand::class]]);

        $this->assertEquals(
            "what we expect here",
            $this->runCommand()->getDisplay()
        );
    }

    /**
     * @return CommandTester
     */
    private function runCommand(): CommandTester
    {
        $application = new Application(static::$kernel);

        $command = $application->find('tactician:debug');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName()
        ));
        return $commandTester;
    }
}