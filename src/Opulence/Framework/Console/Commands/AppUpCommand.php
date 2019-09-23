<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the application-up command
 */
final class AppUpCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName('app:up')
            ->setDescription('Takes the application out of maintenance mode');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        @unlink(Config::get('paths', 'tmp.framework.http') . '/down');
        $response->writeln('<success>Application out of maintenance mode</success>');
    }
}
