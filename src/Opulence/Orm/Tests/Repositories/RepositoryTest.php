<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\Repositories;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Server;
use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\EntityRegistry;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\Ids\Generators\IIdGeneratorRegistry;
use Opulence\Orm\Ids\Generators\IntSequenceIdGenerator;
use Opulence\Orm\OrmException;
use Opulence\Orm\Repositories\Repository;
use Opulence\Orm\Tests\DataMappers\Mocks\SqlDataMapper;
use Opulence\Orm\Tests\Repositories\Mocks\User;
use Opulence\Orm\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the repository class
 */
class RepositoryTest extends \PHPUnit\Framework\TestCase
{
    private User $entity1;
    private User $entity2;
    private UnitOfWork $unitOfWork;
    private SqlDataMapper $dataMapper;
    private Repository $repo;

    protected function setUp(): void
    {
        $idAccessorRegistry = new IdAccessorRegistry();
        $idAccessorRegistry->registerIdAccessors(
            User::class,
            function ($user) {
                /** @var User $user */
                return $user->getId();
            },
            function ($user, $id) {
                /** @var User $user */
                $user->setId($id);
            }
        );
        /** @var IIdGeneratorRegistry|MockObject $idGeneratorRegistry */
        $idGeneratorRegistry = $this->createMock(IIdGeneratorRegistry::class);
        $idGeneratorRegistry->expects($this->any())
            ->method('getIdGenerator')
            ->with(User::class)
            ->willReturn(new IntSequenceIdGenerator('foo'));
        $changeTracker = new ChangeTracker();
        $server = new Server();
        $connection = new Connection($server);
        $entityRegistry = new EntityRegistry($idAccessorRegistry, $changeTracker);
        $this->unitOfWork = new UnitOfWork(
            $entityRegistry,
            $idAccessorRegistry,
            $idGeneratorRegistry,
            $changeTracker,
            $connection
        );
        $this->dataMapper = new SqlDataMapper();
        $this->entity1 = new User(1, 'foo');
        $this->entity2 = new User(2, 'bar');
        $this->repo = new Repository(get_class($this->entity1), $this->dataMapper, $this->unitOfWork);
    }

    public function testAddingEntity(): void
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }

    public function testDeletingEntity(): void
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->repo->delete($this->entity1);
        $this->unitOfWork->commit();
        $this->expectException(OrmException::class);
        $this->repo->getById($this->entity1->getId());
    }

    public function testGettingAll(): void
    {
        $this->repo->add($this->entity1);
        $this->repo->add($this->entity2);
        $this->unitOfWork->commit();
        $this->assertEquals([$this->entity1, $this->entity2], $this->repo->getAll());
    }

    public function testGettingAllAfterAddingEntitiesInDifferentTransactions(): void
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->repo->add($this->entity2);
        $this->unitOfWork->commit();
        $this->assertEquals([$this->entity1, $this->entity2], $this->repo->getAll());
    }

    public function testGettingById(): void
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }

    public function testGettingByIdWhenDataMapperReturnsNullThrowsException(): void
    {
        $this->expectException(OrmException::class);
        $this->repo->getById($this->entity1->getId());
    }

    /**
     * Tests the repo and unit of work to make sure the same instance of an already-managed entity is returned by getAll
     */
    public function testGettingEntityByIdAndThenAllEntities(): void
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        /** @var User $entityFromGetById */
        $entityFromGetById = $this->repo->getById($this->entity1->getId());
        /** @var User[] $allEntities */
        $allEntities = $this->repo->getAll();
        /** @var User $entityFromGetAll */
        $entityFromGetAll = null;

        foreach ($allEntities as $entity) {
            $entity->setUsername('newUsername');

            if ($entity->getId() == $entityFromGetById->getId()) {
                $entityFromGetAll = $entity;
            }
        }

        foreach ($allEntities as $entity) {
            if ($entity->getId() == $entityFromGetById->getId()) {
                $this->assertSame($entityFromGetById, $entity);
                $this->assertEquals($entityFromGetAll->getUsername(), $entityFromGetById->getUsername());
            }
        }
    }

    public function testGettingEntityThatDoesNotExistById(): void
    {
        $this->expectException(OrmException::class);
        $this->repo->getById(123);
    }

    public function testGettingEntityThatExistsInDataMapperButNotRepo(): void
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }
}
