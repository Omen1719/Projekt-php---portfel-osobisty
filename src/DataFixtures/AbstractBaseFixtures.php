<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Base fixture providing Faker and entity-reference helpers.
 */
abstract class AbstractBaseFixtures extends Fixture
{
    protected ?Generator $faker = null;
    protected ?ObjectManager $manager = null;

    /**
     * Load.
     *
     * @param ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData();
    }

    /**
     * Load data.
     */
    abstract protected function loadData(): void;

    /**
     * Create many.
     *
     * @param int      $count              Count
     * @param string   $referenceGroupName Reference group name
     * @param callable $factory            Factory
     */
    protected function createMany(int $count, string $referenceGroupName, callable $factory): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $entity = $factory($i);
            if (null === $entity) {
                throw new \LogicException('Did you forget to return the entity object from your callback to BaseFixture::createMany()?');
            }
            $this->manager->persist($entity);
            $this->addReference(sprintf('%s_%d', $referenceGroupName, $i), $entity);
        }
        $this->manager->flush();
    }

    /**
     * Get random reference.
     *
     * @param string $referenceGroupName Reference group name
     * @param string $className          Class name
     *
     * @return object Random reference
     */
    protected function getRandomReference(string $referenceGroupName, string $className): object
    {
        $referenceNameList = $this->getReferenceNameListByClassName($referenceGroupName, $className);
        $randomReferenceName = (string) $this->faker->randomElement($referenceNameList);

        return $this->getReference($randomReferenceName, $className);
    }

    /**
     * Get random reference list.
     *
     * @param string $referenceGroupName Reference group name
     * @param string $className          Class name
     * @param int    $count              Count
     *
     * @return array Random reference list
     */
    protected function getRandomReferenceList(string $referenceGroupName, string $className, int $count): array
    {
        $referenceNameList = $this->getReferenceNameListByClassName($referenceGroupName, $className);
        $references = [];
        while (count($references) < $count) {
            $randomReferenceName = (string) $this->faker->randomElement($referenceNameList);
            $references[] = $this->getReference($randomReferenceName, $className);
        }

        return $references;
    }

    /**
     * Get reference name list by class name.
     *
     * @param string $referenceGroupName Reference group name
     * @param string $className          Class name
     *
     * @return array Reference name list by class name
     */
    private function getReferenceNameListByClassName(string $referenceGroupName, string $className): array
    {
        if (!array_key_exists($className, $this->referenceRepository->getIdentitiesByClass())) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the name "%s"', $className));
        }
        $referenceNameListByClass = array_keys($this->referenceRepository->getIdentitiesByClass()[$className]);
        if ([] === $referenceNameListByClass) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the name "%s"', $className));
        }
        $referenceNameList = array_filter(
            $referenceNameListByClass,
            fn ($referenceName) => preg_match_all("/^{$referenceGroupName}_\\d+\$/", (string) $referenceName)
        );
        if ([] === $referenceNameList) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the group name "%s" and class name "%s"', $referenceGroupName, $className));
        }

        return $referenceNameList;
    }
}
