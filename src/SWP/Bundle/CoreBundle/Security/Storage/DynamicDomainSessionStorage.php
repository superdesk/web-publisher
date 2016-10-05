<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Security\Storage;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class DynamicDomainSessionStorage extends NativeSessionStorage
{
    /**
     * @var string
     */
    protected $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $options['cookie_domain'] = '.'.$this->domain;
        $options['cookie_httponly'] = true;
        $options['name'] = 'SUPERDESKPUBLISHER';

        parent::setOptions($options);
    }
}
