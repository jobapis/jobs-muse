<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Queries\MuseQuery;
use Mockery as m;

class MuseQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MuseQuery
     */
    public $query;

    public function setUp()
    {
        $this->query = new MuseQuery();
    }

    public function testItAddsDefaultAttributes()
    {
        $this->assertEquals('1', $this->query->get('page'));
    }

    public function testItCanGetBaseUrl()
    {
        $this->assertEquals(
            'https://api-v2.themuse.com/jobs',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetKeyword()
    {
        $keyword = uniqid();
        $this->query->set('category', $keyword);
        $this->assertEquals($keyword, $this->query->getKeyword());
    }

    public function testItCanAddAttributesToUrl()
    {
        $keyword = uniqid();
        $this->query->set('category', $keyword);
        $url = $this->query->getUrl();
        $this->assertContains('category=', $url);
        $this->assertContains('page=', $url);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenSettingInvalidAttribute()
    {
        $this->query->set(uniqid(), uniqid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenGettingInvalidAttribute()
    {
        $this->query->get(uniqid());
    }

    public function testItSetsAndGetsValidAttributes()
    {
        $attributes = [
            'api_key' => uniqid(),
            'company' => uniqid(),
            'location' => uniqid(),
        ];

        foreach ($attributes as $key => $value) {
            $this->query->set($key, $value);
        }

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $this->query->get($key));
        }
    }
}
