<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DeployTest extends TestCase
{
    public function test_deploy_adapter_interface_exists(): void
    {
        $this->assertTrue(interface_exists('Tavp\Deploy\DeployAdapter'));
    }

    public function test_hestiacp_adapter_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\HestiaCPAdapter'));
    }

    public function test_vps_adapter_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\VpsAdapter'));
    }

    public function test_docker_deploy_adapter_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\DockerDeployAdapter'));
    }

    public function test_cpanel_adapter_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\CpanelAdapter'));
    }

    public function test_deploy_manager_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\DeployManager'));
    }

    public function test_hestia_deploy_script_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Deploy\HestiaDeployScript'));
    }

    public function test_hestia_deploy_script_generates_valid_bash(): void
    {
        $script = \Tavp\Deploy\HestiaDeployScript::generate([
            'domain' => 'test.example.com',
            'user' => 'testuser',
            'php_version' => '8.3',
        ]);

        $this->assertStringContainsString('#!/bin/bash', $script);
        $this->assertStringContainsString('v-add-web-domain', $script);
        $this->assertStringContainsString('v-add-database-host', $script);
    }
}
