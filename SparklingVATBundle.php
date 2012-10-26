<?php

namespace Sparkling\VATBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * VAT bundle
 *
 * @author Ruud Kamphuis <ruud@1plus1media.nl>
 */
class SparklingVATBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
