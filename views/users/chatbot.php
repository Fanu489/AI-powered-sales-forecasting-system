<?php
// chatbot.php

header('Content-Type: application/json');

// Check if message is set
if (!isset($_POST['message'])) {
    echo json_encode(['reply' => "🤖 Please enter a message."]);
    exit;
}

// Sanitize and normalize user input
$message = strtolower(trim($_POST['message']));
$defaultReply = "🤖 Sorry, I didn't get that. Try asking about 'uploading data', 'resetting password', or 'getting report'.";
$reply = $defaultReply;

// Uploading data
$uploadKeywords = ['upload', 'uploading data', 'submit file', 'upload csv', 'how to upload'];
foreach ($uploadKeywords as $keyword) {
    if (str_contains($message, $keyword)) {
        $reply = "📤 To upload data, go to the 'Upload Data' page from the sidebar. Choose your CSV file and click 'Submit'.";
        break;
    }
}

// Only continue checking if default reply is still active
if ($reply === $defaultReply) {
    $resetKeywords = ['reset password', 'forgot password', 'change password', 'resetting password'];
    foreach ($resetKeywords as $keyword) {
        if (str_contains($message, $keyword)) {
            $reply = "🔐 To reset your password, go to your 'Profile' and select 'Change Password'. Follow the steps provided.";
            break;
        }
    }
}

if ($reply === $defaultReply) {
    $reportKeywords = ['report', 'generate report', 'view report'];
    foreach ($reportKeywords as $keyword) {
        if (str_contains($message, $keyword)) {
            $reply = "📊 To generate or view a report, click on the 'Reports' tab and select your desired date range or product filter.";
            break;
        }
    }
}

if ($reply === $defaultReply) {
    $analyticsKeywords = ['analytics', 'view analytics', 'sales trends'];
    foreach ($analyticsKeywords as $keyword) {
        if (str_contains($message, $keyword)) {
            $reply = "📈 Visit the 'Analytics' section to see graphs, trends, and sales predictions based on your uploaded data.";
            break;
        }
    }
}

if ($reply === $defaultReply && (str_contains($message, 'settings') || str_contains($message, 'change settings'))) {
    $reply = "⚙️ You can change your preferences and configurations from the 'Settings' page in the sidebar.";
}

if ($reply === $defaultReply && (str_contains($message, 'help') || str_contains($message, 'support'))) {
    $reply = "🆘 I'm here to help! Try asking about 'uploading data', 'generating reports', 'resetting password', or 'analytics'.";
}

// Send the reply as JSON
echo json_encode(['reply' => $reply]);
