<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(Tests\TestCase::class, DatabaseTransactions::class)->in('Feature', 'Unit');
