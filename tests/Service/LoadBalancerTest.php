<?php

use PHPUnit\Framework\TestCase;
use Collection\Queue;
use Service\LoadBalancer;
use Collection\AutoScalingGroup;

/**
 * Class LoadBalancerTest
 */
final class LoadBalancerTest extends TestCase
{
    public function testConstruct()
    {
        $queue = new Queue(3);

        $loadBalancer = new LoadBalancer(new AutoScalingGroup(1));
        $class = new ReflectionClass('Service\LoadBalancer');

        $property = $class->getProperty('instanceGroup');
        $property->setAccessible(true);
        $this->assertEquals(new AutoScalingGroup(1), $property->getValue($loadBalancer));
    }
}