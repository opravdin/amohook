<?php
declare(strict_types=1);

use Opravdin\AmoHook;
use PHPUnit\Framework\TestCase;

final class AfterHandlerTest extends TestCase
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

    public function testWillTriggerAfterHandler(): void
    {
        $trigger = false;
        $result = null;
        $callback = function ($data) use (&$trigger) {
            return 'test';
        };
        AmoHook::build(self::$post)
            ->register('companies', 'delete', $callback)
            ->after(function ($res, $data) use (&$trigger, &$result) {
                $trigger = true;
                $result = $res;
            })
            ->handle();
        $this->assertTrue($trigger);
        $this->assertEquals('test', $result);
    }
}