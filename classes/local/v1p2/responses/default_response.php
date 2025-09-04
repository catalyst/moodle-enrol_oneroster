<?php

namespace enrol_oneroster\local\v1p2\responses;

use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMajor;

class default_response {
    // Properties
    private statusInfo $imsx_statusInfo;
    private ?array $data = null;
    private ?string $collectionName = null;

    public function __construct(
        statusInfo $imsx_statusInfo,
        ?array $data = null,
        ?string $collectionName = null
    ) {
        $this->imsx_statusInfo = $imsx_statusInfo;
        $this->data = $data;
        $this->collectionName = $collectionName;
    }

    public function getstatusInfo(): statusInfo {
        return $this->imsx_statusInfo;
    }

    public function getData(): ?array {
        return $this->data;
    }

    public function getCollectionName(): ?string {
        return $this->collectionName;
    }

    /**
     * Method that converts the default response object type to an array.
     *
     * @return array The array representation of the default response object type.
     */
    public function toArray(): array {
        $result = [
            'imsx_statusInfo' => $this->imsx_statusInfo->toArray(),
        ];
        if ($this->getData() !== null && $this->getCollectionName() !== null) {
            $result[$this->collectionName] = $this->data;
        }
        return $result;
    }

    /**
     * Encoding the default response array to a JSON string.
     *
     * @return string The JSON string representation of the default response object type.
     */
    public function toJSON(): string {
        return json_encode($this->toArray());
    }

    public static function success(
        ?array $data = null,
        ?string $collectionName = null,
        ?string $description = null
    ): self {
        return new self(
            statusInfo::success($description),
            $data,
            $collectionName
        );
    }

    public static function failure(
        severity $severity,
        codeMinor $codeMinor,
        ?string $description = null
    ) : self {
        return new self(
            statusInfo::failure($severity, $codeMinor, $description),
            null,
            null
        );

    }

    public static function processing(
        ?string $description = null
    ) : self {
        return new self(
            statusInfo::processing($description),
            null,
            null
        );
    }

    public static function unsupported(
        ?string $description = null
    ) : self {
        return new self(
            statusInfo::unsupported($description),
            null,
            null
        );
    }

    public function isInfoValid(): bool {
        if ($this->imsx_statusInfo === null) {
            return false;
        }
        if ($this->data === null || $this->collectionName === null) {
            return false;
        }
        if ($this->imsx_statusInfo->getCodeMajor() === codeMajor::failure && $this->data !== null) {
            return false;
        }
        if ($this->imsx_statusInfo->getCodeMajor() === codeMajor::success &&
        $this->data !== null && $this->collectionName === null) {
            return false;
        }

        return true;

    }




}