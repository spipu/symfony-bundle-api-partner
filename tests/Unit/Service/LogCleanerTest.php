<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Repository\ApiLogPartnerRepository;
use Spipu\ApiPartnerBundle\Service\LogCleaner;
use Spipu\ConfigurationBundle\Tests\SpipuConfigurationMock;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(LogCleaner::class)]
class LogCleanerTest extends TestCase
{
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->getService(true, 180)->isEnabled());
        $this->assertFalse($this->getService(false, 180)->isEnabled());
    }

    public function testKeepDays(): void
    {
        $this->assertSame(180, $this->getService(true, 180)->getKeepDays());
        $this->assertSame(1, $this->getService(true, 0)->getKeepDays());
        $this->assertSame(1, $this->getService(true, -5)->getKeepDays());
    }

    public function testCleanLogs(): void
    {
        $limitDate = null;

        $repository = $this->createMock(ApiLogPartnerRepository::class);
        $repository
            ->expects($this->once())
            ->method('deleteOlderThan')
            ->willReturnCallback(
                function (DateTimeInterface $date) use (&$limitDate): int {
                    $limitDate = $date;
                    return 42;
                }
            );

        $before = new DateTimeImmutable('-180 days');
        $this->assertSame(42, $this->getService(true, 180, $repository)->cleanLogs());
        $after = new DateTimeImmutable('-180 days');

        $this->assertInstanceOf(DateTimeInterface::class, $limitDate);
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $limitDate->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $limitDate->getTimestamp());
    }

    private function getService(
        bool $enabled,
        int $keepDays,
        ?ApiLogPartnerRepository $repository = null
    ): LogCleaner {
        $configurationManager = SpipuConfigurationMock::getManager(
            $this,
            [
                'api.partner.cleanup.logs' => 'boolean',
                'api.partner.cleanup.logs_after' => 'integer',
            ],
            [
                'api.partner.cleanup.logs' => $enabled,
                'api.partner.cleanup.logs_after' => $keepDays,
            ]
        );

        return new LogCleaner(
            $configurationManager,
            $repository ?? $this->createMock(ApiLogPartnerRepository::class)
        );
    }
}
