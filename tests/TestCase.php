<?php
namespace Tests;

use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            // We can also customise where our factories live too if we want:
            $namespace = 'Database\\Factories\\';

            // Here we are getting the model name from the class namespace
            $modelName = Str::afterLast($modelName, '\\');

            // Finally we'll build up the full class path where
            // Laravel will find our model factory
            return $namespace.$modelName.'Factory';
        });
    }
}
