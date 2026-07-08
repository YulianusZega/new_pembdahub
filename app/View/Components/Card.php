<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public ?string $title;
    public bool $shadow;
    public string $padding;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        bool $shadow = true,
        string $padding = 'normal'
    ) {
        $this->title = $title;
        $this->shadow = $shadow;
        $this->padding = $padding;
    }

    /**
     * Get card classes
     */
    public function getClasses(): string
    {
        $baseClasses = 'bg-white rounded-lg overflow-hidden';
        $shadowClass = $this->shadow ? 'shadow-md hover:shadow-lg transition-shadow duration-200' : '';

        $paddingClasses = [
            'none' => '',
            'sm' => 'p-3',
            'normal' => 'p-6',
            'lg' => 'p-8',
        ];

        return $baseClasses . ' ' . $shadowClass . ' ' . ($paddingClasses[$this->padding] ?? $paddingClasses['normal']);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}
