<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Bundle\CoreBundle\Resolver\ArticleResolverInterface;
use Symfony\Component\Form\FormFactoryInterface;
use function array_key_exists;
use function parse_url;
use function str_replace;
use function strpos;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Form\Type\ArticleCommentsType;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class ArticleCommentsController extends AbstractController {

  private ArticleRepositoryInterface $articleRepository;
  private ArticleResolverInterface $articleResolver;
  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param ArticleRepositoryInterface $articleRepository
   * @param ArticleResolverInterface $articleResolver
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(ArticleRepositoryInterface $articleRepository, ArticleResolverInterface $articleResolver,
                              FormFactoryInterface       $formFactory, EventDispatcherInterface $eventDispatcher) {
    $this->articleRepository = $articleRepository;
    $this->articleResolver = $articleResolver;
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/content/articles", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_article_comments")
   */
  public function updateAction(Request $request): SingleResourceResponseInterface {
    $repository = $this->articleRepository;
    $articleResolver = $this->articleResolver;
    $form = $this->formFactory->createNamed('', ArticleCommentsType::class, [], ['method' => $request->getMethod()]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();
      $article = null;
      if (null !== $data['url']) {
        $article = strpos($data['url'], '/r/') ? $repository->findOneBySlug(
            str_replace('/r/', '', $this->getFragmentFromUrl($data['url'], 'path'))
        ) : $articleResolver->resolve($data['url']);
      } elseif (null !== $data['id']) {
        $article = $repository->findOneBy(['id' => $data['id']]);
      }

      if (null === $article) {
        throw new NotFoundHttpException('Article was not found');
      }

      $article->setCommentsCount((int)$data['commentsCount']);
      $article->cancelTimestampable();
      $repository->flush();

      $this->eventDispatcher->dispatch(new ArticleEvent(
          $article,
          $article->getPackage(),
          ArticleEvents::POST_UPDATE
      ), ArticleEvents::POST_UPDATE);

      return new SingleResourceResponse($article);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  private function getFragmentFromUrl(string $url, string $fragment): ?string {
    $fragments = parse_url($url);
    if (!array_key_exists($fragment, $fragments)) {
      return null;
    }

    return $fragments[$fragment];
  }
}
