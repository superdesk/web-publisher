<?php

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\SeoBundle\Form\Type\ImageUploadType;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SeoImageUploadController extends AbstractController
{
    /**
     * Uploads SEO image.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Uploads current theme logo",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\ThemeLogoUploadType"
     * )
     * @Route("/api/{version}/content/articles/{id}/upload", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_seo_image")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function uploadSeoImageAction(Request $request, string $id): SingleResourceResponse
    {
        $article = $this->findOr404($id);
        $seoMetadata = $article->getSeoMetadata();

        if (null === $seoMetadata) {
            $seoMetadata = $this->get('swp.factory.seo_metadata')->create();
        }

        $form = $this->get('form.factory')->createNamed('', ImageUploadType::class, $seoMetadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager = $this->get('swp.object_manager.seo_metadata');

            $seoMediaManager = $this->get('swp_core_bundle.manager.seo_media');
            $seoMediaFactory = $this->get('swp.factory.seo_media');
            $randomStringGenerator = $this->get('swp.random_string_generator');

            try {
                if (null !== ($file = $seoMetadata->getMetaMediaFile())) {
                    $image = $seoMediaManager->handleUploadedFile($file, $randomStringGenerator->generate(15));
                    $seoImageMedia = $seoMediaFactory->create();
                    $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_META_KEY);
                    $seoImageMedia->setImage($image);

                    $seoMetadata->setMetaMedia($seoImageMedia);
                }

                if (null !== ($file = $seoMetadata->getOgMediaFile())) {
                    $image = $seoMediaManager->handleUploadedFile($file, $randomStringGenerator->generate(15));
                    $seoImageMedia = $seoMediaFactory->create();
                    $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_OG_KEY);
                    $seoImageMedia->setImage($image);

                    $seoMetadata->setOgMedia($seoImageMedia);
                }

                if (null !== ($file = $seoMetadata->getTwitterMediaFile())) {
                    $image = $seoMediaManager->handleUploadedFile($file, $randomStringGenerator->generate(15));
                    $seoImageMedia = $seoMediaFactory->create();
                    $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_TWITTER_KEY);
                    $seoImageMedia->setImage($image);

                    $seoMetadata->setTwitterMedia($seoImageMedia);
                }

                $article->setPublishable(true);

                if (null === $article->getSeoMetadata()) {
                    $article->setSeoMetadata($seoMetadata);
                }

                $objectManager->flush();
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => 'Could not upload an image.'], new ResponseContext(400));
            }

            return new SingleResourceResponse($seoMetadata, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404(string $id): ArticleInterface
    {
        if (null === $article = $this->get('swp.provider.article')->getOneById($id)) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
