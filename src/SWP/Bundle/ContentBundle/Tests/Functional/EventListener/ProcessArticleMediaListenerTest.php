<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\EventListener;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\EventListener\ProcessArticleMediaListener;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\Bridge\Model\Item;
use Symfony\Component\Filesystem\Filesystem;

class ProcessArticleMediaListenerTest extends WebTestCase
{
    const TEST_PACKAGE = '{"subject":[{"code":"01001000","name":"archaeology"}],"slugline":"an item with pic","keywords":[],"headline":"risa","body_html":"","place":[],"priority":6,"type":"composite","language":"en","associations":{"main":{"subject":[{"code":"01001000","name":"archaeology"}],"service":[{"code":"a","name":"Australian General News"}],"keywords":[],"headline":"risa","body_html":"<p>here goes the picture<\/p><p><br><\/p>\n<!-- EMBED START Image {id: \"embedded6358005131\"} -->\n<figure><img src=\"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321c.jpg\" alt=\"man and tractor\" srcset=\"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321a.jpg 800w, https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321c.jpg 1079w\" \/><figcaption>man and tractor<\/figcaption><\/figure>\n<!-- EMBED END Image {id: \"embedded6358005131\"} -->","place":[],"priority":6,"type":"composite","language":"en","associations":{"embedded6358005131":{"subject":[{"code":"01001000","name":"archaeology"}],"description_text":"man and tractor","service":[{"code":"a","name":"Australian General News"}],"headline":"man and tractor","place":[],"urgency":3,"guid":"tag:localhost:2016:41cd3de8-de67-4477-a37f-2663120cd237","body_text":"man and tractor","type":"picture","language":"en","pubstatus":"usable","slugline":"man and tractor","versioncreated":"2016-09-05T14:16:32+0000","version":"2","renditions":{"original":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321a.jpg","mimetype":"image\/jpeg","width":1189,"media":"20160905140916\/12345678987654321a.jpg","height":793},"baseImage":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321b.jpg","mimetype":"image\/jpeg","width":1400,"media":"20160905140916\/12345678987654321b.jpg","height":933},"16-9":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321c.jpg","mimetype":"image\/jpeg","width":1079,"media":"20160905140916\/12345678987654321c.jpg","height":720},"4-3":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321e.jpg","mimetype":"image\/jpeg","width":800,"media":"20160905140916\/12345678987654321e.jpg","height":533},"viewImage":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321f.jpg","mimetype":"image\/jpeg","width":640,"media":"20160905140916\/12345678987654321f.jpg","height":426},"thumbnail":{"href":"https:\/\/s3.superdesk.org\/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com\/20160905140916\/12345678987654321g.jpg","mimetype":"image\/jpeg","width":179,"media":"20160905140916\/12345678987654321g.jpg","height":120}},"mimetype":"image\/jpeg","byline":"ADmin","priority":6}},"slugline":"an item with pic","versioncreated":"2016-09-05T14:17:56+0000","version":"2","guid":"urn:newsml:localhost:2016-09-05T14:17:56.183055:5aa029e4-9f1e-4045-aa95-ea3e5ea5aec8","pubstatus":"usable","urgency":3,"byline":"ADmin"}},"service":[{"code":"a","name":"Australian General News"}],"versioncreated":"2016-09-05T14:18:07+0000","version":"2","guid":"urn:newsml:localhost:2016-09-05T14:17:03.984630:4cae6d85-0f1d-4a85-845c-b884f11d6044","pubstatus":"usable","urgency":3,"byline":"ADmin"}';

    /**
     * @var ProcessArticleMediaListener
     */
    protected $listener;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');
        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesMediaData',
            ], 'default'
        );

        $this->listener = new ProcessArticleMediaListener(
            $this->getContainer()->get('swp.object_manager.media'),
            $this->getContainer()->get('swp_content_bundle.manager.media'),
            $this->getContainer()->get('swp.factory.media'),
            $this->getContainer()->get('swp.repository.image')
        );
    }

    /**
     * Test handling items under article.
     */
    public function testHandleMedia()
    {
        $article = $this->getMockBuilder(ArticleInterface::class)->getMock();
        $article->expects($this->any())->method('getSlug')->willReturn('an-item-with-pic');

        $this->assertInstanceOf(ArticleMediaInterface::class, $this->listener->handleMedia($article, 'embedded6358005131', new Item()));
    }

    /**
     * Test if body images src path is replaced with one handled by media controller.
     */
    public function testImagesReplaceInBody()
    {
        $package = $this->getContainer()->get('swp_bridge.transformer.json_to_package')->transform(self::TEST_PACKAGE);
        $article = $this->getContainer()->get('swp_content.transformer.package_to_article')->transform($package);
        $this->getContainer()->get('event_dispatcher')->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package));

        $expected = $text = <<<'EOT'
 <p>here goes the picture</p><p><br></p>
<!-- EMBED START Image {id: "embedded6358005131"} -->
<figure><img src="/media/12345678987654321c.jpeg" data-media-id="embedded6358005131" data-image-id="12345678987654321c"><figcaption>man and tractor</figcaption></figure>
<!-- EMBED END Image {id: "embedded6358005131"} -->
EOT;

        self::assertEquals($expected, $article->getBody());
    }
}
