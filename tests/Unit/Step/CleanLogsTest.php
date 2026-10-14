<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Step;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Service\LogCleaner;
use Spipu\ApiPartnerBundle\Step\CleanLogs;
use Spipu\ProcessBundle\Entity\Process\ParametersInterface;
use Spipu\ProcessBundle\Service\LoggerInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(CleanLogs::class)]
class CleanLogsTest extends TestCase
{
    public function testDisabled(): void
    {
        $logCleaner = $this->createMock(LogCleaner::class);
        $logCleaner->method('isEnabled')->willReturn(false);
        $logCleaner->expects($this->never())->method('cleanLogs');

        $step = new CleanLogs($logCleaner);

        $this->assertSame(
            0,
            $step->execute($this->createMock(ParametersInterface::class), $this->createMock(LoggerInterface::class))
        );
    }

    public function testEnabled(): void
    {
        $logCleaner = $this->createMock(LogCleaner::class);
        $logCleaner->method('isEnabled')->willReturn(true);
        $logCleaner->method('getKeepDays')->willReturn(180);
        $logCleaner->expects($this->once())->method('cleanLogs')->willReturn(12);

        $step = new CleanLogs($logCleaner);

        $this->assertSame(
            12,
            $step->execute($this->createMock(ParametersInterface::class), $this->createMock(LoggerInterface::class))
        );
    }
}
