<?php

class TestException
{
    public $variableWithNotTypeAndDoc;

    /**
     * @var string
     */
    public string $variableWithTypeAndDoc;

    /**
     * @var string
     */
    public string $variableWithType;

    /**
     * @var string
     */
    public $variableWithDoc;

    public function method_a(){}

    private static $staticVariableWithNotTypeAndDoc;

    /**
     * @var string
     */
    private static string $staticVariableWithTypeAndDoc;

    /**
     * @var string
     */
    private static string $staticVariableWithType;

    /**
     * @var string
     */
    private static $staticVariableWithDoc;

    public function method_b(){}

    private const constVariableWithNotTypeAndDoc = '';

    /**
     * @var string
     */
    private const string constVariableWithTypeAndDoc = '';

    /**
     * @var string
     */
    private const string constVariableWithType = '';

    /**
     * @var string
     */
    private const constVariableWithDoc = '';

    public function method_c(){}

    private readonly const readonlyConstVariableWithNotTypeAndDoc = '';

    /**
     * @var string
     */
    private readonly const string readonlyConstVariableWithTypeAndDoc = '';

    /**
     * @var string
     */
    private readonly const string readonlyConstVariableWithType = '';

    /**
     * @var string
     */
    private readonly const readonlyConstVariableWithDoc = '';
}
