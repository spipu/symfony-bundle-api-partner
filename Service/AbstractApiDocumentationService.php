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

abstract class AbstractApiDocumentationService
{
    private array $documents = [];

    public function __construct()
    {
        $this->build();
    }

    abstract public function getVersion(): string;

    abstract protected function build(): void;

    protected function addDocument(string $code, string $title, string $template): self
    {
        $this->documents[$code] = [
            'code'     => $code,
            'title'    => $title,
            'template' => $template,
        ];

        return $this;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getDocument(?string $code): ?array
    {
        if ($code === null) {
            return  null;
        }

        if (!array_key_exists($code, $this->documents)) {
            return null;
        }

        return $this->documents[$code];
    }
}
