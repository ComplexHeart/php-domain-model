<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use Exception;

/**
 * Trait HasInvariants
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
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
            if (str_starts_with($invariant, 'invariant') && $invariant !== 'invariants') {
                $invariants[$invariant] = str_replace(
                    'invariant ',
                    '',
                    strtolower(
                        preg_replace('/[A-Z]([A-Z](?![a-z]))*/', ' $0', $invariant)
                    )
                );
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
     * @param  callable|null  $onFail
     *
     * @return void
     */
    private function check(callable $onFail = null): void
    {
        $handler = 'invariantHandler';

        $violations = [];
        foreach (static::invariants() as $invariant => $rule) {
            try {
                if (!call_user_func_array([$this, $invariant], [])) {
                    $violations[$invariant] = $rule;
                }
            } catch (Exception $e) {
                $violations[$invariant] = $e->getMessage();
            }
        }

        if (!empty($violations)) {
            if (is_null($onFail)) {
                $customizedHandler = function (array $violations) use ($handler): void {
                    call_user_func_array([$this, $handler], [$violations]);
                };

                $defaultHandler = function (array $violations): void {
                    throw new InvariantViolation(
                        sprintf(
                            "Unable to create %s due %s",
                            basename(str_replace('\\', '/', static::class)),
                            implode(",", $violations),

                        )
                    );
                };

                $onFail = (method_exists($this, $handler))
                    ? $customizedHandler
                    : $defaultHandler;
            }

            $onFail($violations);
        }
    }
}
