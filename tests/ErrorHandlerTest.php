<?php
declare(strict_types=1);

use Opravdin\AmoHook\AmoHook;
use PHPUnit\Framework\TestCase;

final class ErrorHandlerTest extends TestCase
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

    public function testWillTriggerErrorHandler(): void
    {
        $trigger = false;
        $callback = function ($data) use (&$trigger) {
            throw new \Exception('Testing!');
            return 'Run!';
        };
        AmoHook::build(self::$post)
            ->register('companies', 'delete', $callback)
            ->onError(function ($exception, $data, $entity, $action) use (&$trigger) {
                $trigger = true;
            })
            ->handle();
        $this->assertTrue($trigger);
    }
}