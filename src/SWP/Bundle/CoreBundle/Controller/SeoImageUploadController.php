<?php

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\SeoBundle\Form\Type\ImageUploadType;
use SWP\Bundle\SeoBundle\Uploader\SeoImageUploader;
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

            try {
                $fileUploader = $this->get(SeoImageUploader::class);

                if (null !== $seoMetadata->getMetaImageFile()) {
                    $fileName = $fileUploader->upload($seoMetadata->getMetaImageFile());
                    $seoMetadata->setMetaImageName($fileName);
                }

                if (null !== $seoMetadata->getOgImageFile()) {
                    $fileName = $fileUploader->upload($seoMetadata->getOgImageFile());
                    $seoMetadata->setOgImageName($fileName);
                }

                if (null !== $seoMetadata->getTwitterImageFile()) {
                    $fileName = $fileUploader->upload($seoMetadata->getTwitterImageFile());
                    $seoMetadata->setTwitterImageName($fileName);
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
