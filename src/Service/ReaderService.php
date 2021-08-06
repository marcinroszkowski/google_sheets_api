<?php

namespace App\Service;

use App\Decorators\JsonInput;
use App\Decorators\JsonWithoutRedundantData;
use Psr\Log\LoggerInterface;

/**
 * Class GoogleApiService
 * @package App\Service
 */
class ReaderService
{
    const FILE_PATH = 'public/data';
    const FILE_NAME = 'data.json';

    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array|null
     */
    public function read(): ?array
    {
        $filePath = $this->getFilePath();

        try {
            $fileContent = file_get_contents($filePath);
            $fileContentDecoded = $this->filterJson($fileContent);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return null;
        }

        return $fileContentDecoded['data'];
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        return self::FILE_PATH.'/'.self::FILE_NAME;
    }

    /**
     * @param string $fileContent
     * @return array|null
     */
    private function filterJson(string $fileContent): ?array
    {
        try {
            /**
             * Dla uproszczenia założyłem, że string jest JSONem
             */
            $fileContentDecoded = json_decode($fileContent, true);

            $jsonInput = new JsonInput();
            $fileContentDecoded = $jsonInput->filterInput($fileContentDecoded);
            $jsonWithoutRedundantData = new JsonWithoutRedundantData($jsonInput);
            $fileContentDecoded = $jsonWithoutRedundantData->filterInput($fileContentDecoded);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return null;
        }

        return $fileContentDecoded;
    }
}