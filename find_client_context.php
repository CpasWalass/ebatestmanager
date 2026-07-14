<?php
$lines = file('C:\Users\user\.gemini\antigravity\brain\bdad05d8-9e30-471f-8386-404ab675d36f\.system_generated\logs\transcript.jsonl');
foreach ($lines as $line) {
    $data = json_decode($line, true);
    if ($data && isset($data['type']) && $data['type'] === 'USER_INPUT') {
        if (stripos($data['content'], 'client') !== false || stripos($data['content'], 'gestion') !== false) {
            echo "USER: " . substr($data['content'], 0, 500) . "\n\n";
        }
    }
    if ($data && isset($data['type']) && $data['type'] === 'PLANNER_RESPONSE') {
        if (stripos($data['content'], 'client') !== false) {
            // just to see our responses if needed
        }
    }
}
