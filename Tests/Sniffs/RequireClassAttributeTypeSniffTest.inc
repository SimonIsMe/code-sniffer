<?php

class TestException
{
    /** @var string something */
    public array $value;

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

    private mixed $value2;

    public \Exception $form;

    public function method($input): void
    {
        $a = 0;
    }
}
