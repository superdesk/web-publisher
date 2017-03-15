<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\ContentBundle\Controller\ContentPushController as BaseContentPushController;
use SWP\Component\Bridge\Model\PackageInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentPushController extends BaseContentPushController
{
    /**
     * Receives HTTP Push Request's payload which is then processed by the pipeline.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Adds a new content from HTTP Push",
     *     statusCodes={
     *         201="Returned on successful post."
     *     }
     * )
     * @Route("/api/{version}/content/push", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_push")
     * @Method("POST")
     */
    public function pushContentAction(Request $request)
    {
        return parent::pushContentAction($request);
    }

    protected function getExistingArticleOrNull(PackageInterface $package)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');
        $existingArticle = $this->findArticleByCodeAndOrganization($package->getGuid());

        if (null === $existingArticle) {
            $existingArticle = $this->findArticleByCodeAndOrganization($package->getEvolvedFrom());
        }

        $entityManager->getFilters()->enable('tenantable');

        return $existingArticle;
    }

    private function findArticleByCodeAndOrganization(string $code = null)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        return $this->getArticleRepository()->findOneBy([
            'code' => $code,
            'organization' => $tenantContext->getTenant()->getOrganization(),
        ]);
    }
}
