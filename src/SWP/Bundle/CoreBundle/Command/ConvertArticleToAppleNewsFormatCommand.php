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
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use SWP\Bundle\CoreBundle\AppleNews\Api\AppleNewsApi;
use SWP\Bundle\CoreBundle\AppleNews\Api\ClientFactory;
use SWP\Bundle\CoreBundle\AppleNews\Converter\ArticleToAppleNewsFormatConverter;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMigrationMessage;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use function explode;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ConvertArticleToAppleNewsFormatCommand extends Command
{
    protected static $defaultName = 'swp:apple:convert';

    private $converter;

    private $articleRepository;

    public function __construct(
        ArticleToAppleNewsFormatConverter $converter,
        ArticleRepositoryInterface $articleRepository
    ) {
        parent::__construct();

        $this->converter = $converter;
        $this->articleRepository = $articleRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds packages by term and process them.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $article = $this->articleRepository->findOneBy(['id' => 9]);
//        $article = new Article();
//        $article->setId('1');
        $article->setLocale('en');
        $article->setTitle('Three More US Bishops Announce Return of Public Masses');
        $article->setPublishedAt(new \DateTime());
        $article->setCreatedAt(new \DateTime());
        $article->setLead('The decisions come one week after Bishop Peter Baldacchino of Las Cruces, New Mexico, became the first bishop in the United States to lift the ban on public celebration of Mass in his diocese.');
        //$article->setBody('<h2>heading 2</h2><p>body1</p>\n<!-- EMBED START Image {id: \"editor_0\"} -->\n<figure>\n <img src=\"https://superdesk-pro-a.s3-eu-west-1.amazonaws.com/sd-sp/20190912110948/741aaea3ba5f40842f14eea483a0d486840b28d1e728fb5df978194a1117abde.jpg\" alt=\"Stockholm\" />\n <figcaption>Stockholm</figcaption>\n</figure>\n<!-- EMBED END Image {id: \"editor_0\"} -->\n<p><br/></p>\n<p>body2</p>\n<p>body3</p><p>U Splitu je nakon duge i te\u0161ke bolesti umro legendarni hrvatski majstor borila\u010dkih vje\u0161tina Branko Cikati\u0107 u 65. godini.</p>\n<p>Cikati\u0107 je ro\u0111en 3. oktobra 1954. u Splitu, a bio je istaknuti profesionalac u kik-boksu i tajlandskom boksu.&nbsp;</p>\n<p>U amaterskom kik-boksu bio je svjetski prvak 1981. godine, a evropski prvak 1979., 1980., 1981., 1982. i 1983. godine. Nastupaju\u0107i kao profesionalac, bio je svjetski prvak 1987., 1988., 1989. i 1992. godine, a evropski prvak 1986. godine, dok je 20. aprila 1993. u Japanu postao pobjednik prvog K-1 Grand pri turnira.</p>\n<p>Hrvatski Tigar, kako su ga zvali u domovinu, ukupno je 13 puta bio svjetski prvak u raznim disciplinama.</p>\n<p>Bio je \u010dest gost na revijama u nekada\u0161njem Titogradu, za vrijeme SFRJ, a njegova borba sa Samirom Usenagi\u0107em u Beogradu 1990. godine je jedna od antologijskih u istoriji kik-boksa.</p>\n<div class=\\"embed - block\\"><iframe width=\\"100 % \\" height=\\"435\\" src=\\"https://www.youtube.com/embed/A4ahFVod6Ow\\" frameborder=\\"0\\" allow=\\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\\" allowfullscreen></iframe></div>');

        $article->setBody('<p>HELENA, Montana \u2014 Three further Catholic dioceses have announced they will resume public celebration of Mass, subject to the requirements of public health orders and social distancing.</p>\n<p>The Montana dioceses of Great Falls-Billings and Helena both announced the re-openings on Thursday, April 23, one day after the bishop of Lubbock, Texas told his priests to prepare to restore access to Communion for Catholics in the diocese.</p>\n<p>The public celebration of Mass has been prohibited in dioceses across the United States for over a month as part of efforts to halt the spread of the novel coronavirus pandemic.</p>\n<p>The decisions come one week after Bishop Peter Baldacchino of Las Cruces, New Mexico, became the first bishop in the United States to lift the ban on public celebration of Mass in his diocese.</p>\n<p>In a short video posted on the diocesan website Thursday, Bishop Austin Vetter of Helena noted that the governor of Montana had initiated \u201cphase one\u201d of a readjustment of closure orders. \u201cThat does allow us to begin gathering for Mass,\u201d the bishop said.</p>\n<p>Governor Steve Bullock\u2019s phased reopening plan permits limited reopening for some retail venues and public gathering places, including bars and casinos.</p>\n<p>\u201cBeginning on Sunday, those parishes that are able to comply with all that is necessary in phase one for a gathering are able to celebrate Mass,\u201d said Bishop Vetter.</p>\n<p>The bishop added that it was not possible to guarantee that every church in the diocese would be able to open this weekend, owing to the limitations of space in some places and of sourcing necessary cleaning materials to comply with state regulation in others.</p>\n<p>\u201cIt is so important that you understand that not all parishes will be able to [reopen immediately]. Not because of lack of effort or desire,\u201d he said. \u201cI ask all of you good people of God to be patient with us. To be patient with us and with each other as we start phase one, to see how this goes.\u201d</p>\n<p>Noting that on some occasions more Catholics would want to attend Mass than it will be possible to accommodate in compliance with social distancing, Bishop Vetter said \u201cIt\u2019s so important that you realize that the Sunday obligation is still suspended for you. It is so important, if you are vulnerable, to stay at home \u2013 if you are elderly, if you are [just] not comfortable yet, don\u2019t come. Come only when you are ready.\u201d</p>\n<p>Bishop Vetter also said that parents with small children who would find it harder to observe social distancing may find it easier to remain home, or attend Mass as they are able individually, \u201cat least until we can get a rhythm going and become more comfortable with how this is going to work.\u201d</p>\n<p>Mass from the Helena cathedral will continue to be streamed live, he said, but would now be moved to the main altar since there will be a congregation.</p>\n<p>In a letter posted on the Billings-Great Falls diocesan Facebook page Thursday evening, Bishop Michael Warfel announced that he was lifting the ban on the public celebration of all the sacraments.</p>\n<p>\u201cPublic celebrations of the Sacraments are permitted as long as adequate spacing and social distancing are managed and maintained,\u201d he wrote.</p>\n<p>In addition to Mass, the new directive also covers confirmations and first Communions, which are to be scheduled at the parish pastor\u2019s determination, and baptisms, which are to be limited to immediate family and godparents.</p>\n<p>\u201cWeddings may be celebrated with the limitations stated above,\u201d the letter said, and made similar provision for funerals.</p>\n<p>Priests were instructed to consult with county health departments about precautions when administering the sacrament of anointing of the sick to patients with COVID-19.</p>\n<p>\u201cAll priests are encouraged to provide reasonable and prudent measures to ensure everyone\u2019s safety, including their own,\u201d Bishop Warfel said. \u201cEveryone is encouraged to continue to practice good hygiene. People who feel sick should remain at home, as should vulnerable and at risk-populations.\u201d</p>\n<p>In a video posted on YouTube on April 22, Bishop Robert Coerver of Lubbock, Texas, said that, following new guidelines from the state Attorney General, it was now possible for churches to provide for the distribution of Communion through drive-up services.&nbsp;</p>\n<p>The video was accompanied by a letter on the diocesan website.</p>\n<p>\"Therefore,\" Bishop Coerver said, \"I am asking that our parishes make preparations, as soon as possible, that Communion be made available to people at the conclusion of live stream Masses or at the conclusion of Masses which might be offered outdoors.\"</p>\n<p>In his own provisions, issued last week for Las Cruces, Bishop Baldacchino emphasized his own preference for outdoor Masses, which could accommodate larger numbers of the faithful in a safe way \u2013 either in spaced, parked cars, or elsewhere on parish property.</p>\n<p>\u201cWe have to be creative, we have to respond to the times and the needs of the people,\u201d Baldacchino told CNA. \u201cI was very inspired by our Holy Father, Pope Francis. He spoke about how drastic measures are not always good. He opened the churches of Rome \u2013 in a safe way, of course \u2013 and warned us that we must remain very close to the Lord\u2019s flock at this time. We cannot wall ourselves off.\u201d</p>\n<p>\"Of course,\" Bishop Coerver said, parishes could only hold outdoor Masses \"observing social distancing guidelines.\"</p>\n<p>\"The best prevention of the spread of the virus continues to be staying at home,\" he cautioned. \"Those over 60 years of age, or those with pre-existing medical conditions which make them more vulnerable to the effects of the virus should not attend church services at this time.\"</p>\n<p>The bishop reiterated that the suspension of the Sunday obligation remained in effect.</p>\n<p>All attendees at an outdoor Mass in Lubbock must wear masks, the bishop emphasized, and he said he would be providing the clergy of the diocese with \"very specific instructions\" on the distribution of Communion.&nbsp;</p>\n<p>\"We need to continue being extremely cautious about the spread of the virus,\" Bishop Coerver said. \"I have consistently followed the directives of the civil authorities and will continue to do so, even if I might personally disagree with some of the aspects of reopening which they might be implementing.\"</p>\n<p>When he became the first bishop to reinstitute the public celebration of Mass during the coronavirus pandemic, Bishop Baldacchino noted that many civil jurisdictions, including the state of New Mexico, had prioritized liquor stores and marijuana dispensaries as \u201cessential services\u201d ahead of churches, calling the priorities \"totally upside down.\"</p>\n<p>\u201cI hope that this might be a glimmer of Easter hope for all of us,\u201d Bishop Coerver said.</p>');

        $json = $this->converter->convert($article);
//        dump($json);die;
//$json = '{
//  "version": "1.0",
//  "identifier": "Apple_Demo",
//  "title": "Simple with Headline above Header Image",
//  "language": "en",
//  "layout": {
//    "columns": 7,
//    "width": 1024,
//    "margin": 70,
//    "gutter": 40
//  },
//  "subtitle": "Non occidere quae cumque vi ventia",
//  "metadata": {
//    "excerpt": "Simple with Headline above Header Image",
//    "thumbnailURL": "https://developer.apple.com/news-publisher/download/Apple-News-Example-Articles/images/Iceland01.jpg"
//  },
//  "documentStyle": {
//    "backgroundColor": "#f6f6f6"
//  },
//  "components": [
//    {
//      "role": "title",
//      "layout": "titleLayout",
//      "text": "Headline Above Image",
//      "textStyle": "titleStyle"
//    },
//    {
//      "role": "intro",
//      "layout": "introLayout",
//      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.",
//      "textStyle": "introStyle"
//    },
//    {
//      "role": "header",
//      "layout": "headerImageLayout",
//      "style": {
//        "fill": {
//          "type": "image",
//          "URL": "https://developer.apple.com/news-publisher/download/Apple-News-Example-Articles/images/Iceland01.jpg",
//          "fillMode": "cover",
//          "verticalAlignment": "center"
//        }
//      }
//    },
//    {
//      "role": "author",
//      "layout": "authorLayout",
//      "text": "Byline | Publisher | Date",
//      "textStyle": "authorStyle"
//    },
//    {
//      "role": "body",
//      "text": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\n\nQuis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.\n\nTemporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\n\nNeque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.\n\nNam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\n\nNeque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Sed ut perspiciatis.\n\n",
//      "layout": "bodyLayout",
//      "textStyle": "bodyStyle"
//    }
//  ],
//  "componentTextStyles": {
//    "default-title": {
//      "fontName": "HelveticaNeue-Thin",
//      "fontSize": 36,
//      "textColor": "#2F2F2F",
//      "textAlignment": "center",
//      "lineHeight": 44
//    },
//    "default-subtitle": {
//      "fontName": "HelveticaNeue-Thin",
//      "fontSize": 20,
//      "textColor": "#2F2F2F",
//      "textAlignment": "center",
//      "lineHeight": 24
//    },
//    "titleStyle": {
//      "textAlignment": "left",
//      "fontName": "HelveticaNeue-Bold",
//      "fontSize": 64,
//      "lineHeight": 74,
//      "textColor": "#000"
//    },
//    "introStyle": {
//      "textAlignment": "left",
//      "fontName": "HelveticaNeue-Medium",
//      "fontSize": 24,
//      "textColor": "#000"
//    },
//    "authorStyle": {
//      "textAlignment": "left",
//      "fontName": "HelveticaNeue-Bold",
//      "fontSize": 16,
//      "textColor": "#000"
//    },
//    "bodyStyle": {
//      "textAlignment": "left",
//      "fontName": "Georgia",
//      "fontSize": 18,
//      "lineHeight": 26,
//      "textColor": "#000"
//    }
//  },
//  "componentLayouts": {
//    "headerImageLayout": {
//      "columnStart": 0,
//      "columnSpan": 7,
//      "ignoreDocumentMargin": true,
//      "minimumHeight": "40vh",
//      "margin": {
//        "top": 15,
//        "bottom": 15
//      }
//    },
//    "titleLayout": {
//      "columnStart": 0,
//      "columnSpan": 7,
//      "margin": {
//        "top": 50,
//        "bottom": 10
//      }
//    },
//    "introLayout": {
//      "columnStart": 0,
//      "columnSpan": 7,
//      "margin": {
//        "top": 15,
//        "bottom": 15
//      }
//    },
//    "authorLayout": {
//      "columnStart": 0,
//      "columnSpan": 7,
//      "margin": {
//        "top": 15,
//        "bottom": 15
//      }
//    },
//    "bodyLayout": {
//      "columnStart": 0,
//      "columnSpan": 5,
//      "margin": {
//        "top": 15,
//        "bottom": 15
//      }
//    }
//  }
//}';
        $clientFactory = new ClientFactory();
//dump($json);die;
        $appleNewsClient = new AppleNewsApi($clientFactory->create(), '58f6d49e-9f82-4bc6-86f0-ad23ba299ec2', 'QRR/tvK7vkYf3Acan+Vjhg2fk+Q4Of2sTO362V2hZzg=');
        //$art = $appleNewsClient->getArticles('64e72ab4-cc67-46e2-8530-3bf3cc065165');
        $art = $appleNewsClient->createArticle('64e72ab4-cc67-46e2-8530-3bf3cc065165', $json);

        dump($art);die;

    }
}
