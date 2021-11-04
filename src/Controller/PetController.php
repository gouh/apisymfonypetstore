<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Repository\PetRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PetController
 * @package App\Controller
 *
 * @Route(path="/pets")
 */
class PetController
{
    /** @var PetRepository */
    private PetRepository $petRepository;

    private \Laminas\Hydrator\ReflectionHydrator $hydrator;

    /**
     * @param PetRepository $petRepository
     */
    public function __construct(PetRepository $petRepository)
    {
        $this->petRepository = $petRepository;
        $this->hydrator = new \Laminas\Hydrator\ReflectionHydrator();
        $this->hydrator->addStrategy(
            'Pets',
            new \Laminas\Hydrator\Strategy\NullableStrategy(
                new \Laminas\Hydrator\Strategy\CollectionStrategy(
                    new \Laminas\Hydrator\ReflectionHydrator(),
                    Pet::class
                )
            )
        );
        $this->hydrator->addStrategy(
            'Pet',
            new \Laminas\Hydrator\Strategy\NullableStrategy(
                new \Laminas\Hydrator\Strategy\HydratorStrategy(
                    new \Laminas\Hydrator\ReflectionHydrator(),
                    Pet::class
                )
            )
        );
    }

    /**
     * @Route(path="", name="add_pet", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pet = $this->petRepository->save($data);
        return new JsonResponse(["data" => $this->hydrator->getStrategy('Pet')->extract($pet)]);
    }

    /**
     * @Route(path="/{petId}", name="update_pet", requirements={"petId"="\d+"}, methods={"PUT"})
     */
    public function update($petId, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $pet = $this->petRepository->update($petId, $data);
        return new JsonResponse(["data" => $this->hydrator->getStrategy('Pet')->extract($pet)]);
    }

    /**
     * @Route(path="", name="get_pets", methods={"GET","HEAD"})
     * @Route(path="/{petId}", name="get_pet", requirements={"petId"="\d+"}, methods={"GET","HEAD"})
     */
    public function get($petId = null): JsonResponse
    {
        if ($petId) {
            $data = $this->petRepository->find($petId);
            $data = $this->hydrator->getStrategy('Pet')->extract($data);
        } else {
            $data = $this->petRepository->findAll();
            $data = $this->hydrator->getStrategy('Pets')->extract($data);
        }

        $response = ["data" => $data ? $data : []];
        return new JsonResponse($response, $data ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}
