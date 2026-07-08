<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $size;
    public string $type;
    public bool $outline;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $variant = 'primary',
        string $size = 'md',
        string $type = 'button',
        bool $outline = false
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->type = $type;
        $this->outline = $outline;
    }

    /**
     * Get button classes based on variant, size, and outline
     */
    public function getClasses(): string
    {
        $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

        $variantClasses = [
            'primary' => $this->outline
                ? 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500'
                : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
            'secondary' => $this->outline
                ? 'border-2 border-gray-400 text-gray-700 hover:bg-gray-50 focus:ring-gray-500'
                : 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-500',
            'success' => $this->outline
                ? 'border-2 border-green-600 text-green-600 hover:bg-green-50 focus:ring-green-500'
                : 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
            'danger' => $this->outline
                ? 'border-2 border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500'
                : 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
            'warning' => $this->outline
                ? 'border-2 border-yellow-600 text-yellow-600 hover:bg-yellow-50 focus:ring-yellow-500'
                : 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
        ];

        $sizeClasses = [
            'xs' => 'px-2 py-1 text-xs',
            'sm' => 'px-3 py-1.5 text-sm',
            'md' => 'px-4 py-2 text-base',
            'lg' => 'px-6 py-3 text-lg',
            'xl' => 'px-8 py-4 text-xl',
        ];

        return $baseClasses . ' ' . ($variantClasses[$this->variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$this->size] ?? $sizeClasses['md']);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
