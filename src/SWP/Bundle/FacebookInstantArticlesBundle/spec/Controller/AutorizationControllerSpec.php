<?php

namespace spec\SWP\Bundle\FacebookInstantArticlesBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManager;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookManager;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\ApplicationInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AutorizationControllerSpec extends ObjectBehavior
{
    public function let(
        ContainerInterface $container,
        EntityRepository $entityRepository,
        ObjectManager $objectManager,
        ApplicationInterface $application,
        PageInterface $page,
        FacebookManager $facebookManager,
        FacebookInstantArticlesManager $facebookInstantArticlesManager,
        Facebook $facebook,
        FacebookRedirectLoginHelper $redirectLoginHelper,
        RouterInterface $router
    ) {
        $application->getAppId()->willReturn('123456789');
        $entityRepository->findOneBy(['appId' => '123456789'])->willReturn($application);
        $entityRepository->findOneBy(['pageId' => '987654321'])->willReturn($page);
        $container->get('swp.repository.application')->willReturn($entityRepository);
        $container->get('swp.repository.page')->willReturn($entityRepository);
        $container->get('swp.object_manager.page')->willReturn($objectManager);
        $container->get('swp_facebook.manager')->willReturn($facebookManager);
        $container->get('swp_facebook.instant_articles_manager')->willReturn($facebookInstantArticlesManager);
        $container->get('router')->willReturn($router);
        $redirectLoginHelper->getLoginUrl(Argument::cetera())->willReturn('http://example.com/');
        $facebook->getRedirectLoginHelper()->willReturn($redirectLoginHelper);
        $facebookInstantArticlesManager->getPageAccessToken(Argument::cetera(), Argument::cetera())->willReturn('sdatsa5gsrtvs4vt4dbhbevvtesybe5yvseve5ye');
        $facebookManager->createForApp($application)->willReturn($facebook);
        $this->setContainer($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\FacebookInstantArticlesBundle\Controller\AutorizationController');
    }

    public function it_should_generate_redirect_response()
    {
        $this->authorizeAction('123456789', '987654321')->shouldReturnAnInstanceOf(RedirectResponse::class);
    }

    public function it_should_return_access_token()
    {
        $this->authorizationCallbackAction('123456789', '987654321')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
