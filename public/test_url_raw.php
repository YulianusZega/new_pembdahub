<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing URL helper directly:\n";
echo "Test 1: " . url('/forum/poll/1/test') . "\n";
echo "Test 2: " . url('/forum/poll/1/vote') . "\n";
echo "Test 3: " . route('forum.poll.vote', ['option' => 1]) . "\n";
