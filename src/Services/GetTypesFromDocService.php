<?php

declare(strict_types = 1);

namespace Szymon\CodeSniffer\Services;

class GetTypesFromDocService
{
    /**
     * @return string[]
     */
    public function getVarValueFromDoc(string $docContent): array
    {
        preg_match('/@var (\s*\S+\s*\|)*\s*\S+/', $docContent, $matches);

        if (empty($matches)) {
            return [];
        }

        $rawTypes = trim(str_replace('@var', '', $matches[0]));
        return $this->parserRawTypes($rawTypes);
    }


    /**
     * @return string[]
     */
    private function parserRawTypes(string $rawTypes): array
    {
        $types = [];

        $type = '';
        $nestedLevel = 0;
        for ($i = 0; $i < strlen($rawTypes); $i++) {
            $character = $rawTypes[$i];
            if ($character === '|' && $nestedLevel === 0) {
                $types[] = trim($type);
                $type = '';
                continue;
            }

            if ($character === '(') {
                $nestedLevel++;
            }

            if ($character === ')') {
                $nestedLevel--;
            }

            $type .= $character;
        }
        $types[] = trim($type);

        // replace all '?<type>' into '<type>' and 'null'
        foreach ($types as $i => $type) {
            if ($type[0] === '?') {
                $types[$i] = substr($type, 1);
                $types[] = 'null';
            }
        }

        $types = array_values(array_unique($types));
        sort($types);
        return $types;
    }
}