<?php

namespace Trejjam\Utils\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette;
use Trejjam;

class Labels extends Command
{
	/**
	 * @var Trejjam\Utils\Labels\Labels
	 */
	protected $labels;

	public function __construct(Trejjam\Utils\Labels\Labels $labels)
	{
		parent::__construct();

		$this->labels = $labels;
	}

	protected function configure()
	{
		$this->setName('Utils:labels')
			 ->setDescription('Edit labels')
			 ->addArgument(
				 'label',
				 InputArgument::OPTIONAL,
				 'Enter label name'
			 )->addArgument(
				'value',
				InputArgument::OPTIONAL,
				'Enter label value'
			)->addOption(
				'namespace',
				's',
				InputOption::VALUE_REQUIRED,
				'Set namespace'
			)->addOption(
				'delete',
				'd',
				InputOption::VALUE_NONE,
				'Delete label'
			);
	}
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$namespace = $input->getOption('namespace');
		$delete = $input->getOption('delete');

		$label = $input->getArgument('label');
		$value = $input->getArgument('value');

		if ($delete) {
			$this->labels->delete($label, $namespace);
		}
		else {
			$this->labels->setData($label, $value, $namespace);
		}
	}
}
