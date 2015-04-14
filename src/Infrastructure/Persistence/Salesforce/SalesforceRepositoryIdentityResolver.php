<?php
namespace Infrastructure\Persistence\Salesforce;

use Doctrine\Common\Cache\Cache;
use Infrastructure\Persistence\IdentityResolverInterface;
use Infrastructure\Persistence\Repository;

class SalesforceRepositoryIdentityResolver implements IdentityResolverInterface
{

    /**
     * @var SalesforceRepository
     */
    private $repository;

    /**
     * @var Cache
     */
    private $idCache;

    private $properties;
    private $idsResolved = false;
    private $reflectionProperties = [];

    public function __construct(
        SalesforceRepository $repository,
        Cache $idCache,
        array $properties = [])
    {
        $this->repository = $repository;
        $this->idCache = $idCache;
        $this->properties = $properties;

        if (empty($this->properties)) {
            throw new \InvalidArgumentException("At least one property must be given");
        }
    }

    /**
     * @return array
     */
    private function reflectionProperties($model)
    {
        if (empty($this->reflectionProperties)) {
            $reflection = new \ReflectionClass(get_class($model));

            foreach ($this->properties as $property) {
                $reflectionProperty = $reflection->getProperty($property);
                $reflectionProperty->setAccessible(true);
                $this->reflectionProperties[] = $reflectionProperty;
            }
        }

        return $this->reflectionProperties;
    }

    private function getIdValuesFromModel($model)
    {
        $values = [];
        foreach ($this->reflectionProperties($model) as $reflectionProperty) {
            $value = $reflectionProperty->getValue($model);
            if (is_object($value) && method_exists($value, 'getId')) {
                $value = $value->getId();
            }
            $values[] = $value;
        }

        return $values;
    }

    /**
     * @return Cache
     */
    private function cache()
    {
        if (!$this->idsResolved) {
            $models = $this->repository->getAll();
            if ($models) {
                foreach ($models as $model) {
                    $id = $model->getId();
                    $values = $this->getIdValuesFromModel($model);

                    $this->idCache->save($this->getCacheIdFromValues($values), $id);
                }
            }

            $this->idsResolved = true;
        }

        return $this->idCache;
    }

    private function getCacheIdFromValues($values)
    {
        return json_encode($values);
    }

    public function resolveByValues($values)
    {
        if (empty($values)) {
            throw new \InvalidArgumentException("Cannot resolve id from empty values");
        }

        $cacheId = $this->getCacheIdFromValues($values);
        if (!$this->cache()->contains($cacheId)) {
            return null;
        }

        return $this->cache()->fetch($cacheId);
    }

    public function resolveByModel($model)
    {
        if (empty($model)) {
            throw new \InvalidArgumentException("Cannot resolve id from empty model");
        }

        return $this->resolveByValues($this->getIdValuesFromModel($model));
    }

    /**
     * @return Cache
     */
    public function getIdCache()
    {
        return $this->idCache;
    }

}
