<?php
declare(strict_types=1);

use Opravdin\AmoHook;
use PHPUnit\Framework\TestCase;

final class ChainingTest extends TestCase
{
    static $target = [
            [
                'action' => 'delete',
                'entity' => 'companies'
                // TODO: add data testing
            ]
        ];
    static $post = [
        'contacts' => '{"delete":[{"id":"12345","type":"company"}]}',
        'account' => '{"subdomain":"test","id":"123","_links":{"self":"https://test.amocrm.ru"}}'
    ];

    public function testWillTriggerCorrectCallback(): void
    {
        $trigger = false;
        $callback = function ($data) use (&$trigger) {
            $trigger = true;
            return 'Run!';
        };
        AmoHook::build(self::$post)
            ->register('companies', 'delete', $callback)
            ->handle();
        $this->assertTrue($trigger);
    }

    public function testWillNotTriggerWrongCallback(): void
    {
        $trigger = false;
        $callback = function ($data) use (&$trigger) {
            $trigger = true;
            return 'Run!';
        };
        AmoHook::build(self::$post)
            // Wrong method
            ->register('companies', 'update', $callback)
            // Wrong entity
            ->register('contacts', 'delete', $callback)
            // Wildcard wrong action
            ->register('companies', 'update', $callback)
            ->handle();
        $this->assertFalse($trigger);
    }

    public function testWillTriggerCorrectWildcardCallback(): void
    {
        $trigger = 0;
        $callback = function ($data) use (&$trigger) {
            $trigger += 1;
            return 'Run!';
        };
        AmoHook::build(self::$post)
            ->register('companies', 'any', $callback)
            ->register('any', 'any', $callback)
            ->register('any', 'delete', $callback)
            ->handle();
        // Should be incremented 3 times
        $this->assertEquals(3, $trigger);
    }

    public function testWillTriggerCorrectMultipleCallback(): void
    {
        $trigger = 0;
        $callback = function ($data) use (&$trigger) {
            $trigger += 1;
            return 'Run!';
        };
        AmoHook::build(self::$post)
            ->register(['companies', 'leads'], 'delete', $callback)
            ->register(['companies', 'leads'], ['delete', 'update'], $callback)
            ->register('companies', ['delete', 'update'], $callback)
            ->handle();
        // Should be incremented 3 times
        $this->assertEquals(3, $trigger);
    }
}