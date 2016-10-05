<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadMediaData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;
    private $defaultTenantPrefix;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->defaultTenantPrefix = $this->getTenantPrefix();

        $filesystem = new Filesystem();
        $filesystem->remove($this->container->getParameter('kernel.cache_dir').'/uploads');

        $this->loadMedia($this->getEnvironment(), $manager);

        $manager->flush();
    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadMedia($env, $manager)
    {
        $articles = [
            'test' => [
                ['mediaId' => '123456789876543210a'],
                ['mediaId' => '123456789876543210b'],
                ['mediaId' => '123456789876543210c'],
                ['mediaId' => '123456789876543210d'],
                ['mediaId' => '123456789876543210e'],
                ['mediaId' => '123456789876543210f'],
                ['mediaId' => '123456789876543210g'],
            ],
        ];

        $mediaManager = $this->container->get('swp_content_bundle.manager.media');
        $fakeImage = __DIR__.'/../../Resources/assets/test_cc_image.jpg';

        if (isset($articles[$env])) {
            foreach ($articles[$env] as $media) {
                $uploadedFile = new UploadedFile($fakeImage, $media['mediaId'], 'image/jpeg', filesize($fakeImage), null, true);
                $mediaManager->handleUploadedFile($uploadedFile, $media['mediaId']);
            }

            $manager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 999;
    }
}
