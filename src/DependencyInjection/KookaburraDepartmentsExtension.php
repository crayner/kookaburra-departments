<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 5/10/2019
 * Time: 18:14
 */
namespace Kookaburra\Departments\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class KookaburraDepartmentsExtension
 * @package Kookaburra\Departments\DependencyInjection
 */
class KookaburraDepartmentsExtension extends Extension
{
    /**
     * load
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new YamlFileLoader(
            $container,
            $locator
        );
        $loader->load('services.yaml');
    }
}