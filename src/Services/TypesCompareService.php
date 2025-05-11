<?php

declare(strict_types = 1);

namespace Szymon\CodeSniffer\Services;

class TypesCompareService
{
    /**
     * @var string[]
     */
    private array $basicTypes = [
        'bool',
        'double',
        'float',
        'int',
        'null',
        'object',
        'string',
    ];

    public function areIdentical(array $declaredTypes, array $documentedTypes): bool
    {
        $basicTypesFromDeclaredTypes = array_intersect($this->basicTypes, $declaredTypes);
        $basicTypesFromDocumentedTypes = array_intersect($this->basicTypes, $documentedTypes);

        $nonBasicTypesFromDeclaredTypes = array_diff($declaredTypes, $this->basicTypes);
        $nonBasicTypesFromDocumentedTypes = array_diff($documentedTypes, $this->basicTypes);

        if (in_array('array', $nonBasicTypesFromDeclaredTypes)) {
            $nonBasicTypesFromDeclaredTypes = array_diff($nonBasicTypesFromDeclaredTypes, ['array']);
            $nonBasicTypesFromDocumentedTypes = $this->removeAdvancedArrays($nonBasicTypesFromDocumentedTypes);
        }

        if (empty($nonBasicTypesFromDeclaredTypes) && empty($nonBasicTypesFromDocumentedTypes)) {
            return json_encode($basicTypesFromDeclaredTypes) === json_encode($basicTypesFromDocumentedTypes);
        }

        return false;
    }

    public function removeAdvancedArrays(array $documentedTypes): array
    {
        $toReturn = [];
        foreach ($documentedTypes as $documentedType) {
            if (strpos($documentedType, ']') === false && $documentedType !== 'array') {
                $toReturn[] = $documentedType;
            }
        }

        return $toReturn;
    }
}