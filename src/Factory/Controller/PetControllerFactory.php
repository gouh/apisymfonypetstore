<?php

namespace App\Factory\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PetControllerFactory
{
    /** @var ParameterBagInterface  */
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function __invoke()
    {
        $this->parameterBag->get("");
    }
}
