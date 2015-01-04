<?php
namespace Infrastructure\Symfony2;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;

use Oro\Component\Config\CumulativeResourceManager;

abstract class GlueKernel extends Kernel
{

    /**
     * {@inheritdoc}
     */
    protected function initializeBundles()
    {
        parent::initializeBundles();

        // pass bundles to CumulativeResourceManager
        $bundles = [];
        foreach ($this->bundles as $name => $bundle) {
            $bundles[$name] = get_class($bundle);
        }
        CumulativeResourceManager::getInstance()->setBundles($bundles);
    }

    /**
     * Get the list of all "autoregistered" bundles
     *
     * @return array List ob bundle objects
     */
    public function registerBundles()
    {
        // clear state of CumulativeResourceManager
        CumulativeResourceManager::getInstance()->clear();

        $bundles = [];

        if (!$this->getCacheDir()) {
            foreach ($this->collectBundles() as $class => $params) {
                $bundles[] = $params['kernel']
                    ? new $class($this)
                    : new $class;
            }
        } else {
            $file  = $this->getCacheDir() . '/bundles.php';
            $cache = new ConfigCache($file, $this->debug);

            if (!$cache->isFresh($file)) {
                $bundles = $this->collectBundles();
                $dumper = new PhpBundlesDumper($bundles);

                $metaData = [];
                foreach ($bundles as $bundle) {
                    $metaData[] = new FileResource($bundle['file']);
                }
                $metaData[] = new FileResource($this->rootDir . '/../composer.lock'); //a composer update might add bundles
                $metaData[] = new DirectoryResource($this->rootDir . '/../src/', '/^bundles.yml$/'); //all bundles.yml in src

                $cache->write($dumper->dump(), $metaData);
            }

            // require instead of require_once used to correctly handle sub-requests
            $bundles = require $cache;
        }

        return $bundles;
    }

    /**
     * Finds all .../Resource/config/oro/bundles.yml in given root folders
     *
     * @param array $roots
     *
     * @return array
     */
    protected function findBundles($roots = [])
    {
        $paths = [];
        foreach ($roots as $root) {
            if (!is_dir($root)) {
                continue;
            }
            $root   = realpath($root);
            $dir    = new \RecursiveDirectoryIterator($root, \FilesystemIterator::FOLLOW_SYMLINKS);
            $filter = new \RecursiveCallbackFilterIterator(
                $dir,
                function (\SplFileInfo $current) use (&$paths) {
                    $fileName = strtolower($current->getFilename());
                    if ($fileName === '.'
                        || $fileName === '..'
                        || $fileName === 'tests'
                        || $current->isFile()
                    ) {
                        return false;
                    }
                    if (!is_dir($current->getPathname() . '/Resources')) {
                        return true;
                    } else {
                        $file = $current->getPathname() . '/Resources/config/glue/bundles.yml';
                        if (is_file($file)) {
                            $paths[] = $file;
                        }

                        return false;
                    }
                }
            );

            $iterator = new \RecursiveIteratorIterator($filter);
            $iterator->rewind();
        }

        return $paths;
    }

    /**
     * @return array
     */
    protected function collectBundles()
    {
        $files = $this->findBundles(
            [
                $this->getRootDir() . '/../src',
                $this->getRootDir() . '/../vendor'
            ]
        );

        $bundles    = [];
        $exclusions = [];
        foreach ($files as $file) {
            $import  = Yaml::parse($file);
            $bundles = array_merge($bundles, $this->getBundlesMapping($import['bundles'], $file));
            if (!empty($import['exclusions'])) {
                $exclusions = array_merge($exclusions, $this->getBundlesMapping($import['exclusions'], $file));
            }
        }

        $bundles = array_diff_key($bundles, $exclusions);

        uasort($bundles, [$this, 'compareBundles']);

        return $bundles;
    }

    /**
     * @param $bundles
     *
     * @return array
     */
    protected function getBundlesMapping(array $bundles, $file)
    {
        $result = [];
        foreach ($bundles as $bundle) {
            $kernel   = false;
            $priority = 0;

            if (is_array($bundle)) {
                $class    = $bundle['name'];
                $kernel   = isset($bundle['kernel']) && true == $bundle['kernel'];
                $priority = isset($bundle['priority']) ? (int) $bundle['priority'] : 0;
            } else {
                $class = $bundle;
            }

            $result[$class] = [
                'name'     => $class,
                'kernel'   => $kernel,
                'priority' => $priority,
                'file'     => $file
            ];
        }

        return $result;
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public function compareBundles($a, $b)
    {
        // @todo: this is preliminary algorithm. we need to implement more sophisticated one,
        // for example using bundle dependency info from composer.json
        $p1 = (int) $a['priority'];
        $p2 = (int) $b['priority'];

        if ($p1 == $p2) {
            $n1 = (string) $a['name'];
            $n2 = (string) $b['name'];

            //removed ORO stuff

            // bundles with the same priorities are sorted alphabetically
            return strcasecmp($n1, $n2);
        }

        // sort be priority
        return ($p1 < $p2) ? -1 : 1;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function boot()
    {
        /*$phpVersion = phpversion();

        include_once $this->getRootDir() . '/OroRequirements.php';

        if (!version_compare($phpVersion, OroRequirements::REQUIRED_PHP_VERSION, '>=')) {
            throw new \Exception(
                sprintf(
                    'PHP version must be at least %s (%s is installed)',
                    OroRequirements::REQUIRED_PHP_VERSION,
                    $phpVersion
                )
            );
        }*/

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        // cache the container
        $dumper = new PhpDumper($container);

        if (class_exists('ProxyManager\Configuration')) {
            $dumper->setProxyDumper(new ProxyDumper());
        }

        $content = $dumper->dump(['class' => $class, 'base_class' => $baseClass]);
        $cache->write($content, $container->getResources());

        if (!$this->debug) {
            $cache->write(php_strip_whitespace($cache), $container->getResources());
        }
    }

    /**
     * Add custom error handler
     */
    protected function initializeContainer()
    {
        $handler = new ErrorHandler();
        $handler->registerHandlers();

        parent::initializeContainer();
    }

}
