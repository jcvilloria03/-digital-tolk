<?php

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testWillExpireAt()
    {
        // Mocking Carbon class for testing
        $carbonMock = $this->getMockBuilder(\Carbon\Carbon::class)
            ->setMethods(['parse', 'diffInHours', 'addMinutes', 'addHours', 'subHours', 'format'])
            ->getMock();

        // Mock input parameters
        $due_time = '2024-02-05 12:00:00';
        $created_at = '2024-02-04 12:00:00';

        // Set expectations for parse method
        $carbonMock::expects($this->exactly(2))
            ->method('parse')
            ->willReturn($carbonMock);

        // Set expectations for diffInHours method
        $carbonMock::expects($this->once())
            ->method('diffInHours')
            ->willReturn(80); // adjust the return value based on your logic

        // Set expectations for addMinutes, addHours, and subHours methods
        $carbonMock::expects($this->exactly(3))
            ->method('addMinutes')
            ->willReturn($carbonMock);
        $carbonMock::expects($this->exactly(1))
            ->method('addHours')
            ->willReturn($carbonMock);
        $carbonMock::expects($this->exactly(1))
            ->method('subHours')
            ->willReturn($carbonMock);

        // Set expectations for format method
        $carbonMock::expects($this->once())
            ->method('format')
            ->willReturn('2024-02-05 15:30:00'); // adjust the return value based on your logic

        // Replace the Carbon class with the mocked version
        $this->setReflectionPropertyValue(\TeHelper::class, 'carbon', $carbonMock);

        // Create an instance of YourClass
        $yourClass = new TeHelper();

        // Call the method to test
        $result = $yourClass->willExpireAt($due_time, $created_at);

        // Assertions
        $this->assertEquals('2024-02-05 15:30:00', $result); // adjust the expected value based on your logic
    }

    /**
     * Set the value of a private or protected property using reflection.
     *
     * @param string $class  The fully qualified class name.
     * @param string $property  The name of the property.
     * @param mixed $value  The value to set.
     */
    protected function setReflectionPropertyValue($class, $property, $value)
    {
        $reflectionClass = new ReflectionClass($class);
        $property = $reflectionClass->getProperty($property);
        $property->setAccessible(true);
        $property->setValue(null, $value);
    }
}