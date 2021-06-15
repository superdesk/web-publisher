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

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\EventListener\ProcessArticleMediaListener;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\Rendition;
use Symfony\Component\Filesystem\Filesystem;

class ProcessArticleMediaListenerTest extends WebTestCase
{
    const TEST_PACKAGE = '{"subject":[{"code":"01001000","name":"archaeology"}],"slugline":"an item with pic","keywords":[],"headline":"risa","body_html":"","place":[],"priority":6,"type":"composite","language":"en","associations":{"main":{"subject":[{"code":"01001000","name":"archaeology"}],"service":[{"code":"a","name":"Australian General News"}],"keywords":[],"headline":"risa","body_html":"<p>here goes the picture</p><p><br></p>\n<!-- EMBED START Image {id: \"embedded6358005131\"} -->\n<figure><img src=\"http://localhost:3000/api/upload/12345678987654321c.jpg/raw\" alt=\"man and tractor\" srcset=\"http://localhost:3000/api/upload/12345678987654321a.jpg/raw 800w, http://localhost:3000/api/upload/12345678987654321c.jpg/raw 1079w\" /><figcaption>man and tractor</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded6358005131\"} -->","place":[],"priority":6,"type":"composite","language":"en","associations":{"embedded6358005131":{"subject":[{"code":"01001000","name":"archaeology"}],"description_text":"man and tractor","service":[{"code":"a","name":"Australian General News"}],"headline":"man and tractor","place":[],"urgency":3,"guid":"tag:localhost:2016:41cd3de8-de67-4477-a37f-2663120cd237","body_text":"man and tractor","type":"picture","language":"en","pubstatus":"usable","slugline":"man and tractor","versioncreated":"2016-09-05T14:16:32+0000","version":"2","renditions":{"original":{"href":"http://localhost:3000/api/upload/12345678987654321a.jpg/raw","mimetype":"image/jpeg","width":1189,"media":"20160905140916/12345678987654321a.jpg","height":793},"baseImage":{"href":"http://localhost:3000/api/upload/12345678987654321b.jpg/raw","mimetype":"image/jpeg","width":1400,"media":"20160905140916/12345678987654321b.jpg","height":933},"16-9":{"href":"http://localhost:3000/api/upload/12345678987654321c.jpg/raw","mimetype":"image/jpeg","width":1079,"media":"20160905140916/12345678987654321c.jpg","height":720},"4-3":{"href":"http://localhost:3000/api/upload/12345678987654321e.jpg/raw","mimetype":"image/jpeg","width":800,"media":"20160905140916/12345678987654321e.jpg","height":533},"viewImage":{"href":"http://localhost:3000/api/upload/12345678987654321f.jpg/raw","mimetype":"image/jpeg","width":640,"media":"20160905140916/12345678987654321f.jpg","height":426},"thumbnail":{"href":"http://localhost:3000/api/upload/12345678987654321g.jpg/raw","mimetype":"image/jpeg","width":179,"media":"20160905140916/12345678987654321g.jpg","height":120}},"mimetype":"image/jpeg","byline":"ADmin","priority":6}},"slugline":"an item with pic","versioncreated":"2016-09-05T14:17:56+0000","version":"2","guid":"urn:newsml:localhost:2016-09-05T14:17:56.183055:5aa029e4-9f1e-4045-aa95-ea3e5ea5aec8","pubstatus":"usable","urgency":3,"byline":"ADmin"}},"service":[{"code":"a","name":"Australian General News"}],"versioncreated":"2016-09-05T14:18:07+0000","version":"2","guid":"urn:newsml:localhost:2016-09-05T14:17:03.984630:4cae6d85-0f1d-4a85-845c-b884f11d6044","pubstatus":"usable","urgency":3,"byline":"ADmin"}';

    const TEST_ITEM = '{"byline": "Holman Romero", "body_html": "<!-- EMBED START Image {id: \"embedded11331114891\"} -->\n<figure><img src=\"http://localhost:3000/api/upload/58512c44c3a5be49f3529d98/raw?_schema=http\" alt=\"Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)\" srcset=\"http://localhost:3000/api/upload/58512c44c3a5be49f3529d98/raw?_schema=http 451w, http://localhost:3000/api/upload/58512c44c3a5be49f3529d95/raw?_schema=http 777w\" /><figcaption>Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded11331114891\"} -->\n<p>It is 8:00 AM, you\u2019re at home having some breakfast before heading to the office. You\u2019re enjoying a good cup of coffee while reading the latest news on your mobile. Have you ever wondered how those pieces of news, photos, videos and other forms of news content get to your screen?<br></p><p>The news is created somewhere, someone (usually a human being, a journalist) writes it down, someone else (also a human being, this time, an editor) edits it and publishes it, then that piece of news goes somewhere and continues being retransmitted multiple times until it reaches your phone. Bang!</p><p>For the above to happen, the news content needs to be expressed in a digital format. These formats are developed with the standards defined by the global standards body of the news media called the <a href=\"https://iptc.org/\" target=\"_blank\">International Press Telecommunications Council (IPTC)</a>, and play a very important role in the news industry. This year we attended their summer meeting in Stockholm, Sweden.</p><p>Back in November of 2015, we became members of IPTC. This group is composed of over 50 companies, organisations and associations from the news industry. Superdesk\u2019s open-source end-to-end news management system uses those IPTC standards to help newsrooms to create, produce, publish and distribute news content.</p><p>Now you get the idea, we simply had to be there to join this wonderful group of people, this time in Stockholm, Sweden, for their summer meeting. Here are our brief takeaways from the meeting:</p><h3>IPTC Working Groups</h3><p>Because IPTC is all about standards for the news industry, a big chunk of time during the summer meeting was well spent on sessions where some of the different working parties and groups presented an update on their latest developments. These working groups included SportsML, RightsML, NewsML-G2, Photo Metadata and Video Metadata.</p><p>During these updates, we learned about SportsML 3, the new version of the standard for interchanging sports data, which features several improvements upon SportsML-G2, including the new SportsJS specification for handling Sports data in JSON format.</p><p>The other working groups also shared the results of their latest development that will translate into new versions to be released in the upcoming months.</p><h3>EXTRA</h3><p>EXTRA (The EXTraction Rules Apparatus) is a new initiative by IPTC to develop a multilingual open-source platform for rules-based classification of news content.</p><p>Wait, what? Let\u2019s try again\u2026 EXTRA will essentially be a system that allows newsrooms to automatically annotate news content with high-quality metadata subjects using a predefined set of rules. The project is part of the first round of the Google Digital News Initiative for supporting innovation in journalism.</p><p>During the meeting in Stockholm, the group discussed the general idea, first steps and how to manage the project while encouraging others to contribute.</p><p>If you are interested, you can read more about EXTRA <a href=\"https://iptc.org/news/iptc-receives-google-grant-develop-extra-multilingual-classification-platform/\" target=\"_blank\">here</a>.</p><p>Sourcefabric has also received a grant from Google DNI to develop a live blogging platform for, mainly, news agencies. You can learn more about that <a href=\"/en/site/news/76/Introducing-Live-Coverage-Ecosystem-funded-by-Google.htm\" target=\"_blank\">in our recent blog post</a>.</p><h3>News in JSON</h3><p>While all the IPTC base standards like NewsML-G2, SportsML-G2, EventsML-G2 and RightsML have been semantically defined using the XML format, the IPTC group acknowledges the importance of the representation of news content and data in JSON, an open-standard format that has become the most common data format used for asynchronous browser/server communication. That\u2019s how back in October 2013, IPTC developed ninjs (News in JSON), a standard to represent news and publishing information in JSON format.</p><p>An interesting discussion took place during the last day of the event around the idea of developing a new standard to provide rich JSON for news content. Essentially, a JSON specification in line with the NewsML-G2 scope, which is not covered by ninjs. Ninjs was originally designed to solve a set of particular problems instead of being an identical transformation of NewsML-G2 into JSON.</p><p>After some deliberation, the group opted for enhancing and improving ninjs without going away from its original design principles. On top of that, the group will consider the idea of developing a new specification to represent editorial planning in JSON: PlanningJS. Sourcefabric volunteered to contribute the first draft for PlanningJS, which will come out of an initial prototype developed for a Planning component in Superdesk.</p><p>Together, ninjs, SportsJS and PlanningJS will definitely find a place in the development of Web APIs, data storage and indexing, mobile applications and more, to handle news content and we are exciting to contribute.</p><h3>Superdesk and IPTC</h3>\n<!-- EMBED START Image {id: \"embedded5366428123\"} -->\n<figure><img src=\"http://localhost:3000/api/upload/58512c10c3a5be49fad39a2d/raw?_schema=http\" alt=\"Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis\" srcset=\"http://localhost:3000/api/upload/58512c10c3a5be49fad39a2d/raw?_schema=http 400w, http://localhost:3000/api/upload/58512c10c3a5be49fad39a29/raw?_schema=http 777w\" /><figcaption>Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded5366428123\"} -->\n<p>We are very glad we were given the opportunity to present Superdesk at the event. IPTC standards have been an inspiration for us when developing Superdesk and we wanted not only to show our work but also to start contributing back. We have written in the past a couple of blog posts about <a href=\"/en/site/news/55/NewsML-G2-and-Superdesk.htm\" target=\"_blank\">the role of NewsML-G2 in Superdesk</a>, <a href=\"/en/site/news/87/Superdesk-content-editor-the-building-blocks-of-structured-journalism.htm\" target=\"_blank\">why Superdesk is a platform for structured journalism</a> and the <a href=\"/en/site/news/92/Why-metadata-is-important-to-Superdesk.htm\" target=\"_blank\">importance of metadata</a>, give those a read if you haven\u2019t.</p><p>We were pleased to announce the future plans for Superdesk, which include the development of an Editorial Planning component based on EventsML and support for IPTC Media Topics. During all these years, we have made some fundamental decisions in the Superdesk project and following the concepts and ideas behind the standards defined by this wonderful group has probably been the wisest one.</p><p>The response to our presentation was absolutely positive and, in a way, a confirmation of the good direction we are heading towards with Superdesk.</p><p>View a copy of the <a title=\"Superdesk IPTC summer meeting\" href=\"/attachment/15/Sourcefabric%20Superdesk%20-%20IPTC%20Summer%20Meeting%202016.pdf?g_download=1\" target=\"_blank\">presentation slides</a>.</p><p>Lastly, we wanted to say \u2018thank you\u201d to IPTC for inviting us to attend this meeting and to the event host, Swedish News Agency, TT and finally, to all the attendees and organisers. We look forward to the next meeting.</p><p>The <a href=\"https://iptc.org/events/\" target=\"_blank\">next IPTC meeting</a> is to be held in Berlin, October 24 - 26, and we will definitely be there again!</p><br clear=\"all\">", "firstcreated": "2016-12-20T11:18:32+0000", "headline": "IPTC\'s summer meeting: our takeaways", "profile": "57d91f4ec3a5bed769c59846", "version": "4", "place": [], "slugline": "IPTC-summer-meeting", "type": "text", "priority": 6, "pubstatus": "usable", "description_html": "<p><span style=\"font-style: normal;\">In November of 2015, Superdesk became a part of the International Press Telecommunications Council (IPTC), the global standards body of the news media. Here are our takeaways from our recent attendance at their summer meeting in Stockholm, Sweden.&nbsp;</span><br></p>", "guid": "urn:newsml:localhost:2016-12-20T11:18:32.710495:ab5ae1be-b7e7-4db9-8c18-fbd3f63d12b2", "service": [{"code": "news", "name": "News"}], "located": "2016", "urgency": 3, "associations": {"embedded5366428123": {"byline": "Ljuba Rankovi\u0107", "pubstatus": "usable", "firstcreated": "2016-12-14T11:24:20+0000", "headline": "IPTC summer meeting", "mimetype": "image/jpeg", "place": [], "renditions": {"600x300": {"poi": {"y": 212, "x": 588}, "href": "http://localhost:3000/api/upload/58512c10c3a5be49fad39a2d/raw?_schema=http", "media": "58512c10c3a5be49fad39a2d", "mimetype": "image/jpeg", "width": 400, "height": 300}, "viewImage": {"poi": {"y": 163, "x": 313}, "href": "http://localhost:3000/api/upload/58512be5c3a5be49fdca1172/raw?_schema=http", "media": "58512be5c3a5be49fdca1172", "mimetype": "image/jpeg", "width": 640, "height": 480}, "thumbnail": {"poi": {"y": 40, "x": 78}, "href": "http://localhost:3000/api/upload/58512be5c3a5be49fdca116c/raw?_schema=http", "media": "58512be5c3a5be49fdca116c", "mimetype": "image/jpeg", "width": 160, "height": 120}, "original": {"poi": {"y": 306, "x": 588}, "href": "http://localhost:3000/api/upload/58512be4c3a5be49fdca1168/raw?_schema=http", "media": "58512be4c3a5be49fdca1168", "mimetype": "image/jpeg", "width": 1200, "height": 900}, "baseImage": {"poi": {"y": 357, "x": 686}, "href": "http://localhost:3000/api/upload/58512be5c3a5be49fdca1170/raw?_schema=http", "media": "58512be5c3a5be49fdca1170", "mimetype": "image/jpeg", "width": 1400, "height": 1050}, "777x600": {"poi": {"y": 306, "x": 579}, "href": "http://localhost:3000/api/upload/58512c10c3a5be49fad39a29/raw?_schema=http", "media": "58512c10c3a5be49fad39a29", "mimetype": "image/jpeg", "width": 777, "height": 582}}, "slugline": "iptc-meeting", "type": "picture", "priority": 6, "guid": "tag:localhost:2016:a00b17c4-978e-40ec-84a6-def98937bc93", "body_text": "Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis", "urgency": 3, "version": "4", "versioncreated": "2016-12-14T11:25:04+0000", "language": "en", "description_text": "Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis"}, "embedded11331114891": {"byline": "Ljuba Rankovi\u0107", "pubstatus": "usable", "firstcreated": "2016-12-14T11:24:22+0000", "headline": "Stockholm, Sweden", "mimetype": "image/jpeg", "place": [], "renditions": {"600x300": {"poi": {"y": 318, "x": 624}, "href": "http://localhost:3000/api/upload/58512c44c3a5be49f3529d98/raw?_schema=http", "media": "58512c44c3a5be49f3529d98", "mimetype": "image/jpeg", "width": 451, "height": 300}, "viewImage": {"poi": {"y": 233, "x": 332}, "href": "http://localhost:3000/api/upload/58512be7c3a5be49fdca1184/raw?_schema=http", "media": "58512be7c3a5be49fdca1184", "mimetype": "image/jpeg", "width": 640, "height": 425}, "thumbnail": {"poi": {"y": 66, "x": 93}, "href": "http://localhost:3000/api/upload/58512be6c3a5be49fdca117e/raw?_schema=http", "media": "58512be6c3a5be49fdca117e", "mimetype": "image/jpeg", "width": 180, "height": 120}, "original": {"poi": {"y": 438, "x": 624}, "href": "http://localhost:3000/api/upload/58512be6c3a5be49fdca1178/raw?_schema=http", "media": "58512be6c3a5be49fdca1178", "mimetype": "image/jpeg", "width": 1200, "height": 797}, "baseImage": {"poi": {"y": 510, "x": 728}, "href": "http://localhost:3000/api/upload/58512be7c3a5be49fdca1182/raw?_schema=http", "media": "58512be7c3a5be49fdca1182", "mimetype": "image/jpeg", "width": 1400, "height": 929}, "777x600": {"poi": {"y": 438, "x": 513}, "href": "http://localhost:3000/api/upload/58512c44c3a5be49f3529d95/raw?_schema=http", "media": "58512c44c3a5be49f3529d95", "mimetype": "image/jpeg", "width": 777, "height": 516}}, "slugline": "stockholm", "type": "picture", "priority": 6, "guid": "tag:localhost:2016:385325fb-e175-43af-817f-79f40cd1e5ef", "body_text": "Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)", "urgency": 3, "version": "4", "versioncreated": "2016-12-14T11:25:56+0000", "language": "en", "description_text": "Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)"}}, "versioncreated": "2016-12-20T11:18:55+0000", "language": "en", "description_text": "In November of 2015, Superdesk became a part of the International Press Telecommunications Council (IPTC), the global standards body of the news media. Here are our takeaways from our recent attendance at their summer meeting in Stockholm, Sweden.\u00a0"}';

    /**
     * @var ProcessArticleMediaListener
     */
    protected $listener;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
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
            $this->getContainer()->get('swp.repository.media'),
            $this->getContainer()->get('swp.factory.media'),
            $this->getContainer()->get('swp_content_bundle.processor.article_body')
        );
    }

    /**
     * Test handling items under article.
     */
    public function testHandleMedia()
    {
        $article = $this->getMockBuilder(ArticleInterface::class)->getMock();
        $article->expects($this->any())->method('getSlug')->willReturn('an-item-with-pic');

        $item = new Item();
        $rendition = new Rendition();
        $rendition->setHref('https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20160905140916/12345678987654321a.jpg');
        $rendition->setHeight(793);
        $rendition->setWidth(1189);
        $rendition->setName('original');
        $rendition->setMedia('20160905140916\/12345678987654321a.jpg');
        $rendition->setMimetype('image/jpeg');
        $item->setRenditions(new ArrayCollection([$rendition]));

        $this->assertInstanceOf(ArticleMediaInterface::class, $this->listener->handleMedia($article, 'embedded6358005131', $item));
    }

    /**
     * Test if body images src path is replaced with one handled by media controller.
     */
    public function testImagesReplaceInBody()
    {
        $package = $this->getContainer()->get('swp_bridge.transformer.json_to_package')->transform(self::TEST_PACKAGE);
        $article = $this->getContainer()->get('swp_content.transformer.package_to_article')->transform($package);
        $this->getContainer()->get('event_dispatcher')->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package));

        self::assertEquals('', $article->getBody());
    }

    /**
     * Test if body images src path are replaced with one handled by media controller.
     */
    public function testImagesReplaceInComplexItemBody()
    {
        $item = $this->getContainer()->get('swp_bridge.transformer.json_to_package')->transform(self::TEST_ITEM);
        $article = $this->getContainer()->get('swp_content.transformer.package_to_article')->transform($item);
        $this->getContainer()->get('event_dispatcher')->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $item));

        $embed1 = <<<'EOT'
<!-- EMBED START Image {id: "embedded11331114891"} --> <figure><img src="/uploads/swp/media/58512be6c3a5be49fdca1178.jpg" data-media-id="embedded11331114891" data-image-id="58512be6c3a5be49fdca1178" data-rendition-name="original" width="1200" height="797" loading="lazy" alt="Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)"><figcaption>Stockholm, Sweden | Photo by Peter Adermark (CC BY-NC-ND 2.0)<span>Ljuba Ranković</span></figcaption></figure> <!-- EMBED END Image {id: "embedded11331114891"} -->
EOT;

        $embed2 = <<<'EOT'
<!-- EMBED START Image {id: "embedded5366428123"} --> <figure><img src="/uploads/swp/media/58512be4c3a5be49fdca1168.jpg" data-media-id="embedded5366428123" data-image-id="58512be4c3a5be49fdca1168" data-rendition-name="original" width="1200" height="900" loading="lazy" alt="Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis"><figcaption>Snapshot of the IPTC summer meeting | Photo by Jill Laurinaitis<span>Ljuba Ranković</span></figcaption></figure> <!-- EMBED END Image {id: "embedded5366428123"} -->
EOT;
        self::assertContains($embed1, $article->getBody());
        self::assertContains($embed2, $article->getBody());
    }
}
