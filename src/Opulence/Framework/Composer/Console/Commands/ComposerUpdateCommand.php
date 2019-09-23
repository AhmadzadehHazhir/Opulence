<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Composer\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Composer\Executable;

/**
 * Defines the Composer update command
 */
final class ComposerUpdateCommand extends Command
{
    /** @var Executable The executable wrapper */
    private Executable $executable;

    /**
     * @param Executable $executable The Composer executable
     */
    public function __construct(Executable $executable)
    {
        parent::__construct();

        $this->executable = $executable;
    }

    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName('composer:update')
            ->setDescription('Updates any Composer dependencies');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->write($this->executable->update());
        $this->commandCollection->call('composer:dump-autoload', $response);
    }
}
