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

namespace Spipu\ApiPartnerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;

/**
 * @method ApiLogPartner|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiLogPartner|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiLogPartner[]    findAll()
 * @method ApiLogPartner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiLogPartnerRepository extends ServiceEntityRepository
{
    /**
     * ApiLogEcommerceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiLogPartner::class);
    }
}
