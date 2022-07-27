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

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

final class ContentPushTest extends WebTestCase
{
    const TEST_ITEM_UPDATE_ORIGIN = '{"body_html": "<p>this is test body</p><p>footer text</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:23:34+0000", "description_text": "test abstract", "byline": "Test Persona", "place": [], "version": "2", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99", "language": "en", "urgency": 3, "slugline": "test item update", "headline": "test headline", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:18:57+0000", "located": "Berlin", "type": "text", "description_html": "<p>test abstract</p>"}';

    const TEST_ITEM_UPDATE_UPDATE_1 = '{"body_html": "<p>this is test body&nbsp;updated</p><p>footer text &nbsp;updated</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:26:08+0000", "description_text": "test abstract\u00a0updated", "byline": "Test Persona", "place": [], "version": "3", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521", "language": "en", "urgency": 3, "slugline": "test item update", "type": "text", "headline": "test headline 2", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:25:35+0000", "evolvedfrom": "urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99", "located": "Berlin", "description_html": "<p>test abstract&nbsp;updated</p>"}';

    // update of TEST_ITEM_UPDATE_UPDATE_1
    const TEST_ITEM_UPDATE_UPDATE_2 = '{"body_html": "<p>this is test body&nbsp;updated 2</p><p>footer text &nbsp;updated 2</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:29:51+0000", "description_text": "test abstract\u00a0updated 2", "byline": "Test Persona", "place": [], "version": "4", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:29:27.222376:5aef400e-ee5c-4110-b929-04bd26e4a757", "language": "en", "urgency": 3, "slugline": "test item update modified", "type": "text", "headline": "test headline updated 2", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:29:27+0000", "evolvedfrom": "urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521", "located": "Berlin", "description_html": "<p>test abstract&nbsp;updated 2</p>"}';

    const TEST_CONTENT = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","pubstatus":"usable","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"ads fsadf sdaf sadf sadf","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

    const TEST_CONTENT_WITH_SOURCE = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","pubstatus":"usable","source": "package_tests_source","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"ads fsadf sdaf sadf sadf","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","source": "package_item_tests_source","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

    const TEST_CONTENT_WITH_MEDIA = '{"slugline": "text item with image", "urgency": 3, "versioncreated": "2016-08-17T17:47:18+0000", "firstcreated":"2016-05-25T10:23:15+0000", "guid": "urn:newsml:localhost:2016-08-17T18:45:49.955085:fd9771ee-a1a7-40d7-8eb6-64c502e5f495", "body_html": "<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam imperdiet diam enim, vehicula venenatis nunc maximus vitae. Suspendisse ligula turpis, dictum vel mi quis, viverra viverra massa. Nulla vitae enim id sapien efficitur interdum vel sed nisl. Morbi pharetra suscipit pulvinar. Phasellus tincidunt tortor at porttitor blandit. Nulla ac nibh ut arcu tristique sagittis. Cras ac tristique odio. Sed dolor risus, pulvinar dapibus tincidunt nec, elementum nec dui. Fusce vel enim vel diam auctor faucibus id quis erat. Duis eget risus orci. Praesent sit amet diam tristique, egestas nulla quis, sagittis arcu. Suspendisse non facilisis tellus, ac scelerisque dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie fringilla dui dapibus pellentesque.</p><p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed imperdiet ante vitae luctus ullamcorper. Nam sit amet rhoncus urna. Integer eget euismod arcu. Pellentesque cursus luctus magna vel porttitor. Nunc fringilla aliquet quam, vel porta enim volutpat eu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam vestibulum auctor purus a consequat.</p>\n<!-- EMBED START Image {id: \"embedded4905430171\"} -->\n<figure><img src=\"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:5000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:5000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->\n<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed sit amet ligula imperdiet, finibus tellus consectetur, condimentum mi. Nam eleifend eleifend elit. Donec sit amet molestie lectus. In auctor ullamcorper tortor non ultrices. Morbi id mattis nisl, a placerat quam. Maecenas sed urna in lorem sodales lobortis. Etiam sodales odio vitae risus cursus blandit. Sed consequat gravida justo nec facilisis. Aenean ac erat luctus, posuere neque nec, blandit lacus. Nulla convallis sem quis tristique dictum. Phasellus porta massa sollicitudin, tincidunt nulla ac, pulvinar quam. Nullam a elit magna. Aenean maximus rhoncus lorem, sodales sagittis leo dictum id. Nulla ut interdum turpis.</p>", "located": "Porto", "language": "en", "version": "2", "priority": 6, "type": "text", "byline": "Pawe\u0142 Miko\u0142ajczuk", "service": [{"name": "Australian General News", "code": "a"}], "associations": {"embedded4905430171": {"renditions": {"16-9": {"height": 720, "mimetype": "image/jpeg", "width": 1079, "media": "1234567890987654321a", "href": "http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}, "pubstatus": "usable", "headline": "Text item with image headline\u00a0", "subject": [{"name": "photography", "code": "01013000"}]}';

    const TEST_CONTENT_WITH_DOWNLOADED_MEDIA = '{"slugline": "text item with image", "urgency": 3, "versioncreated": "2016-08-17T17:47:18+0000", "firstcreated":"2016-05-25T10:23:15+0000", "guid": "urn:newsml:localhost:2016-08-17T18:45:49.955085:fd9771ee-a1a7-40d7-8eb6-64c502e5f495", "body_html": "<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam imperdiet diam enim, vehicula venenatis nunc maximus vitae. Suspendisse ligula turpis, dictum vel mi quis, viverra viverra massa. Nulla vitae enim id sapien efficitur interdum vel sed nisl. Morbi pharetra suscipit pulvinar. Phasellus tincidunt tortor at porttitor blandit. Nulla ac nibh ut arcu tristique sagittis. Cras ac tristique odio. Sed dolor risus, pulvinar dapibus tincidunt nec, elementum nec dui. Fusce vel enim vel diam auctor faucibus id quis erat. Duis eget risus orci. Praesent sit amet diam tristique, egestas nulla quis, sagittis arcu. Suspendisse non facilisis tellus, ac scelerisque dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie fringilla dui dapibus pellentesque.</p><p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed imperdiet ante vitae luctus ullamcorper. Nam sit amet rhoncus urna. Integer eget euismod arcu. Pellentesque cursus luctus magna vel porttitor. Nunc fringilla aliquet quam, vel porta enim volutpat eu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam vestibulum auctor purus a consequat.</p>\n<!-- EMBED START Image {id: \"embedded4905430171\"} -->\n<figure><img src=\"http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:3000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:3000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->\n<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed sit amet ligula imperdiet, finibus tellus consectetur, condimentum mi. Nam eleifend eleifend elit. Donec sit amet molestie lectus. In auctor ullamcorper tortor non ultrices. Morbi id mattis nisl, a placerat quam. Maecenas sed urna in lorem sodales lobortis. Etiam sodales odio vitae risus cursus blandit. Sed consequat gravida justo nec facilisis. Aenean ac erat luctus, posuere neque nec, blandit lacus. Nulla convallis sem quis tristique dictum. Phasellus porta massa sollicitudin, tincidunt nulla ac, pulvinar quam. Nullam a elit magna. Aenean maximus rhoncus lorem, sodales sagittis leo dictum id. Nulla ut interdum turpis.</p>", "located": "Porto", "language": "en", "version": "2", "priority": 6, "type": "text", "byline": "Pawe\u0142 Miko\u0142ajczuk", "service": [{"name": "Australian General News", "code": "a"}], "associations": {"embedded4905430171": {"renditions": {"16-9": {"height": 720, "mimetype": "image/jpeg", "width": 1079, "media": "1234567890987654321a", "href": "http://localhost:3000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:3000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:3000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}, "pubstatus": "usable", "headline": "Text item with image headline\u00a0", "subject": [{"name": "photography", "code": "01013000"}]}';

    const TEST_CONTENT_WITH_MEDIA_MODIFIED = '{"slugline": "text item with image", "urgency": 3, "versioncreated": "2016-08-17T17:47:18+0000", "firstcreated":"2016-05-25T10:23:15+0000", "guid": "urn:newsml:localhost:2016-08-17T18:45:49.955085:fd9771ee-a1a7-40d7-8eb6-64c502e5f495", "body_html": "<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam imperdiet diam enim, vehicula venenatis nunc maximus vitae. Suspendisse ligula turpis, dictum vel mi quis, viverra viverra massa. Nulla vitae enim id sapien efficitur interdum vel sed nisl. Morbi pharetra suscipit pulvinar. Phasellus tincidunt tortor at porttitor blandit. Nulla ac nibh ut arcu tristique sagittis. Cras ac tristique odio. Sed dolor risus, pulvinar dapibus tincidunt nec, elementum nec dui. Fusce vel enim vel diam auctor faucibus id quis erat. Duis eget risus orci. Praesent sit amet diam tristique, egestas nulla quis, sagittis arcu. Suspendisse non facilisis tellus, ac scelerisque dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie fringilla dui dapibus pellentesque.</p><p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed imperdiet ante vitae luctus ullamcorper. Nam sit amet rhoncus urna. Integer eget euismod arcu. Pellentesque cursus luctus magna vel porttitor. Nunc fringilla aliquet quam, vel porta enim volutpat eu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam vestibulum auctor purus a consequat.</p>\n<!-- EMBED START Image {id: \"embedded4905430000\"} -->\n<figure><img src=\"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:5000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:5000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->\n<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed sit amet ligula imperdiet, finibus tellus consectetur, condimentum mi. Nam eleifend eleifend elit. Donec sit amet molestie lectus. In auctor ullamcorper tortor non ultrices. Morbi id mattis nisl, a placerat quam. Maecenas sed urna in lorem sodales lobortis. Etiam sodales odio vitae risus cursus blandit. Sed consequat gravida justo nec facilisis. Aenean ac erat luctus, posuere neque nec, blandit lacus. Nulla convallis sem quis tristique dictum. Phasellus porta massa sollicitudin, tincidunt nulla ac, pulvinar quam. Nullam a elit magna. Aenean maximus rhoncus lorem, sodales sagittis leo dictum id. Nulla ut interdum turpis.</p>", "located": "Porto", "language": "en", "version": "2", "priority": 6, "type": "text", "byline": "Pawe\u0142 Miko\u0142ajczuk", "service": [{"name": "Australian General News", "code": "a"}], "associations": {"embedded4905430000": {"renditions": {"16-10": {"height": 620, "mimetype": "image/jpeg", "width": 1069, "media": "1234567890987654321a", "href": "http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}, "pubstatus": "usable", "headline": "edited Text item with image headline\u00a0", "subject": [{"name": "photography", "code": "01013000"}]}';

    const TEST_FEATUREMEDIA_ITEM = '{"located": "Warsaw", "profile": "583d545634d0c100405d84d2", "version": "3", "type": "text", "slugline": "feature media item", "priority": 6, "description_html": "<p>abstract</p>", "guid": "urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc00", "pubstatus": "usable", "associations": {"embedded9582903151": {"version": "4", "type": "picture", "priority": 6, "renditions": {"viewImage": {"width": 640, "mimetype": "image/jpeg", "poi": {"x": 339, "y": 115}, "media": "20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg", "height": 426, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg"}, "thumbnail": {"width": 180, "mimetype": "image/jpeg", "poi": {"x": 95, "y": 32}, "media": "20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg", "height": 120, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg"}, "baseImage": {"width": 1400, "mimetype": "image/jpeg", "poi": {"x": 742, "y": 251}, "media": "20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg", "height": 933, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg"}, "original": {"width": 2048, "mimetype": "image/jpeg", "poi": {"x": 1085, "y": 368}, "media": "20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg", "height": 1365, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg"}, "600x300": {"width": 450, "mimetype": "image/jpeg", "poi": {"x": 1085, "y": 368}, "media": "20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg", "height": 300, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg"}, "777x600": {"width": 777, "mimetype": "image/jpeg", "poi": {"x": 880, "y": 368}, "media": "20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg", "height": 517, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg"}}, "pubstatus": "usable", "place": [], "firstcreated": "2016-12-06T16:59:49+0000", "mimetype": "image/jpeg", "body_text": "Bell Peppers", "description_text": "Few of a kind", "urgency": 3, "language": "en", "headline": "Bell Peppers", "guid": "tag:localhost:2016:a5199d69-1dce-4572-bb1a-34ed2953ea72", "byline": "Ljub. Z. Rankovi\u0107", "versioncreated": "2016-12-06T17:13:18+0000"}, "featuremedia": {"subject": [{"code": "05004000", "name": "preschool"}], "type": "picture", "usageterms": "indefinite-usage", "priority": 6, "renditions": {"viewImage": {"width": 640, "mimetype": "image/jpeg", "poi": {"x": 384, "y": 183}, "media": "20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg", "height": 426, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg"}, "thumbnail": {"width": 180, "mimetype": "image/jpeg", "poi": {"x": 108, "y": 51}, "media": "20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg", "height": 120, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg"}, "baseImage": {"width": 1400, "mimetype": "image/jpeg", "poi": {"x": 840, "y": 401}, "media": "20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg", "height": 933, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg"}, "original": {"width": 2048, "mimetype": "image/jpeg", "poi": {"x": 1228, "y": 586}, "media": "20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg", "height": 1365, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg"}, "600x300": {"poi": {"x": 573, "y": 371}, "width": 598, "media": "sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg", "height": 300, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg"}, "777x600": {"poi": {"x": 1184, "y": 586}, "width": 668, "media": "sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg", "height": 517, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg"}}, "place": [], "pubstatus": "usable", "slugline": "gradac", "firstcreated": "2017-01-11T14:32:58+0000", "mimetype": "image/jpeg", "service": [{"code": "news", "name": "News"}], "byline": "Ljub. Z. Rankovi\u0107", "urgency": 3, "language": "en", "headline": "Smoke on the water", "versioncreated": "2017-01-11T14:52:05+0000", "description_text": "Smoke on the water on River Gradac\u00a0", "guid": "tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09", "body_text": "Gradac", "version": "5"}}, "place": [], "firstcreated": "2017-02-07T07:46:48+0000", "body_html": "<p>some text and after that we should get image</p>\n<!-- EMBED START Image {id: \"embedded9582903151\"} -->\n<figure><img src=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg\" alt=\"Bell Peppers\" srcset=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg 450w, https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg 777w\" /><figcaption>Few of a kind</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded9582903151\"} -->\n<p>and after image again some text</p><p>footer content</p>", "service": [{"code": "news", "name": "News"}], "description_text": "abstract", "urgency": 3, "language": "en", "headline": "headline", "byline": "ADmin", "versioncreated": "2017-02-07T07:49:48+0000"}';

    const TEST_FEATUREMEDIA_ITEM_CORRECTED = '{"located": "Warsaw", "evolvedfrom": "urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc00", "profile": "583d545634d0c100405d84d2", "version": "5", "type": "text", "slugline": "feature media item", "priority": 6, "description_html": "<p>abstract</p>", "guid": "urn:newsml:localhost:2017-02-07T07:46:48.027116:2cde1d3f-302f-4cf9-a4b9-809d2320cc01", "pubstatus": "usable", "associations": {"embedded9582903151": {"version": "4", "type": "picture", "genre": [{"name": "Article (news)", "code": "Article"}], "priority": 6, "renditions": {"viewImage": {"width": 640, "mimetype": "image/jpeg", "poi": {"x": 339, "y": 115}, "media": "20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg", "height": 426, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg"}, "thumbnail": {"width": 180, "mimetype": "image/jpeg", "poi": {"x": 95, "y": 32}, "media": "20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg", "height": 120, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg"}, "baseImage": {"width": 1400, "mimetype": "image/jpeg", "poi": {"x": 742, "y": 251}, "media": "20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg", "height": 933, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg"}, "original": {"width": 2048, "mimetype": "image/jpeg", "poi": {"x": 1085, "y": 368}, "media": "20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg", "height": 1365, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg"}, "600x300": {"width": 450, "mimetype": "image/jpeg", "poi": {"x": 1085, "y": 368}, "media": "20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg", "height": 300, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg"}, "777x600": {"width": 777, "mimetype": "image/jpeg", "poi": {"x": 880, "y": 368}, "media": "20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg", "height": 517, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg"}}, "pubstatus": "usable", "place": [], "firstcreated": "2016-12-06T16:59:49+0000", "mimetype": "image/jpeg", "body_text": "Bell Peppers", "description_text": "Few of a kind", "urgency": 3, "language": "en", "headline": "Bell Peppers", "guid": "tag:localhost:2016:a5199d69-1dce-4572-bb1a-34ed2953ea72", "byline": "Ljub. Z. Rankovi\u0107", "versioncreated": "2016-12-06T17:13:18+0000"}, "featuremedia": {"subject": [{"code": "05004000", "name": "preschool"}], "type": "picture", "usageterms": "indefinite-usage", "priority": 6, "renditions": {"viewImage": {"width": 640, "mimetype": "image/jpeg", "poi": {"x": 384, "y": 183}, "media": "20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg", "height": 426, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg"}, "thumbnail": {"width": 180, "mimetype": "image/jpeg", "poi": {"x": 108, "y": 51}, "media": "20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg", "height": 120, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg"}, "baseImage": {"width": 1400, "mimetype": "image/jpeg", "poi": {"x": 840, "y": 401}, "media": "20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg", "height": 933, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg"}, "original": {"width": 2048, "mimetype": "image/jpeg", "poi": {"x": 1228, "y": 586}, "media": "20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg", "height": 1365, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg"}, "600x300": {"poi": {"x": 573, "y": 371}, "width": 598, "media": "sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg", "height": 300, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg"}, "777x600": {"poi": {"x": 1184, "y": 586}, "width": 668, "media": "sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg", "height": 517, "href": "https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg"}}, "place": [], "pubstatus": "usable", "slugline": "gradac", "firstcreated": "2017-01-11T14:32:58+0000", "mimetype": "image/jpeg", "service": [{"code": "news", "name": "News"}], "byline": "Ljub. Z. Rankovi\u0107", "urgency": 3, "language": "en", "headline": "Smoke on the water", "versioncreated": "2017-01-11T14:52:05+0000", "description_text": "Smoke on the water on River Gradac corrected", "guid": "tag:localhost:2017:4bea4f26-d5a1-446b-8953-3096c0ad0f09", "body_text": "Gradac corrected", "version": "5"}}, "place": [], "firstcreated": "2017-02-07T07:46:48+0000", "body_html": "<p>some text and after that we should get image</p>\n<!-- EMBED START Image {id: \"embedded9582903151\"} -->\n<figure><img src=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg\" alt=\"Bell Peppers\" srcset=\"https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg 450w, https://s3.superdesk.org/superdesk-test-eu-west-1.s3-eu-west-1.amazonaws.com/20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg 777w\" /><figcaption>Few of a kind</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded9582903151\"} -->\n<p>and after image again some text</p><p>footer content</p>", "service": [{"code": "news", "name": "News"}], "description_text": "abstract", "urgency": 3, "language": "en", "headline": "headline", "byline": "ADmin", "versioncreated": "2017-02-08T07:49:48+0000"}';

    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    const TEST_ITEM_CONTENT_SOURCE = '{"language": "en", "slugline": "abstract-html-test", "source": "tests", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    const TEST_KILLED_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "canceled"}';

    const TEST_KILLED_PACKAGE = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","pubstatus":"canceled","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"ads fsadf sdaf sadf sadf","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

    const TEST_ITEM_CONTENT_CORRECTED = '{"language": "en", "evolvedfrom": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "slugline": "abstract-html-test-corrected", "body_html": "<p>some html body</p>", "versioncreated": "2017-02-23T13:57:28+0000", "firstcreated":"2017-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2017-02-02T11:26:59.404843:7u465de4-0d5c-495a-2u36-3b986def3k81", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test corrected", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    const TEST_ITEM_CONTENT_VALIDATION = '{"language": "en", "slugline": "too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline too long slugline", "body_html": "<p>some html body</p>", "versioncreated": "2017-02-23T13:57:28+0000", "firstcreated":"2017-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "code": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2017-02-02T11:26:59.404843:7u465de4-0d5c-495a-2u36-3b986def3k81", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test corrected", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->runCommand('fos:elastica:reset');
        $this->router = $this->getContainer()->get('router');
    }

    public function testArticleUpdates()
    {
        // submit original item
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_ORIGIN
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());


        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $content = $this->getPushedArticle();

        self::assertEquals(1, $content['id']);
        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);
        self::assertEquals('test headline', $content['title']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99', $content['code']);

        $this->assertThereIsOnlyOneArticle();

        // update origin item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_UPDATE_1
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertThereIsOnlyOneArticle();

        $content1 = $this->getPushedArticle();

        self::assertEquals(1, $content1['id']);
        self::assertEquals('published', $content1['status']);
        self::assertTrue($content1['is_publishable']);
        self::assertEquals('test-item-update', $content1['slug']);
        self::assertEquals('test headline 2', $content1['title']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521', $content1['code']);

        // an update of the first update (i.e. second update of origin item)
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_UPDATE_2
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertThereIsOnlyOneArticle();

        $content = $this->getPushedArticle();

        self::assertEquals(1, $content['id']);
        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);
        self::assertEquals('test headline updated 2', $content['title']);
        self::assertEquals('test-item-update', $content['slug']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:29:27.222376:5aef400e-ee5c-4110-b929-04bd26e4a757', $content['code']);
        self::assertEquals($content1['published_at'], $content['published_at']);
        self::assertEquals($content1['route']['id'], $content['route']['id']);
        $this->assertThereIsOnlyOneArticle();
    }

    private function getPushedArticle()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'test-item-update'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function assertThereIsOnlyOneArticle()
    {
        sleep(1);
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_list_articles')
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testContentPushWithMedia()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321a'],
            [
                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png',  null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321b',],
            [

                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png',  null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321c'],
            [

                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png',  null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(5, $content['media'][0]['renditions']);

        self::assertArraySubset(['id' => 3, 'asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // test resending this same content with media
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(5, $content['media'][0]['renditions']);
        self::assertArraySubset(['asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        // test resending modified content with media
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA_MODIFIED
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(5, $content['media'][0]['renditions']);
        self::assertArraySubset(['asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        self::assertArraySubset(['name' => '16-10'], $content['media'][0]['renditions'][0]);
    }

    public function testContentPushWithDownloadedMedia()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_DOWNLOADED_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(5, $content['media'][0]['renditions']);
        self::assertArraySubset(['id' => 1, 'asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUnpublishContentWithMedia()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321a'],
            [

                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png', null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321b',],
            [
                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png', null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            ['media_id' => '1234567890987654321c'],
            [
                'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.png', 'test_file.png', 'image/png', null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'text-item-with-image']), [
            'status' => 'unpublished',
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('unpublished', $content['status']);

        // test package status
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('unpublished', $content['status']);
        self::assertCount(1, $content['articles']);
    }

    public function testAssigningContentToCollectionRouteWithParentRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'site',
            'type' => 'content',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'news',
            'type' => 'collection',
            'parent' => 1,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
            'content' => 'ads-fsadf-sdaf-sadf-sadf',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testItemWithFeatureMedia()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $images = [
            '20161206161256/ce89694dd867849ef907a2b982c38ec51e8c31c043eb9db6924f6fac56261ab3.jpg',
            '20161206161256/49f799a59ca97c238674bd66aaf57f544fcb69ae41e9f297e8bfa59bc2565c52.jpg',
            '20161206161256/876f2496c559df9a1a2ff37a90e9e14cba7d0f210082181f5d69149504fe7773.jpg',
            '20161206161256/383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg',
            '20161206171212/896bd89c2eaa29bbb8a953787a86615c5a9aaf16b4ad93b4ac7f7af23f0c459c.jpg',
            '20161206171212/2577b421dbaf12be0af28be026e4fc4e5484b42aa745c35f071bb3da56e0aadc.jpg',
            '20170111140132/3e737624ba92da6a54ca113344266ffc779c209df0f62297c0269a324c9b504c.jpg',
            '20170111140132/e6fd5d3ed6cff77e2556fde13bf9f383a33795760c42e693a40d0479794febf4.jpg',
            '20170111140132/828ca0e06e013797aa2f32be119803f37843501c7618ea364b6b393f17e69708.jpg',
            '20170111140132/979ff3c8a001d6cb2a7071eab9be852211853990f8d60e693e38f79e972772ea.jpg',
            'sdsite/20170207070248/206d13a7-42aa-4618-b534-d56468a03e15.jpg',
            'sdsite/20170207070248/49521a16-9b51-4ddd-a01a-59aa4db3e71f.jpg',
        ];

        $client = static::createClient();

        foreach ($images as $mediaId) {
            $client->request(
                'POST',
                $this->router->generate('swp_api_assets_push'),
                ['media_id' => $mediaId],
                [

                    'media' => new UploadedFile(__DIR__.'/Functional/Resources/test_file.jpg', 'test_file.jpg', 'image/jpeg', null, true),
                ]
            );

            $this->assertEquals(201, $client->getResponse()->getStatusCode());
            $this->assertEquals(
                [
                    'media_id' => $mediaId,
                    'URL' => 'http://localhost/uploads/swp/123456/media/'.str_replace('/', '_', $mediaId),
                    'media' => base64_encode(file_get_contents(__DIR__.'/Functional/Resources/test_file.jpg')),
                    'mime_type' => 'image/jpeg',
                    'filemeta' => [],
                ],
                json_decode($client->getResponse()->getContent(), true)
            );
        }

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_FEATUREMEDIA_ITEM
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'articlesTemplateName' => 'article.html.twig',
            'content' => null,
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // test package preview
        $client->request('GET', $this->router->generate('swp_package_preview', ['routeId' => 3, 'id' => 1]));
        self::assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_package_preview', ['routeId' => 3, 'id' => 1, 'auth_token' => base64_encode('test_token:')]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('<figure><img src="/uploads/swp/123456/media/20161206161256_383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg"', $content);
        self::assertStringContainsString('alt="Article loaded from context" src="http://localhost/uploads/swp/123456/media/20161206161256_383592fef7acb9fc4731a24a691285b7bc51477264a5e343d95c74ccf1d85a93.jpg"', $content);

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]),

            [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'feature-media-item'])
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('feature_media', $content);

        // correct item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_FEATUREMEDIA_ITEM_CORRECTED
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'feature-media-item'])
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('feature_media', $content);
    }

    public function testIfArticleHasValues()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]),
            [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('lead', $content);
        self::assertEquals(null, $content['lead']);

        // create article from text item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 2]),
            [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('lead', $content);
        self::assertEquals('some abstract text', $content['lead']);
        self::assertArrayHasKey('keywords', $content);
        self::assertEquals(['name' => 'keyword1', 'slug' => 'keyword1'], $content['keywords'][0]);
        self::assertEquals(['name' => 'keyword2', 'slug' => 'keyword2'], $content['keywords'][1]);
    }

    public function testArticleUnpublishWhenItemKilled()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]),
            [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);

        // kill article
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_KILLED_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // check package status
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('canceled', $content['status']);

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('canceled', $content['status']);
        self::assertFalse($content['is_publishable']);
    }

    public function testUnpublishedArticleUnpublishWhenItemKilled()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);

        // kill article
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_KILLED_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('canceled', $content['status']);
        self::assertFalse($content['is_publishable']);
    }

    public function testArticleCorrection()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // publish package
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $publishedAt = $content['published_at'];

        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);
        self::assertEquals('Abstract html test', $content['title']);
        self::assertEquals('abstract-html-test', $content['slug']);
        self::assertEquals(1, $content['id']);
        self::assertEquals('urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0', $content['code']);

        // correct item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT_CORRECTED
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals($publishedAt, $content['published_at']);
        self::assertEquals('published', $content['status']);
        self::assertTrue($content['is_publishable']);
        self::assertEquals('Abstract html test corrected', $content['title']);
        self::assertEquals('abstract-html-test', $content['slug']);
        self::assertEquals(1, $content['id']);
        self::assertEquals('urn:newsml:localhost:2017-02-02T11:26:59.404843:7u465de4-0d5c-495a-2u36-3b986def3k81', $content['code']);
    }

    public function testIncomingDataWhenSlugNotValid()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT_VALIDATION
        );

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertEquals(0, $crawler->filter('html:contains("Slug cannot be longer than 200 characters")')->count());
    }

    public function testOutputChannelPublish()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'name' => 'Local Wordpress',
            'subdomain' => 'local_wordpress',
            'domainName' => 'localhost',
            'organization' => '123456',
            'outputChannel' => [
                'type' => 'wordpress',
                'config' => [
                    'url' => 'http://localhost:3000',
                    'authorizationKey' => 'Basic YWRtaW46dTJnWiB1QTlpIFVkYXogZnVtMSAxQnNkIHpwV2c=',
                ],
            ],
        ]);
        $externalTenant = \json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'expression' => 'true == true',
            'priority' => 1,
            'configuration' => [
                [
                    'key' => 'destinations',
                    'value' => [
                        [
                            'tenant' => $externalTenant['code'],
                        ],
                    ],
                ],
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $externalClient = static::createClient([], [
            'HTTP_HOST' => 'local_wordpress.localhost',
        ]);
        $externalClient->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'expression' => 'true == true',
            'priority' => 1,
            'configuration' => [
                [
                    'key' => 'published',
                    'value' => true,
                ],
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $externalClient->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_ORIGIN
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $externalClient->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $externalClient->getResponse()->getStatusCode());
        $content = json_decode($externalClient->getResponse()->getContent(), true);
        self::assertEquals('publish', $content['external_article']['status']);
    }

    public function testPackageWithSource()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_SOURCE
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('package_tests_source', $content['source']);
        self::assertEquals('package_item_tests_source', $content['associations'][0]['source']);

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'articles',
            'type' => 'collection',
            'content' => null,
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => 3,
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['name' => 'package_tests_source'], $content['sources'][0]['article_source']);
    }

    public function testLoadingArticleFromChildRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'root',
            'type' => 'collection',
        ]);

        $rootRouteContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'name' => 'child',
            'type' => 'collection',
            'parent' => $rootRouteContent['id'],
        ]);

        $childRouteContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]),
            [
                'destinations' => [
                    [
                        'tenant' => '123abc',
                        'route' => $childRouteContent['id'],
                        'isPublishedFbia' => false,
                        'published' => true,
                    ],
                ],
            ]
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent(), true);
        $client->request('GET', $response['_links']['online']['href']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', str_replace('/child', '', $response['_links']['online']['href']));
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
