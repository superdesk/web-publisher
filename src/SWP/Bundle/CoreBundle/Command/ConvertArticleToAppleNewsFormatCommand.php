<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\CoreBundle\AppleNews\Converter\ArticleToAppleNewsFormatConverter;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Common\Exception\ArticleNotFoundException;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertArticleToAppleNewsFormatCommand extends Command
{
    protected static $defaultName = 'swp:apple:convert';

    private $converter;

    private $articleRepository;

    private $tenantRepository;

    public function __construct(
        ArticleToAppleNewsFormatConverter $converter,
        ArticleRepositoryInterface $articleRepository,
        TenantRepositoryInterface $tenantRepository
    ) {
        parent::__construct();

        $this->converter = $converter;
        $this->articleRepository = $articleRepository;
        $this->tenantRepository = $tenantRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Converts article to Apple News Format')
            ->addArgument('articleId', InputArgument::REQUIRED, 'Article ID')
            ->addArgument('tenantCode', InputArgument::REQUIRED, 'Tenant code');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $article = $this->articleRepository->findOneBy(['id' => $input->getArgument('articleId')]);

        if (null === $article) {
            throw new ArticleNotFoundException();
        }

        $tenant = $this->tenantRepository->findOneBy(['code' => $input->getArgument('tenantCode')]);

        if (null === $tenant) {
            throw new TenantNotFoundException();
        }

        $json = $this->converter->convert($article, $tenant);

        $output->writeln($json);

        return 0;
    }
}
