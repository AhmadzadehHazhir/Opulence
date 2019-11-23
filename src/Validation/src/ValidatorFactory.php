<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation;

use Opulence\Validation\Rules\RulesFactory;

/**
 * Defines the validator factory
 */
final class ValidatorFactory implements IValidatorFactory
{
    /** @var RulesFactory The rules factory */
    protected RulesFactory $rulesFactory;

    /**
     * @param RulesFactory|null $rulesFactory The rules factory
     */
    public function __construct(RulesFactory $rulesFactory = null)
    {
        $this->rulesFactory = $rulesFactory ?? new RulesFactory();
    }

    /**
     * @inheritdoc
     */
    public function createValidator(): IValidator
    {
        return new Validator($this->rulesFactory);
    }
}