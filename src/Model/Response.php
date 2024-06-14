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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response
{
    private int $code;
    private array $headers;
    private string $contentType;
    private string $content;
    private bool $binaryContent = false;
    private ?int $logId = null;
    private ?string $logError = null;

    public function __construct()
    {
        $this->code = SymfonyResponse::HTTP_OK;
        $this->headers = [];
    }

    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContentText(string $value): self
    {
        $this->binaryContent = false;
        $this->contentType = 'application/text';
        $this->content = $value;
        return $this;
    }

    public function setContentCsv(string $value): self
    {
        $this->binaryContent = false;
        $this->contentType = 'application/csv';
        $this->content = $value;
        return $this;
    }

    public function setContentJson(array $value): self
    {
        $this->binaryContent = false;
        $this->contentType = 'application/json';
        $this->content = json_encode($value);
        return $this;
    }

    public function setContentPdf(string $fileName, string $fileContent): self
    {
        $this->binaryContent = true;
        $this->headers['Content-Disposition'] = 'attachment; filename=' . $fileName;
        $this->contentType = 'application/pdf';
        $this->content = $fileContent;
        return $this;
    }

    public function setContentJpg(string $fileName, string $fileContent): self
    {
        $this->binaryContent = true;
        $this->headers['Content-Disposition'] = 'attachment; filename=' . $fileName;
        $this->contentType = 'image/jpg';
        $this->content = $fileContent;

        return $this;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getLogId(): ?int
    {
        return $this->logId;
    }

    public function setLogId(?int $logId): self
    {
        $this->logId = $logId;
        return $this;
    }

    public function getLogError(): ?string
    {
        return $this->logError;
    }

    public function setLogError(?string $logError): self
    {
        $this->logError = $logError;
        return $this;
    }

    public function isBinaryContent(): bool
    {
        return $this->binaryContent;
    }
}
