<?php
declare(strict_types=1);

use Opravdin\AmoHook;
use PHPUnit\Framework\TestCase;

final class ParsingTest extends TestCase
{
    static $target = [
            [
                'action' => 'delete',
                'entity' => 'companies'
                // TODO: add data testing
            ]
        ];

    public function testCanBeCreatedFromPost(): void
    {
        $post = [
            'contacts' => '{"delete":[{"id":"12345","type":"company"}]}',
            'account' => '{"subdomain":"test","id":"123","_links":{"self":"https://test.amocrm.ru"}}'
        ];
        $data = AmoHook::build($post)->get();
        $this->assertEquals(
            $data[0]['action'],
            self::$target[0]['action']
        );
        $this->assertEquals(
            $data[0]['entity'],
            self::$target[0]['entity']
        );
    }

    public function testCanBeCreatedFromParsedPost(): void
    {
        $post = [
            'contacts' => [
                "delete" => [
                        [
                            "id" => "12345",
                            "type" => "company"
                        ]
                    ]
            ],
            'account' => [
                "subdomain" => "test",
                "id" => "123",
                "_links" => [
                    "self" => "https://test.amocrm.ru"
                    ]
            ]
            
        ];
        $data = AmoHook::build($post)->get();
        $this->assertEquals(
            $data[0]['action'],
            self::$target[0]['action']
        );
        $this->assertEquals(
            $data[0]['entity'],
            self::$target[0]['entity']
        );
    }

    public function testCanBeCreatedFromObjectPost(): void
    {
        $post = (object) [
            'contacts' => [
                "delete" => [
                        [
                            "id" => "12345",
                            "type" => "company"
                        ]
                    ]
            ],
            'account' => [
                "subdomain" => "test",
                "id" => "123",
                "_links" => [
                    "self" => "https://test.amocrm.ru"
                    ]
            ]
            
        ];
        $data = AmoHook::fromObject($post)->get();
        $this->assertEquals(
            $data[0]['action'],
            self::$target[0]['action']
        );
        $this->assertEquals(
            $data[0]['entity'],
            self::$target[0]['entity']
        );
    }
}