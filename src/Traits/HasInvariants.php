<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use Exception;

/**
 * Trait HasInvariants
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasInvariants
{
    /**
     * Retrieve the object invariants.
     *
     * @return string[]
     */
    final public static function invariants(): array
    {
        $invariants = [];
        foreach (get_class_methods(static::class) as $invariant) {
            if (str_starts_with($invariant, 'invariant') && !in_array($invariant, ['invariants', 'invariantHandler'])) {
                $invariantRuleName = preg_replace('/[A-Z]([A-Z](?![a-z]))*/', ' $0', $invariant);
                if (is_null($invariantRuleName)) {
                    continue;
                }

                $invariants[$invariant] = str_replace('invariant ', '', strtolower($invariantRuleName));
            }
        }

        return $invariants;
    }

    /**
     * Execute all the registered invariants.
     *
     *  - All invariants method names must begin with the invariant prefix
     *    ("invariant" by default) and must be written in PascalCase.
     *  - All invariant can either return bool or throw an exception.
     *  - Return true by invariant means the invariants is passed successfully.
     *  - Return false by the invariant means the invariant has failed.
     *
     * If boolean is returned the error message will be the invariant method name
     * (PascalCase) in normal case.
     *
     * If exception is thrown the error message will be the exception message.
     *
     * $onFail function must have the following signature:
     *  fn(array<string, string>) => void
     *
     * @param  string|callable  $onFail
     *
     * @return void
     */
    private function check(string|callable $onFail = 'invariantHandler'): void
    {
        $violations = $this->computeInvariantViolations();
        if (!empty($violations)) {
            call_user_func_array($this->computeInvariantHandler($onFail), [$violations]);
        }
    }

    /**
     * Computes the list of invariant violations.
     *
     * @return array<string, string>
     */
    private function computeInvariantViolations(): array
    {
        $violations = [];
        foreach (static::invariants() as $invariant => $rule) {
            try {
                if (!$this->{$invariant}()) {
                    $violations[$invariant] = $rule;
                }
            } catch (Exception $e) {
                $violations[$invariant] = $e->getMessage();
            }
        }

        return $violations;
    }

    private function computeInvariantHandler(string|callable $handlerFn): callable
    {
        if (!is_string($handlerFn)) {
            return $handlerFn;
        }

        return method_exists($this, $handlerFn)
            ? function (array $violations) use ($handlerFn): void {
                $this->{$handlerFn}($violations);
            }
            : function (array $violations): void {
                throw new InvariantViolation(
                    sprintf(
                        "Unable to create %s due %s",
                        basename(str_replace('\\', '/', static::class)),
                        implode(",", $violations),

                    )
                );
            };
    }
}
