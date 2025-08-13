<?php
// gemini_api.php

function callGeminiAPI($reportText, $pastText = '', $userData = []) {
    // Replace with your actual Gemini API key
    $apiKey = 'YOUR_GEMINI_API_KEY';

    // Build prompt dynamically
    $prompt = "Analyze the following medical report and provide a JSON output with:
- risk_level (Low, Moderate, High),
- summary,
- diet,
- physical_activities.

You can consider user's vitals if available (height, weight, age).

Patient Info:
Age: " . ($userData['age'] ?? 'N/A') . ",
Height: " . ($userData['height'] ?? 'N/A') . " cm,
Weight: " . ($userData['weight'] ?? 'N/A') . " kg

Current Report:
$reportText

";

    if (!empty($pastText)) {
        $prompt .= "\n\nPast Report:\n$pastText\n\nCompare with the past report and identify any trends or changes.";
    }

    // Request payload
    $postData = [
        "contents" => [[
            "parts" => [["text" => $prompt]]
        ]]
    ];

    // Create request context
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($postData)
        ]
    ];

    $context = stream_context_create($options);
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return ['error' => 'Failed to connect to Gemini API'];
    }

    $result = json_decode($response, true);

    // Extract the text content
    $outputText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

    // Try parsing it as JSON
    $parsed = json_decode($outputText, true);

    // Fallback if Gemini returns raw text
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['raw_response' => $outputText];
    }

    return $parsed;
}
?>
