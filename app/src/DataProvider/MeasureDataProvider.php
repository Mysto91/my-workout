<?php

// api/src/DataProvider/BlogPostCollectionDataProvider.php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Measure;
use App\Entity\User;
use App\Repository\MeasureRepository;
use Symfony\Component\Security\Core\Security;

final class MeasureDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private MeasureRepository $measureRepository;
    private Security $security;

    public function __construct(MeasureRepository $measureRepository, Security $security)
    {
        $this->measureRepository = $measureRepository;
        $this->security = $security;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array<string> $context
     *
     * @return boolean
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Measure::class === $resourceClass;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array<string> $context
     *
     * @return iterable<Measure>
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $user->getRole()->getLabel() === 'admin' ? $this->measureRepository->findAll() : $this->measureRepository->findByUser($user);
    }
}
