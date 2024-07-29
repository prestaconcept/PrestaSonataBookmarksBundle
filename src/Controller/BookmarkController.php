<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Presta\SonataBookmarksBundle\Entity\Bookmark;
use Presta\SonataBookmarksBundle\Entity\BookmarkOwnerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends CRUDController<Bookmark>
 */
final class BookmarkController extends CRUDController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
    ) {
    }

    public function newAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $owner = $this->getUser();
        if (!$owner instanceof BookmarkOwnerInterface) {
            throw $this->createAccessDeniedException();
        }

        $bookmark = new Bookmark();
        $route = $request->request->get('route');

        if ($route === null) {
            return new JsonResponse(
                [
                    'msg' => 'route not found in request',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $bookmark->setName($request->request->get('name'));

        $requestParameters = $request->request->all();
        unset($requestParameters['route']);
        unset($requestParameters['name']);

        $url = $this->router->generate($route, $requestParameters);

        $bookmark->setUrl($url);
        $bookmark->setOwner($owner);

        $violations = $validator->validate($bookmark);
        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();

        return $this->json(
            [
                'name' => $bookmark->getName(),
                'url' => $bookmark->getUrl(),
            ],
            Response::HTTP_CREATED,
        );
    }
}
