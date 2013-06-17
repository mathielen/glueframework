<?php
namespace Infrastructure;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\ContainerAwarenessPass;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Monolog\Logger;

class Bootstrap
{

	/**
	 * @var array
	 */
	private $config;
	private $isValid = false;

	/**
	 * @var ContainerInterface
	 */
	private static $container;

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return \Infrastructure\Bootstrap
	 */
	public function sanitize()
	{
		if (!isset($this->config['root_dir'])) {
			throw new \InvalidArgumentException('root_dir must be given');
		}
		$this->config['root_dir'] = realpath($this->config['root_dir']);

		$this->applyDirectoryLayout();

		if (!is_writable($this->config['temp_dir'])) {
			throw new \InvalidArgumentException('temp_dir '.$this->config['temp_dir'].' must be writeable');
		}

		$this->isValid = true;
		return $this;
	}

	private function applyDirectoryLayout()
	{
		$this->addDirectory('temp', '/tmp');
		$this->addDirectory('config', '/config');
		$this->addDirectory('data', '/data');
	}

	private function addDirectory($prefixName, $defaultRootSuffix)
	{
		$configKey = $prefixName . '_dir';

		//apply default
		if (!isset($this->config[$configKey])) {
			$this->config[$configKey] = $this->config['root_dir'] . $defaultRootSuffix;
		}

		//realpathify
		$this->config[$configKey] = realpath($this->config[$configKey]);
	}

	/**
	 * @throws \DomainException
	 * @return \Infrastructure\Bootstrap
	 */
	public function apply()
	{
		if (!$this->isValid) {
			throw new \DomainException('bootstrap has not yet been validated');
		}

		if ($this->config['debug']) {
			ini_set("display_errors", TRUE);
			ini_set('display_startup_errors', TRUE);
			error_reporting(E_ALL ^ E_NOTICE);
			$this->config['loglevel'] = Logger::DEBUG;
		}

		if (file_exists($this->config['config_dir'] . '/config.ini')) {
			$configIni = parse_ini_file($this->config['config_dir'] . '/config.ini');
			$this->config = array_merge($this->config, $configIni);
		}

		return $this;
	}

	/**
	 * @return ContainerInterface
	 */
	public function container()
	{
		$containerCacheClass = $this->config['container_cacheclassname'];
		$containerCacheFile = $this->config['temp_dir'] . '/'.$containerCacheClass.'.php';
		$containerConfigCache = new ConfigCache($containerCacheFile, $this->config['debug']);

		if ($this->config['debug'] || !$containerConfigCache->isFresh()) {
			$containerBuilder = new ContainerBuilder();

			$loader = new XmlFileLoader($containerBuilder, new FileLocator($this->config['config_dir']));
			$loader->load($this->config['context_file']);

			foreach ($this->config as $name=>$value) {
				$containerBuilder->setParameter(strtolower($name), $value);
			}

			$containerBuilder->compile();

			$dumper = new PhpDumper($containerBuilder);
			$containerConfigCache->write(
				$dumper->dump(array('class' => $containerCacheClass)),
				$containerBuilder->getResources()
			);
		}

		require_once $containerCacheFile;
		$container = new $containerCacheClass();
		self::$container = $container;

		return self::containerInstance();
	}

	/**
	 * @return array
	 */
	private static function defaultConfig()
	{
		return array(
			'debug' => false,
			'container_cacheclassname' => 'CachedContainer',
			'context_file' => 'context.xml',
			'loglevel' => Logger::INFO
		);
	}

	/**
	 * @return Bootstrap
	 * @param array $config
	 */
	public static function boot(array $config = array())
	{
		$config = array_merge(self::defaultConfig(), $config);

		$bootstrap = new self($config);
		$bootstrap
			->sanitize()
			->apply();

		return $bootstrap;
	}

	/**
	 * @return ContainerInterface
	 */
	public static function containerInstance()
	{
		return self::$container;
	}

	public static function isDebug()
	{
		if (self::$container) {
			return self::$container->getParameter('debug');
		}

		return null;
	}

}