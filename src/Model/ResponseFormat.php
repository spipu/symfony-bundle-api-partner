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

namespace Spipu\ApiPartnerBundle\Model;

use Spipu\ApiPartnerBundle\Exception\ApiException;

class ResponseFormat
{
    private array $allowedTypes = [
        'text' => ['type' => 'application/text', 'binary' => false],
        'csv'  => ['type' => 'application/csv',  'binary' => false],
        'json' => ['type' => 'application/json', 'binary' => false],
        'pdf'  => ['type' => 'application/pdf',  'binary' => true],
        'jpg'  => ['type' => 'image/jpg',        'binary' => true],
    ];

    private string $type;
    private string $contentType;
    private bool $binaryContent;

    /** @var ParameterInterface[] */
    private array $jsonContent = [];

    public function __construct(string $type)
    {
        if (!array_key_exists($type, $this->allowedTypes)) {
            throw new ApiException('This response type is not allowed');
        }

        $this->type = $type;
        $this->contentType = $this->allowedTypes[$type]['type'];
        $this->binaryContent = $this->allowedTypes[$type]['binary'];
    }

    public function setJsonContent(array $parameters): self
    {
        if ($this->type !== 'json') {
            throw new ApiException('Only JSON response can have a json content');
        }

        foreach ($parameters as $parameter) {
            if (!($parameter instanceof ParameterInterface)) {
                throw new ApiException('You must provide ParameterInterface');
            }
        }

        $this->jsonContent = $parameters;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function isBinaryContent(): bool
    {
        return $this->binaryContent;
    }

    public function getJsonContent(): array
    {
        return $this->jsonContent;
    }
}
