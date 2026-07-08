<?php

function print_steps_from($conv_id, $from_step = 270) {
    $path = "C:\\Users\\ACER\\.gemini\\antigravity\\brain\\{$conv_id}\\.system_generated\\logs\\transcript.jsonl";
    echo "\n=========================================\n";
    echo "=== STEPS FROM $from_step OF {$conv_id} ===\n";
    echo "=========================================\n";
    if (!file_exists($path)) {
        echo "File does not exist: {$path}\n";
        return;
    }
    
    $lines = file($path);
    if ($lines === false) {
        echo "Failed to read {$path}\n";
        return;
    }
    
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if (!$data) continue;
        
        $step = $data['step_index'] ?? 0;
        if ($step < $from_step) continue;
        
        $t = $data['type'] ?? '';
        $source = $data['source'] ?? '';
        $content = $data['content'] ?? '';
        
        if ($t === 'USER_INPUT') {
            echo "\n[Step {$step}] USER REQUEST:\n" . trim($content) . "\n";
        } elseif ($t === 'PLANNER_RESPONSE' && !empty($content)) {
            $trimmed = strlen($content) > 1000 ? substr($content, 0, 1000) . "\n... [TRUNCATED] ..." : $content;
            echo "\n[Step {$step}] MODEL RESPONSE:\n" . trim($trimmed) . "\n";
        }
    }
}

print_steps_from("626cf84c-af02-46c1-8ad9-982eca1fe92e", 270);



