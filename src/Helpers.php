<?php

/**
 * Возвращает человекочитаемое название уровня энергии.
 *
 * @param string $level Уровень энергии
 * @return string
 */
function energyLabel(string $level): string
{
    return match ($level) 
    {
        'low' => 'Низкий',
        'medium' => 'Средний',
        'high' => 'Высокий',
        default => $level,
    };
}

/**
 * Возвращает иконку настроения по коду.
 *
 * @param string $mood Код настроения
 * @return string
 */
function moodIcon(string $mood): string
{
    return match ($mood)
    {
        'happy' => '😊',
        'calm' => '😌',
        'sad' => '😢',
        'angry' => '😠',
        'tired' => '😴',
        default => '🙂',
    };
}

/**
 * Возвращает название настроения по коду.
 *
 * @param string $mood Код настроения
 * @return string
 */
function moodLabel(string $mood): string
{
    return match ($mood) 
    {
        'happy' => 'Радостное',
        'calm' => 'Спокойное',
        'sad' => 'Грустное',
        'angry' => 'Злое',
        'tired' => 'Уставшее',
        default => $mood,
    };
}