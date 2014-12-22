<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 26. 10. 2014
 * Time: 17:38
 */

namespace Trejjam\DI;

use Nette;

class UtilsExtension extends Nette\DI\CompilerExtension
{
	private $defaults = [
		'labels'   => [
			'table'     => 'utils__labels',
			'id'        => 'id',
			'namespace' => [
				'name'    => 'namespace',
				'default' => 'default'
			],
			'name'      => 'name',
			'value'     => 'value',
		],
		'pageInfo' => [
			'table'        => 'page_info',
			'id'           => 'id',
			'parentId'     => 'parent_id',
			'page'         => 'page',
			/*
			 * parentId=>NULL: [[module:]presenter:]action
			 * parentId=>int:  value for parent subAttribute
			*/
			'subAttribute' => 'sub_attribute',
			'title'        => 'title',
			'description'  => 'description',
			'keywords'     => 'keywords',
			'img'          => 'img',
			'rootPage'     => 1,
			'cache'        => [
				"use"     => TRUE,
				"name"    => "page_info",
				"timeout" => "60 minutes"
			],
		],
		'layout'   => [
			'fileVersion'   => 1,
			'reformatFlash' => TRUE,
		],
		'debugger' => FALSE, //not implemented yet
	];

	public function loadConfiguration() {
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$labels = $builder->addDefinition($this->prefix('labels'))
						  ->setClass('Trejjam\Utils\Labels')
						  ->addSetup("setConfig", [
							  "config" => $config["labels"],
						  ]);

		$label = $builder->addDefinition($this->prefix('label'))
						 ->setClass('Trejjam\Utils\Components\Label');

		$pageInfo = $builder->addDefinition($this->prefix('pageInfo'))
							->setClass('Trejjam\Utils\PageInfo')
							->addSetup("setConfig", [
								"config" => $config["pageInfo"],
							]);

		$layout = $builder->addDefinition($this->prefix('baseLayout'))
						  ->setClass('Trejjam\Utils\Layout\BaseLayout')
						  ->addSetup("setConfig", [
							  "config" => $config["layout"],
						  ]);

		if (class_exists('\Symfony\Component\Console\Command\Command')) {
			$command = [
				"cliLabels"  => "CliLabels",
				"cliInstall" => "CliInstall",
			];

			foreach ($command as $k => $v) {
				$builder->addDefinition($this->prefix($k))
						->setClass('Trejjam\Utils\\' . $v)
						->addTag("kdyby.console.command");
			}
		}

		if ($config["pageInfo"]["cache"]["use"]) {
			$builder->addDefinition($this->prefix("cache"))
					->setFactory('Nette\Caching\Cache')
					->setArguments(['@cacheStorage', $config["pageInfo"]["cache"]["name"]])
					->setAutowired(FALSE);

			$pageInfo->setArguments([$this->prefix("@cache")])
					 ->addSetup("setTimeout", ["timeout" => $config["pageInfo"]["cache"]["timeout"]]);
		}

		/*
		if ($config["cache"]["use"]) {
			$builder->addDefinition($this->prefix("cache"))
					->setFactory('Nette\Caching\Cache')
					->setArguments(['@cacheStorage', $config["cache"]["name"]])
					->setAutowired(FALSE);

			$accessControlList->setArguments([$this->prefix("@cache")])
							  ->addSetup("setCacheParams", ["cacheParams" => [
								  Nette\Caching\Cache::EXPIRE => $config["cache"]["timeout"]
							  ]]);
		}


		if ($config["debugger"]) {
			$builder->addDefinition($this->prefix("panel"))
					->setClass('Trejjam\Utils')
					->setAutowired(FALSE);

			$accessControlList->addSetup('injectPanel', array($this->prefix("@panel")));
		}*/
	}
}