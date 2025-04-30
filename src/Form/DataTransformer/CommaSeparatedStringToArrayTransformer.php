<?php
// src/Form/DataTransformer/CommaSeparatedStringToArrayTransformer.php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CommaSeparatedStringToArrayTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        return is_array($value) ? implode(', ', $value) : '';
    }

    public function reverseTransform($value): array
    {
        return array_map('trim', explode(',', $value));
    }
}
