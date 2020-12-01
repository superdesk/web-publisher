<?php

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

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\FixturesBundle\WebTestCase;

class InlineAdsTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'metadata_articles']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testSplittingArticleBody()
    {
        $body = '<p><a href="urn:newsml:localhost:2020-03-10T14:06:22.897017:e3146f94-924c-43ea-a3f6-5407c3fb17f4" target="_blank">Starke Frauen und ihre Geschichten</a> gibt es im neuen <b>Print-Magazin GRL PWR, das ab dem 11 .03.2020</b> überall im Handel erhältlich ist. Was es da noch zu entdecken gibt? Unter vielem anderen: Der Moment, an dem uns klar wurde, dass <a href="urn:newsml:localhost:2020-03-10T13:55:13.816893:f827c7d4-d e1a-41f9-9e2f-9b6919febce7" target="_blank">Women Empowerment</a> noch lange nicht abgeschlossen ist und das große <a href="urn:newsml:localhost:2020-03-10T16:00:55.892243:921b1dcb-4c3f-45a1-aa63-46eae778dc5a" target="_blank">Feminusmus-ABC.</a> <b>Besorgt euch das Heft</b> im nächsten Kiosk, Späti oder im Supermarkt. Es lohnt sich!</p> <p>Oder du bestellst du das Heft <a href="https://amzn.to/39JISXZ">mit diesem Lin k bei Amazon...</a></p> <!-- EMBED START Image {id: "editor_4"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e6f4a5d3672a6b61d8bbc99.jpeg" data-media-id="editor_4" data-image-id="5e6f4a5d3672a6b61d8bbc99" data-rendition-name="original" width="1599" height="2000" alt="GRL PWR"> <figcaption>So sieht das neue GRL PWR-Magazin der Funke Mediengruppe aus.<span></span></figcaption> </figure> <!-- EMBED END Image {id: " editor_4"} --> <h2>&nbsp;Schubladendenken: Die ist mir viel zu...</h2> <p>…laut? Anstrengend? Hysterisch? Frauen werden <b>viel schneller in Schubladen gesteckt</b> als Männer – besonders, wenn sie in der Öffen tlichkeit stehen. Was bei ihm für Charakter spricht, ist bei ihr schnell Charakterschwäche. Was Markenzeichen sein könnte, gilt oft als Makel.</p> <!-- EMBED START Image {id: "editor_5"} --> <figure> <img src=" /uploads/swp/sxl34v/media/5e67aba9022cfb45a10aab2b.jpeg" data-media-id="editor_5" data-image-id="5e67aba9022cfb45a10aab2b" data-rendition-name="original" width="5792" height="8688" alt="GRL PWR: Brote"> <figcap tion>GRL PWR: Schubladendenken | Foto: Isabell Triemer<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_5"} --> <h2>Schubladendenken raus. GRL PWR rein.</h2> <p>Noch mehr Brotvergleiche gib t es im neuen <b>Print-Magazin GRL PWR, das ab dem 11.03.2020 </b>überall im Handel erhältlich ist.&nbsp;</p> <p>Was es da noch zu entdecken gibt? Das große Feminismus ABC erklärt euch, was Feminismus ist. Und diese starken Frauen wünschen wir uns alle als große Schwestern. <b>Besorgt euch das Heft </b>im nächsten Kiosk, Späti oder im Supermarkt. Es lohnt sich! &nbsp;</p> <p>Oder du bestellst du das Heft <a href="htt ps://amzn.to/39JISXZ" target="_blank">mit diesem Link bei Amazon...&nbsp;</a></p> <p><i>Fotos: Isabell Triemer | Text: Lena Schindler</i></p> <p><i>textliche Aufarbeitung für wmn: Mona Schäffer</i></p> <!-- EMB ED START Image {id: "editor_7"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67af6b3672a6b61d8bb797.jpeg" data-media-id="editor_7" data-image-id="5e67af6b3672a6b61d8bb797" data-rendition-name="original" w idth="1920" height="1080" alt="GRL PWR: Silvie Meis"> <figcaption>Schubladendenken: Silvie Meis soll zu schlicht sein?<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_7"} --> <h2>… zu schl icht: Sylvie Meis&nbsp;</h2> <p>Die sieht ja echt gut aus, die Sylvie, aber ist die nicht ein <b>bisschen einfach gestrickt? </b>So was hört man öfter. Aber sagen wir mal so: Es kommt ja immer drauf an, in welc hem Metier man sich bewegt. Und in Sylvies ist die effektivste Föhntechnik sicher wichtiger als die Quantentheorie.&nbsp;</p> <p>Zu schlicht? Aber wofür eigentlich? Es läuft doch bei ihr wie, äh, geschnitten Br ot. Sie liefert das, was man erwartet – und manchmal noch was obendrauf. Wie die <b>grandiose Werbeüberleitung </b>beim „Let’s Dance“-Ausfall von Franziska Traub: <i>„Franziska erzählt uns gleich von ihrer ganz schlimmen Knieverletzung. Also, liebe Zuschauer, bleiben Sie dran und feiern Sie mit uns!“&nbsp;</i></p> <p>So wenig gehaltvoll <b>wie eine labbrige Scheibe Toast?</b> Ach was, auf so was muss man schließlich auch erst mal kommen!&nbsp;</p> <!-- EMBED START Image {id: "editor_8"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67afaebe633aec67f61bd3.jpeg" data-media-id="editor_8" data-image-id="5e67afaebe633aec67 f61bd3" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Angelina Jolie"> <figcaption>Schubladendenken: Angelina Jolie ist zu kompliziert?<span></span></figcaption> </figure> <!-- EMBED E ND Image {id: "editor_8"} --> <h2>… zu kompliziert: Angelina Jolie&nbsp;</h2> <p>Starke Frauen, die unkonventionelle Wege gehen, gelten schnell als <b>undurchschaubar, und: brezelmäßig kompliziert.</b> Angie re belliert gern gegen die Norm. „Hätte ich in früheren Zeiten gelebt, ich wäre auf dem Scheiterhaufen verbrannt worden“, hat sie mal in einem Essay geschrieben.&nbsp;</p> <p>Sie<b> steht auf beide Geschlechter, < /b>ist UN-Sonderbotschafterin und Gastprofessorin… Da gehen viele lieber in Deckung. Dabei ist sie ähnlich dem Gebäck mit der Salzkruste und dem weichen Innenleben gar nicht so hart, wie es scheinen mag. Sagt s ie jedenfalls über sich selbst.&nbsp;</p> <!-- EMBED START Image {id: "editor_9"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67afe5d6dc81519c49d8b8.jpeg" data-media-id="editor_9" data-image-id="5e67afe5 d6dc81519c49d8b8" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Lena Meier-Landrut"> <figcaption>Schubladendenken: Lena Meier-Landrut ist zu dünn?<span></span></figcaption> </figure> < !-- EMBED END Image {id: "editor_9"} --> <h2>… zu dünn: Lena Meyer-Landrut&nbsp;</h2> <p>So als handle es sich um ein wichtiges gesellschaftliches Thema, dürfen weibliche Körper öffentlich seziert werden. Und w as nicht Standard ist, muss pathologisch sein:<i> „Da ist ja nix dran!“, </i><i><b>„Dünn wie eine Baguettestange!“</b></i> – so die Kommentare zu Lenas zarter Figur.</p> <p>Zugegeben: Im Hinblick auf ihre sehr junge Anhängerschaft und die dort verbreitete Suche nach Vorbildern könnte sie mal was Längeres anziehen. Aber wenn<b> fehlendes Bauchfett Nachrichtenstatus </b>bekommt, macht das die Sache auch nicht besser.</ p> <!-- EMBED START Image {id: "editor_10"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67b00b1355aa275ba9cfbe.jpeg" data-media-id="editor_10" data-image-id="5e67b00b1355aa275ba9cfbe" data-rendition-name ="original" width="1920" height="1080" alt="GRL PWR: Madonna"> <figcaption>Schubladendenken: Madonna ist zu alt?<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_10"} --> <h2>… zu alt: Mado nna&nbsp;</h2> <p>Was haben ein <b>vertrocknetes Brötchen und die Queen of Pop</b> gemeinsam? Eigentlich nichts. Außer dass man beiden altersbedingt gern neue, sozialverträgliche Rollen aufdrücken würde. Dem Br ötchen: Paniermehl oder Entenfutter.&nbsp;</p> <p><b>Madonna: gesetzte Charitydame mit Rolli. </b>Denn egal, ob sie sich im Video vor Justin Timberlake auszieht oder mit jemandem im Alter ihrer Kinder rumknutsc ht: Immer gibt’s große Empörung. So verhält man sich nicht, nicht mit 61!&nbsp;</p> <p>Darum lieben die Paparazzi es, ihre natürlich gealterten Hände zu fotografieren – quasi zur Beweisführung: Guck mal,<b> du wirst nämlich doch alt! </b>Das ist mindestens so hämisch wie einer Frau zu sagen, sie sähe <i>„für ihr Alter noch gut aus“!</i></p> <!-- EMBED START Image {id: "editor_11"} --> <figure> <img src="/uploads/swp/ sxl34v/media/5e67b038022cfb45a10aab93.jpeg" data-media-id="editor_11" data-image-id="5e67b038022cfb45a10aab93" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Ina Müller"> <figcaption>Sc hubladendenken: Ina Müller ist zu laut?<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_11"} --> <h2>… zu laut: Ina Müller&nbsp;</h2> <p>Beherrscht und angepasst zu sein wird Mädchen meist lange vorm Schuleintritt eingetrichtert. Sind sie stattdessen auch mal so<b> unüberhörbar wie der Sound eines krossen Knäckebrots,</b> werden sie gleich darüber definiert.&nbsp;</p> <p>Wie Ina Müller: <i>„Ich spiele so schöne Konzerte, ich brülle ja nicht die ganze Zeit auf der Bühne herum. Wenn dann das einzige Prädikat ,Kodderschnauze‘ ist, dann ist das schon hart. Muss man sich </i><i><b>als Frau mit über 50 Jahr en immer noch so betiteln lassen?“</b></i> Wir finden: auf gar keinen Fall!</p> <!-- EMBED START Image {id: "editor_12"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67b0a1022cfb45a10aabab.jpeg" data-medi a-id="editor_12" data-image-id="5e67b0a1022cfb45a10aabab" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Katja Riemann"> <figcaption>Schubladendenken: Katja Riemann<span></span></figcap tion> </figure> <!-- EMBED END Image {id: "editor_12"} --> <h2>… zu dünnhäutig: Katja Riemann&nbsp;</h2> <p>„Zickig“, „arrogant und unsympathisch“, „toxisch“, „mentale Probleme“. So lauten die Kommentare, wenn Katja Riemann zum TV-Interview geladen wird. Denn sie gibt sich absolut <b>keine Mühe, zu gefallen oder ihre Ablehnung zu verstecken, </b>wenn ihr das Gegenüber nicht passt.&nbsp;</p> <p><i>„Es waren viele Jahr e Arbeit, über meine Angst vor Menschen hinwegzukommen“,</i> erklärt sie sich heute selbst. Sie fühlt sich dann schnell in die Enge gedrängt,<b> fällt auseinander – wie ein Croissant, </b>das einem den ganzen B oden vollbröselt.&nbsp;</p> <p>Sogar Ina Müller hatte die Hosen voll, als sie die Schauspielerin zu „Inas Nacht“ in die Seemannskneipe einlud – bekam für ihre (ziemlich intimen!) Fragen aber <b>viel Lachen und eine dicke Umarmung.</b>&nbsp;</p> <!-- EMBED START Image {id: "editor_13"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67b0ca7ad429ac326f2c71.jpeg" data-media-id="editor_13" data-image-id="5e67b0ca7ad42 9ac326f2c71" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Angela Merkel"> <figcaption>Schubladendenken: Angela Merkel<span></span></figcaption> </figure> <!-- EMBED END Image {id: "ed itor_13"} --> <h2>… zu mächtig: Angela Merkel&nbsp;</h2> <p>Achtmal in Folge ist die Meisterin des geschmeidigen Pragmatismus vom US-Magazin<i> Forbes</i> zur mächtigsten Frau der Welt gewählt worden! Doch eine Frau, die <b>wenig Härte ausstrahlt, sich aber trotzdem klar durchsetzt</b>, wenn es sein muss – das ist nicht so leicht zu verdauen für viele Männer.&nbsp;</p> <p>Wie unbehaglich ihre starke Position (im Bäck ereibusiness würde sie <b>locker ein ganzes Brotregal für sich einnehmen!) </b>für viele sein muss, zeigten die geifernden Reaktionen, als sie bei Staatsempfängen zitterte und die Merkel-Raute danach öfter durc h verschränkte Arme ersetzte – so als wollte sie sich selbst festhalten.&nbsp;</p> <p>Wenn die <b>Mächtigen dieser Welt sich menschlich und verletzlich</b> zeigen, dann ist das für viele ungeheuer erleichternd – und bei einer so einflussreichen Frau ganz besonders.&nbsp;</p> <!-- EMBED START Image {id: "editor_14"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67b11a3672a6b61d8bb7b1.jpeg" data-media-id="editor_1 4" data-image-id="5e67b11a3672a6b61d8bb7b1" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Heidi Klum"> <figcaption>Schubladendenken: Heidi Kllum ist zu flach?<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_14"} --> <h2>… zu flach: Heidi Klum&nbsp;</h2> <p>Es ist kontrovers: Wenn Frauen schön sind, möchte man, bitte, auf keinen Fall, dass sie auch noch eine Professur in Philosophie haben! Treffen dann aber<b> Schönheit und ein eher unkomplizierter Blick auf die Dinge</b> tatsächlich mal zusammen, ist es auch wieder nicht recht.&nbsp;</p> <p><i>„Heidi haut immer die gleichen p latten Phrasen raus!“,</i> heißt es dann. Zum Beispiel?<i> „Wenn du top Dollar haben willst, musst du auch top aussehen“, „Du bist wunderschön, aber das allein reicht nicht aus.“</i> Na ja, bei <b>so schlichten Wahrheiten</b> kann zumindest keiner sagen, er hätte irgendwas falsch verstanden. Darüber, dass <a href="urn:newsml:localhost:2020-02-18T17:49:33.134224:5f8a3d1b-50a1-4643-8328-1469840069ac" target="_blank">He idis „GNTM“</a> natürlich gar nicht geht, müssen wir nicht reden. Wir sind defintiv #notheidisgirl, aber das ist eine andere Geschichte …</p> <p>Warum wir <a href="urn:newsml:localhost:2019-11-28T12:14:02.10951 7:6cfb6751-1b56-45e1-99c0-c6ef8430f12a" target="_blank">TrashTV so sehr lieben </a>und einfach nicht davon loskommen, liest du hier.</p> <!-- EMBED START Image {id: "editor_17"} --> <figure> <img src="/uploads/ swp/sxl34v/media/5e67b2ae01161d7a92769c44.jpeg" data-media-id="editor_17" data-image-id="5e67b2ae01161d7a92769c44" data-rendition-name="original" width="1920" height="1080" alt="GRL PWR: Helene Fischer"> <figca ption>Schubladendenken: Helene Fischer ist zu perfekt?<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_17"} --> <h2>… zu perfekt: Helene Fischer&nbsp;</h2> <p>Es heißt, ihr einziger Makel sei der, dass bei ihr <b>einfach alles stimmt: wie bei einem feinen Teilchen aus der Confiserie. </b>Als sie mal wegen Krankheit einen Auftritt absagen musste, wurden gleich Umfragen gestartet, ob sie dadurch n un Sympathiepunkte gesammelt hätte, menschlicher, echter wirkt. Vielen ist sie <b>zu perfekt, zu glatt, zu steril. </b>Und sicher auch zu erfolgreich …</p> <!-- EMBED START Image {id: "editor_18"} --> <figure> <img src="/uploads/swp/sxl34v/media/5e67acee3672a6b61d8bb755.jpeg" data-media-id="editor_18" data-image-id="5e67acee3672a6b61d8bb755" data-rendition-name="original" width="5792" height="8688" alt="GRL PWR: Schu bladendenken für Frauen"> <figcaption>GRL PWR: Schubladendenken für Frauen ist in Deutschland noch immer sehr verbreitet.<span></span></figcaption> </figure> <!-- EMBED END Image {id: "editor_18"} --> <h2><br/> </h2>';
        $result =  $this->getRendered('{% set x = setInlineAds(body) %} {{x|length}} ', ['body' => $body ]);
        self::assertEquals($result, ' 18 ');
    }

    public function testMultipleParagraphsUnderCount()
    {
        $body = '<p>test</p><p>article</p><p>under count</p>';
        $result = $this->getRendered('{% set x = setInlineAds(body) %} {{x|length}} ', ['body' => $body ]);
        self::assertEquals($result, ' 1 ');
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);
        return $content;
    }

}