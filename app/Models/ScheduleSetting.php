<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting by key with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $decoded = json_decode($setting->value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
    }

    /**
     * Set / update a setting by key.
     */
    public static function set(string $key, mixed $value): self
    {
        $val = is_array($value) ? json_encode($value) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $val]
        );
    }

    /**
     * Get full schedule configuration with default school hours.
     */
    public static function getScheduleConfig(): array
    {
        return [
            'lesson_duration' => (int) static::get('lesson_duration', 40), // 1 JP = 40 menit
            'start_time' => (string) static::get('start_time', '07:00'),
            'end_time' => (string) static::get('end_time', '14:00'),
            'friday_end_time' => (string) static::get('friday_end_time', '11:15'),
            'recesses' => (array) static::get('recesses', [
                ['name' => 'Istirahat 1', 'start' => '09:40', 'end' => '10:05'],
                ['name' => 'Istirahat 2 (Dhuhur)', 'start' => '11:45', 'end' => '12:30'],
            ]),
            'friday_recesses' => (array) static::get('friday_recesses', [
                ['name' => 'Istirahat Pagi', 'start' => '09:00', 'end' => '09:20'],
            ]),
        ];
    }

    /**
     * Compute lesson slots and recesses for a specific day.
     */
    public static function getDayTimeline(string $dayOfWeek = 'monday'): array
    {
        $config = static::getScheduleConfig();
        $isFriday = ($dayOfWeek === 'friday');

        $startTimeStr = $config['start_time'];
        $endTimeStr = $isFriday ? $config['friday_end_time'] : $config['end_time'];
        $duration = $config['lesson_duration'];
        $recesses = $isFriday ? $config['friday_recesses'] : $config['recesses'];

        $current = Carbon::createFromFormat('H:i', $startTimeStr);
        $end = Carbon::createFromFormat('H:i', $endTimeStr);

        $timeline = [];
        $periodIndex = 1;

        while ($current->lt($end)) {
            $currentStr = $current->format('H:i');

            // Check if current time falls in a recess
            $hitRecess = null;
            $recessEnd = null;
            foreach ($recesses as $r) {
                if (empty($r['start']) || empty($r['end'])) {
                    continue;
                }
                $rStart = Carbon::createFromFormat('H:i', $r['start']);
                $rEnd = Carbon::createFromFormat('H:i', $r['end']);
                if ($currentStr === $r['start'] || ($current->gte($rStart) && $current->lt($rEnd))) {
                    $hitRecess = $r;
                    $recessEnd = $rEnd;
                    break;
                }
            }

            if ($hitRecess && $recessEnd) {
                $rStart = Carbon::createFromFormat('H:i', $hitRecess['start']);
                $timeline[] = [
                    'type' => 'recess',
                    'name' => $hitRecess['name'],
                    'start_time' => $hitRecess['start'],
                    'end_time' => $hitRecess['end'],
                    'duration' => $rStart->diffInMinutes($recessEnd),
                ];
                $current = $recessEnd;
                continue;
            }

            // Normal lesson period (JP)
            $next = $current->copy()->addMinutes($duration);
            if ($next->gt($end)) {
                break;
            }

            $currentPeriodNum = $periodIndex++;
            $timeline[] = [
                'type' => 'period',
                'period' => $currentPeriodNum,
                'name' => 'Jam ke-'.$currentPeriodNum,
                'start_time' => $current->format('H:i'),
                'end_time' => $next->format('H:i'),
                'duration' => $duration,
            ];

            $current = $next;
        }

        return $timeline;
    }
}
