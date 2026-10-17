<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemStatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $totalMemMB = 0;
        $usedMemMB = 0;
        $freeMemMB = 0;
        $memUsagePercent = 0;

        // Read Memory info from /proc/meminfo on Linux
        if (file_exists('/proc/meminfo')) {
            $memInfo = @file_get_contents('/proc/meminfo') ?: '';
            preg_match('/MemTotal:\s+(\d+)/', $memInfo, $matchesTotal);
            preg_match('/MemAvailable:\s+(\d+)/', $memInfo, $matchesAvail);
            $totalKB = (int)($matchesTotal[1] ?? 0);
            $freeKB = (int)($matchesAvail[1] ?? 0);
            $usedKB = $totalKB - $freeKB;
            $totalMemMB = (int)round($totalKB / 1024);
            $usedMemMB = (int)round($usedKB / 1024);
            $freeMemMB = (int)round($freeKB / 1024);
            $memUsagePercent = (int)round(($usedKB / max(1, $totalKB)) * 100);
        } else {
            // Fallback for non-Linux
            $totalMemMB = 8192;
            $freeMemMB = 4096;
            $usedMemMB = 4096;
            $memUsagePercent = 50;
        }

        // Read CPU info
        $cpuModel = 'Standard Processor';
        $cpus = 1;
        if (file_exists('/proc/cpuinfo')) {
            $cpuInfo = @file_get_contents('/proc/cpuinfo') ?: '';
            preg_match_all('/model name\s+:\s+(.+)/', $cpuInfo, $cpuMatches);
            if (!empty($cpuMatches[1])) {
                $cpuModel = trim($cpuMatches[1][0]);
                $cpus = count($cpuMatches[1]);
            }
        }

        // Read Uptime
        $uptime = 0;
        if (file_exists('/proc/uptime')) {
            $uptimeInfo = @file_get_contents('/proc/uptime') ?: '';
            $uptime = (int)explode(' ', $uptimeInfo)[0];
        }

        $loadAvg = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];

        $specs = [
            'platform' => php_uname('s') === 'Linux' ? 'Linux' : (php_uname('s') === 'Darwin' ? 'macOS' : 'Windows Server'),
            'arch' => php_uname('m'),
            'cpus' => $cpus,
            'cpuModel' => $cpuModel,
            'totalMemMB' => $totalMemMB,
            'usedMemMB' => $usedMemMB,
            'freeMemMB' => $freeMemMB,
            'memUsagePercent' => $memUsagePercent,
            'nodeVersion' => 'PHP ' . PHP_VERSION,
            'uptimeSeconds' => $uptime,
            'loadAverage' => $loadAvg,
        ];

        return response()->json([
            'success' => true,
            'message' => 'System specifications retrieved successfully',
            'data' => $specs,
        ]);
    }
}
