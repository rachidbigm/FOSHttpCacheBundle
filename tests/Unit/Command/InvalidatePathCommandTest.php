<?php

/*
 * This file is part of the FOSHttpCacheBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCacheBundle\Tests\Unit\Command;

use FOS\HttpCacheBundle\CacheManager;
use FOS\HttpCacheBundle\Command\InvalidatePathCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InvalidatePathCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testExecuteMissingParameters()
    {
        $invalidator = \Mockery::mock(CacheManager::class);

        $application = new Application();
        $application->add(new InvalidatePathCommand($invalidator));

        $command = $application->find('fos:httpcache:invalidate:path');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
    }

    public function testExecuteParameter()
    {
        $invalidator = \Mockery::mock(CacheManager::class)
            ->shouldReceive('invalidatePath')->once()->with('/my/path')
            ->shouldReceive('invalidatePath')->once()->with('/other/path')
            ->getMock()
        ;

        $application = new Application();
        $application->add(new InvalidatePathCommand($invalidator));

        $command = $application->find('fos:httpcache:invalidate:path');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'paths' => ['/my/path', '/other/path'],
        ]);

        // the only output should be generated by the listener in verbose mode
        $this->assertEquals('', $commandTester->getDisplay());
    }
}
