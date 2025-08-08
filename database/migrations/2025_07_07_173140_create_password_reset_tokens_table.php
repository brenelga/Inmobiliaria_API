<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('password_reset_tokens', function (Blueprint $collection) {
    $collection->index('email');
    $collection->string('token');
    $collection->timestamp('created_at')->nullable();
});