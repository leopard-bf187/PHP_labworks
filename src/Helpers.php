<?php

function Hetemule(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function SortLink(string $field, string $currentField, string $currentOrder, string $view): string
{
    $newOrder = 'asc';

    if ($field === $currentField && $currentOrder === 'asc') 
        $newOrder = 'desc';

    return '?page=list&sort=' . urlencode($field) . '&order=' . urlencode($newOrder) . '&view=' . urlencode($view);
}

function MoodLabel(string $mood): string
{
    return match ($mood) 
    {
        'happy' => 'Радостное',
        'calm' => 'Спокойное',
        'sad' => 'Грустное',
        'angry' => 'Злое',
        'tired' => 'Уставшее',
        default => $mood
    };
}

function EnergyLabel(string $level): string
{
    return match ($level) 
    {
        'low' => 'Низкий',
        'medium' => 'Средний',
        'high' => 'Высокий',
        default => $level
    };
}

function MoodIcon(string $mood): string
{
    return match ($mood)
    {
        'happy' => '😊',
        'calm' => '😌',
        'sad' => '😢',
        'angry' => '😠',
        'tired' => '😴',
        default => '🙂'
    };
}