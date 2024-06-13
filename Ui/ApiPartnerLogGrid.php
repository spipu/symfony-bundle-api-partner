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

namespace Spipu\ApiPartnerBundle\Ui;

use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Form\Options\ApiStatusOptions;
use Spipu\ApiPartnerBundle\Form\Options\PartnerOptions;
use Spipu\ApiPartnerBundle\Service\ApiResponseStatus;
use Spipu\UiBundle\Entity\Grid;
use Spipu\UiBundle\Service\Ui\Definition\GridDefinitionInterface;

class ApiPartnerLogGrid implements GridDefinitionInterface
{
    protected ApiStatusOptions $apiStatusOptions;
    protected PartnerOptions $partnerOptions;
    protected ?Grid\Grid $definition = null;

    public function __construct(
        ApiStatusOptions $apiStatusOptions,
        PartnerOptions $partnerOptions
    ) {
        $this->apiStatusOptions = $apiStatusOptions;
        $this->partnerOptions = $partnerOptions;
    }

    public function getDefinition(): Grid\Grid
    {
        if (!$this->definition) {
            $this->prepareGrid();
        }

        return $this->definition;
    }

    protected function prepareGrid(): void
    {
        $this->definition = (new Grid\Grid('api_log_partner', ApiLogPartner::class))
            ->setPager(
                (new Grid\Pager([10, 20, 50, 100], 20))
            )
            ->setPersonalize(true)
            ->addColumn(
                (new Grid\Column('id', 'spipu.api_partner.log.field.id', 'id', 10))
                    ->setType((new Grid\ColumnType(Grid\ColumnType::TYPE_INTEGER)))
                    ->setFilter((new Grid\ColumnFilter(true))->useRange())
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('partnerId', 'spipu.api_partner.log.field.partner', 'partnerId', 20))
                    ->setType(
                        (new Grid\ColumnType(Grid\ColumnType::TYPE_SELECT))
                            ->setOptions($this->partnerOptions)
                    )
                    ->setFilter((new Grid\ColumnFilter(true, false)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('date', 'spipu.api_partner.log.field.date', 'date', 30))
                    ->setType((new Grid\ColumnType(Grid\ColumnType::TYPE_DATETIME)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('user_ip', 'spipu.api_partner.log.field.user_ip', 'userIp', 40))
                    ->setType((new Grid\ColumnType(Grid\ColumnType::TYPE_TEXT)))
                    ->setFilter((new Grid\ColumnFilter(true, true)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('route_code', 'spipu.api_partner.log.field.route_code', 'routeCode', 50))
                    ->setType((new Grid\ColumnType(Grid\ColumnType::TYPE_TEXT)))
                    ->setFilter((new Grid\ColumnFilter(true, true)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column(
                    'response_status',
                    'spipu.api_partner.log.field.response_status',
                    'responseStatus',
                    60
                ))
                    ->setType(
                        (new Grid\ColumnType(Grid\ColumnType::TYPE_SELECT))
                            ->setOptions($this->apiStatusOptions)
                            ->setTemplateField('@SpipuUi/grid/field/select-color.html.twig')
                    )
                    ->setOptions(
                        [
                            'colors' => [
                                ApiResponseStatus::STATUS_SUCCESS => 'success',
                                ApiResponseStatus::STATUS_ERROR => 'danger',
                            ],
                        ]
                    )
                    ->setFilter((new Grid\ColumnFilter(true, false)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('response_code', 'spipu.api_partner.log.field.response_code', 'responseCode', 70))
                    ->setType((new Grid\ColumnType(Grid\ColumnType::TYPE_INTEGER)))
                    ->setFilter((new Grid\ColumnFilter(true, true)))
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('memory_usage', 'spipu.api_partner.log.field.memory_usage', 'memoryUsage', 80))
                    ->setType(
                        (new Grid\ColumnType(Grid\ColumnType::TYPE_INTEGER))
                            ->setTemplateField('@SpipuUi/grid/field/size.html.twig')
                    )
                    ->setFilter((new Grid\ColumnFilter(true))->useRange())
                    ->useSortable()
            )
            ->addColumn(
                (new Grid\Column('duration', 'spipu.api_partner.log.field.duration', 'duration', 90))
                    ->setType(
                        (new Grid\ColumnType(Grid\ColumnType::TYPE_FLOAT))
                            ->setTemplateField('@SpipuUi/grid/field/number.html.twig')
                    )
                    ->setOptions(
                        [
                            'suffix' => ' s',
                        ]
                    )
                    ->setFilter((new Grid\ColumnFilter(true))->useRange())
                    ->useSortable()
            )
            ->setDefaultSort('id', 'desc')
        ;
    }
}
