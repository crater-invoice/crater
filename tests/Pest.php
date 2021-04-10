<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');
