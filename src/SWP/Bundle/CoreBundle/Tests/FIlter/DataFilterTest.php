<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Fragment;

use PHPUnit\Framework\TestCase;
use SWP\Bundle\CoreBundle\Filter\DataFilter;
use SWP\Bundle\CoreBundle\Filter\Exception\FilterException;
use SWP\Bundle\CoreBundle\Filter\Exception\KeyNotExistsException;
use SWP\Bundle\CoreBundle\Filter\Exception\NotEqualDataException;

final class DataFilterTest extends TestCase
{
    private $dataFilter;

    public function setUp()
    {
        $this->dataFilter = new DataFilter();
    }

    public function testFiltering()
    {
        $data = \json_decode('{"subject":[{"code":"02002001","scheme":"test","name":"lawyer"}]}', true);
        $result = false;

        try {
            $this->dataFilter->loadData($data);
            $this->dataFilter->contains('subject');
            $this->dataFilter->containsItem('code', '02002001');
            $this->dataFilter->containsItem('scheme', 'test');
            $result = true;
        } catch (FilterException $e) {
            // ignore exceptions
        }

        self::assertTrue($result);
    }

    public function testKeyNotFound()
    {
        $data = \json_decode('{"subject":[{"code":"02002001","scheme":"test","name":"lawyer"}]}', true);

        self::expectException(KeyNotExistsException::class);
        $this->dataFilter->loadData($data);
        $this->dataFilter->contains('data');
    }

    public function testDataNotEqual()
    {
        $data = \json_decode('{"subject":[{"code":"02002001","scheme":"test","name":"lawyer"}]}', true);

        self::expectException(NotEqualDataException::class);
        $this->dataFilter->loadData($data);
        $this->dataFilter->containsItem('data', 'wrongValue');
    }
}
