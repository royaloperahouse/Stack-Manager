<?php

/*
 * This file is part of the Stack Manager package.
 *
 * © Royal Opera House Covent Garden Foundation <website@roh.org.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ROH\Bundle\StackManagerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to list all available templates.
 *
 * @author Robert Leverington <robert.leverington@roh.org.uk>
 */
class ListTemplatesCommand extends Command
{
    /**
     * @var string[]
     */
    private $defaults;

    /**
     * @var string[]
     */
    private $environments;

    /**
     * @var string[]
     */
    private $scalingProfiles;

    public function __construct(
        array $defaults,
        array $environments,
        array $scalingProfiles
    ) {
        $this->defaults = $defaults;
        $this->environments = $environments;
        $this->scalingProfiles = $scalingProfiles;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('stack-manager:list-templates')
            ->setDescription(
                'List all available templates, their environments and their'
                .' scaling profiles'
            )
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templates = [];
        foreach ($this->defaults as $name => $defaults) {
            $templates[$name] = [
                'environments' => [],
                'scalingProfiles' => [],
            ];
        }

        foreach ($this->environments as $template => $environments) {
            foreach ($environments as $name => $options) {
                $templates[$template]['environments'][] = $name;
            }
        }

        foreach ($this->scalingProfiles as $template => $scalingProfiles) {
            foreach ($scalingProfiles as $name => $options) {
                $templates[$template]['scalingProfiles'][] = $name;
            }
        }

        $table = new Table($output);
        $table->setHeaders(['Name', 'Environments', 'Scaling profiles']);

        $i = 0;
        foreach ($templates as $name => $data) {
            ++$i;

            $table->addRow([
                $name,
                implode("\n", $data['environments']),
                implode("\n", $data['scalingProfiles']),
            ]);

            if ($i !== count($templates)) {
                $table->addRow(new TableSeparator());
            }
        }
        $table->render();
    }
}
