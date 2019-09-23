<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Rules;

use LogicException;

/**
 * Defines the conditional rule
 */
final class ConditionalRule extends CallbackRule
{
    /** @var IRule[] The list of rules to evaluate if the condition is true */
    protected array $rules = [];

    /**
     * Adds a rule to evaluate if the condition is true
     *
     * @param IRule $rule
     */
    public function addRule(IRule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Gets the sub-rules in this condition
     *
     * @return IRule[] The list of rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'conditional';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        if ($this->callback === null) {
            throw new LogicException('Condition not set');
        }

        if (!($this->callback)($value, $allValues)) {
            return true;
        }

        $passes = true;

        foreach ($this->rules as $rule) {
            if (!$rule->passes($value, $allValues)) {
                $passes = false;
            }
        }

        return $passes;
    }
}
