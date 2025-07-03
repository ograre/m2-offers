<?php

namespace Ograre\Offers\Api\Data;

interface OfferFileProcessorInterface
{
    /**
     * @param string $fileId
     * @return array
     */
    public function saveToTmp(string $fileId): array;

    /**
     * @param string $fileName
     * @return string
     */
    public function moveFileFromTmp(string $fileName): string;

    /**
     * @param string $filename
     * @return bool
     */
    public function tmpFileExists(string $filename): bool;
}
