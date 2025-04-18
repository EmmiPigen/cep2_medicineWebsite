<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriorityExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('priority_class', [$this, 'priorityClass']),
        ];
    }

    public function priorityClass(string $priority): string
    {
        return match ($priority) {
            'Høj' => 'high-priority',
            'Middel' => 'mid-priority',
            'Lav' => 'low-priority',
            default => '',
        };
    }
}
