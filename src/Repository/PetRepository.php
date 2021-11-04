<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @method Pet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pet[]    findAll()
 * @method Pet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $manager;

    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($registry, Pet::class);
        $this->manager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param $data
     * @return Pet|null
     */
    public function save($data): ?Pet
    {
        $response = null;
        try {
            $pet = new Pet();
            $pet
                ->setName($data['name'])
                ->setType($data['type'])
                ->setPicture($data['picture']);

            $this->manager->persist($pet);
            $this->manager->flush();

            $response = $pet;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->logger->debug($e->getTraceAsString());
        }
        return $response;
    }

    /**
     * @param $petId
     * @param $data
     * @return Pet|null
     */
    public function update($petId, $data): ?Pet
    {
        $response = null;
        try {
            $pet = $this->find($petId);
            $pet
                ->setName($data['name'])
                ->setType($data['type'])
                ->setPicture($data['picture']);

            $this->manager->persist($pet);
            $this->manager->flush();

            $response = $pet;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->logger->debug($e->getTraceAsString());
        }
        return $response;
    }
}
