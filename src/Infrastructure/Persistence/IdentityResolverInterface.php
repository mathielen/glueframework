<?php
namespace Infrastructure\Persistence;

interface IdentityResolverInterface
{

    public function resolveId($model);

}
