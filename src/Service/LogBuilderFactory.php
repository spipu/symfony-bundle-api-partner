<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class LogBuilderFactory
{
    private EntityManagerInterface $entityManager;
    private ApiResponseStatus $apiResponseStatus;
    private SensitiveParameterService $sensitiveParameterService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ApiResponseStatus $apiResponseStatus,
        SensitiveParameterService $sensitiveParameterService
    ) {
        $this->entityManager = $entityManager;
        $this->apiResponseStatus = $apiResponseStatus;
        $this->sensitiveParameterService = $sensitiveParameterService;
    }

    public function create(): LogBuilder
    {
        $builder = new LogBuilder(
            $this->entityManager,
            $this->apiResponseStatus,
            $this->sensitiveParameterService
        );
        $builder->init();

        return $builder;
    }
}
