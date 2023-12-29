# Value Objects

> A small simple object, like money or a date range, whose equality isn't based on identity.\
> -- Martin Fowler

Creating a Value Object is quite easy you only need to use the Trait `IsValueObject` this will
add the `HasAttributes`, `HasInvariants` and the `HasEquality` Traits. In addition, you could
use the `ValueObject` interface to expose the `values` and `equals` methods.

The following example illustrates the implementation of these components.

```php
use ComplexHeart\Contracts\Domain\Model\ValueObject;use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Class Color
 * @method string value()
 */
final class Color implements ValueObject 
{
    use IsValueObject;
    
    private string $value;
 
    public function __construct(string $value) 
    {
        $this->initialize(['value' => $value]);
    }
    
    protected function invariantValueMustBeHexadecimal(): bool 
    {
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $this->value) === 1;
    }
    
    public function __toString(): string 
    {
        return $this->value();
    }
}

$red = new Color('#ff0000');
$red->equals(new Color('#00ff00')); // false
$red->value(); // #ff0000
$magenta = new Color('ff00ff'); // Exception InvariantViolation: Value must be hexadecimal.
```