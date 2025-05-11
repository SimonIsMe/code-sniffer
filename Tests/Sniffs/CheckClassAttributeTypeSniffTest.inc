<?php

class TestException
{
    /** @var string something */
    public array $value;

    /** @var string[] something */
    public array $arrayValue;

    /** @var string[] | null something */
    public ?array $arrayNullableValue;

    /** @var ?(string[]) something */
    public ?array $arrayNullableValue_2;

    /** @var int|null|?string|float */
    private const string | int | null AAA = 123;

    /** @var int|null|?string|float */
    private const string|float|int|null BBB;
    const string | int | null CCC;

    private static string $AAAAAAA;

    static private string $AAAAAAA2;

    /** @var int[] something */
    public array $value123;

    /** @var ?object something */
    public object | null $value133323;

    /** @var ?object something */
    public ?object $value1333233;

    /** @var object | null something */
    public ?object $value13332334;

    private mixed $value2;

    public \Exception $form;

    /**
     * @var \RuntimeException
     */
    public $runtimeException;

    public function method(string $input): void
    {
        $a = 0;
    }

    public \InvalidArgumentException $invalidArgumentException;
}
