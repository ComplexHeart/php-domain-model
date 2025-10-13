<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use Throwable;

use function Lambdish\Phunctional\map;

/**
 * Trait HasInvariants
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasInvariants
{
    /**
     * Static property to keep cached invariants list to optimize performance.
     *
     * @var array<string, string[]>
     */
    protected static $_invariantsCache = [];

    /**
     * Retrieve the object invariants.
     *
     * @return string[]
     */
    final public static function invariants(): array
    {
        if (array_key_exists(static::class, static::$_invariantsCache) === false) {
            $invariants = [];
            foreach (get_class_methods(static::class) as $invariant) {
                if (str_starts_with($invariant, 'invariant') && !in_array(
                    $invariant,
                    ['invariants', 'invariantHandler']
                )) {
                    $invariantRuleName = preg_replace('/[A-Z]([A-Z](?![a-z]))*/', ' $0', $invariant);
                    if (is_null($invariantRuleName)) {
                        continue;
                    }

                    $invariants[$invariant] = str_replace('invariant ', '', strtolower($invariantRuleName));
                }
            }

            static::$_invariantsCache[static::class] = $invariants;
        }

        return static::$_invariantsCache[static::class];
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
     *  fn(array<string, Throwable>) => void
     *
     * @param  string|callable  $onFail
     * @param  string  $exception
     *
     * @return void
     */
    private function check(
        string|callable $onFail = 'invariantHandler',
        string $exception = InvariantViolation::class
    ): void {
        $violations = $this->computeInvariantViolations($exception);
        if (!empty($violations)) {
            call_user_func_array($this->computeInvariantHandler($onFail, $exception), [$violations]);
        }
    }

    /**
     * Computes the list of invariant violations.
     *
     * @param  string  $exception
     *
     * @return array<string, Throwable>
     */
    private function computeInvariantViolations(string $exception): array
    {
        $violations = [];
        foreach (static::invariants() as $invariant => $rule) {
            try {
                if (!$this->{$invariant}()) {
                    /** @var array<string, Throwable> $violations */
                    $violations[$invariant] = new $exception($rule);
                }
            } catch (Throwable $e) {
                /** @var array<string, Throwable> $violations */
                $violations[$invariant] = $e;
            }
        }

        return $violations;
    }

    private function computeInvariantHandler(string|callable $handlerFn, string $exception): callable
    {
        if (!is_string($handlerFn)) {
            return $handlerFn;
        }

        return method_exists($this, $handlerFn)
            ? function (array $violations) use ($handlerFn, $exception): void {
                $this->{$handlerFn}($violations, $exception);
            }
        : function (array $violations) use ($exception): void {
            if (count($violations) === 1) {
                throw array_shift($violations);
            }

            throw new $exception( // @phpstan-ignore-line
                sprintf(
                    "Unable to create %s due: %s",
                    basename(str_replace('\\', '/', static::class)),
                    implode(", ", map(fn (Throwable $e): string => $e->getMessage(), $violations)),
                )
            );
        };
    }
}
