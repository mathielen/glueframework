<?php
namespace Infrastructure\Persistence;

interface IdentityResolverInterface
{

    public function resolveByModel($model);

    public function resolveByValues($values);

}
