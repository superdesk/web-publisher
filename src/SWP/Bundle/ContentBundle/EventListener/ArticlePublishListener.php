<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;

final class ArticlePublishListener
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * ArticlePublishListener constructor.
     *
     * @param ArticleServiceInterface $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @param ArticleEvent $event
     */
    public function publish(ArticleEvent $event)
    {

        $article = $event->getArticle();

        $articleSlug = $article->getSlug();

        //get te auth token from superdesk
        $superdesk_user = $_SERVER["SUPERDESK_USER"];
        $superdesk_pass = $_SERVER["SUPERDESK_PASS"];
        $token = $this->getAuth($superdesk_user, $superdesk_pass);

        //get the unique name from superdesk and append it to the end of the article link
        $data = $this->getArticleUniqueId($article->getCode(), $token);

        $uniqueId = $data["unique_name"];
        $familyId = $data["family_id"];
        $profile = $data["profile"];

        //get the contentType of the profile
        $contentType = $this->getArticleContentType($profile, $token);

        $contentTypeExploded =  explode("_", $contentType);

        if(count($contentTypeExploded) > 1 ){
            $k = "";
            foreach($contentTypeExploded as $label) {
                $k .= $label[0];
            }
            $article->setContentType($k);
        } else {
            $article->setContentType($contentType[0]);
        }



        // assign a id at the end of the url
        if ($uniqueId != null) {

            $uniqueId = '-id' . $uniqueId;

            //if there is an old id clear it
            if (preg_match('/(-id)[0-9]+$/', $articleSlug)) {
                $articleSlug = preg_replace('/(-id)[0-9]+$/', "", $articleSlug);
            }

            //add the id if it doesn't already exist in the url
            if (!preg_match("/{$uniqueId}/i", $articleSlug)) {

                $article->setSlug($articleSlug . $uniqueId);
            }
        }

        if ($familyId !== null)
            $article->setFamilyId($familyId);


        if ($article->isPublished()) {
            return;
        }

        $this->articleService->publish($article);
    }


    /**
     * get auth token
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    private function getAuth($username, $password)
    {

        $superdesk_domain = $_SERVER["SUPERDESK_CMS"] . "/api/auth_db";

        $json = json_encode(["username" => $username, "password" => $password]);

        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, $superdesk_domain);
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
        curl_setopt($chObj, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
            )
        );

        $response = curl_exec($chObj);

        return json_decode($response)->token;
    }


    private function getArticleContentType($profile, $token)
    {
        $superdesk_domain = $_SERVER["SUPERDESK_CMS"];

        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, $superdesk_domain . "/api/content_types/" . $profile);
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($chObj, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization:' . $token
            )
        );

        $response = curl_exec($chObj);

        $contentTypeLabel = json_decode($response)->label;


        if (curl_errno($chObj)) {
            echo 'Error:' . curl_error($chObj);
        }
        curl_close($chObj);

        $contentTypeLabel = $contentTypeLabel !== "" ? $contentTypeLabel : null;


        return $contentTypeLabel;
    }

    /**
     *
     * return the article unique id from superdesk
     *
     * @param $guid
     * @param $token
     * @return mixed
     */
    private function getArticleUniqueId($guid, $token)
    {

        $superdesk_domain = $_SERVER["SUPERDESK_CMS"];

        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, $superdesk_domain . "/api/archive/" . $guid);
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($chObj, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization:' . $token
            )
        );

        $response = curl_exec($chObj);

        $uniqueName = json_decode($response)->unique_name;
        $familyId = json_decode($response)->family_id;
        $contentProfile = json_decode($response)->profile;


        $uniqueName = str_replace('#', '', $uniqueName);

        if (curl_errno($chObj)) {
            echo 'Error:' . curl_error($chObj);
        }
        curl_close($chObj);

        $resultUniqueName = $uniqueName !== "" ? $uniqueName : null;
        $resultFamilyId = $familyId !== "" ? $familyId : null;
        $contentProfile = $contentProfile !== "" ? $contentProfile : null;

        $res["unique_name"] = $resultUniqueName;
        $res["family_id"] = $resultFamilyId;
        $res["profile"] = $contentProfile;

        return $res;
    }


    /**
     * @param ArticleEvent $event
     */
    public function unpublish(ArticleEvent $event)
    {
        $article = $event->getArticle();

        if ($article->isPublished()) {
            $this->articleService->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED);
        }
    }

    /**
     * @param ArticleEvent $event
     */
    public function cancel(ArticleEvent $event)
    {
        $this->articleService->unpublish($event->getArticle(), ArticleInterface::STATUS_CANCELED);
    }
}
