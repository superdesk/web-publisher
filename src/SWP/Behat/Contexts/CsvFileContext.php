<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behatch\Context\BaseContext;
use SplFileObject;

class CsvFileContext extends BaseContext
{
    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @Given the CSV file :path should contain :number rows
     * @Given the CSV file :path should contain :number row
     */
    public function theCsvFileShouldContainElements(string $path, int $number): void
    {
        $file = new SplFileObject($this->projectDir.$path, 'r');

        $file->seek(PHP_INT_MAX);
        $elements = $file->key() + 1;

        if ($elements !== $number) {
            throw new \Exception("The file contains $elements rows but expected $number!");
        }
    }
}
