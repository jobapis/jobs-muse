<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Muse;
use Mockery as m;

class MuseTest extends \PHPUnit_Framework_TestCase
{
    private $clientClass = 'JobBrander\Jobs\Client\Providers\AbstractProvider';
    private $collectionClass = 'JobBrander\Jobs\Client\Collection';
    private $jobClass = 'JobBrander\Jobs\Client\Job';

    public function setUp()
    {
        $this->client = new Muse();
    }

    public function testItWillUseJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testItWillUseGetHttpVerb()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testListingPath()
    {
        $path = $this->client->getListingsPath();

        $this->assertEquals('results', $path);
    }

    public function testUrlIncludesPageWhenProvided()
    {
        $page = uniqid();
        $param = 'page='.$page;

        $url = $this->client->setPage($page)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesPageWhenNotProvided()
    {
        $param = 'page=';

        $url = $this->client->setPage(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesDescendingWhenProvided()
    {
        $descending = uniqid();
        $param = 'descending='.$descending;

        $url = $this->client->setDescending($descending)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesDescendingWhenNotProvided()
    {
        $param = 'descending=';

        $url = $this->client->setDescending(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesCategoryWhenProvided()
    {
        $string = uniqid().' '.uniqid();
        $param = urlencode('job_category[]').'='.urlencode($string);

        $url = $this->client->setCategory($string)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesCategoryWhenNotProvided()
    {
        $param = urlencode('job_category[]').'=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesCompanyWhenProvided()
    {
        $string = uniqid().' '.uniqid();
        $param = urlencode('company[]').'='.urlencode($string);

        $url = $this->client->setCompany($string)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesCompanyWhenNotProvided()
    {
        $param = urlencode('company[]').'=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLevelWhenProvided()
    {
        $string = uniqid().' '.uniqid();
        $param = urlencode('job_level[]').'='.urlencode($string);

        $url = $this->client->setLevel($string)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLevelWhenNotProvided()
    {
        $param = urlencode('job_level[]').'=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLocationWhenProvided()
    {
        $location = uniqid().' '.uniqid();
        $param = urlencode('job_location[]').'='.urlencode($location);

        $url = $this->client->setLocation($location)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLocationWhenNotProvided()
    {
        $param = urlencode('job_location[]').'=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testItCanCreateJobFromPayload()
    {
        $payload = $this->createJobArray();

        $results = $this->client->createJobObject($payload);

        $this->assertEquals($payload['title'], $results->title);
        $this->assertEquals($payload['company_name'], $results->company);
        $this->assertEquals('https://www.themuse.com'.$payload['apply_link'], $results->url);
        $this->assertEquals($payload['id'], $results->sourceId);
    }

    public function testItCanCreateJobArrayFromPayloadWithMultipleLocations()
    {
        $locations_count = rand(2,20);
        $payload = $this->createJobArrayWithMultipleLocations($locations_count);

        $results = $this->client->createJobArray($payload);

        $this->assertCount($locations_count, $results);
    }

    public function testItCanConnect()
    {
        $provider = $this->getProviderAttributes();

        for ($i = 0; $i < $provider['jobs_count']; $i++) {
            $payload['results'][] = $this->createJobArray();
        }

        $responseBody = json_encode($payload);

        $job = m::mock($this->jobClass);
        $job->shouldReceive('setSource')->with($provider['source'])
            ->times($provider['jobs_count'])->andReturnSelf();

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        $this->assertInstanceOf($this->collectionClass, $results);
        $this->assertCount($provider['jobs_count'], $results);
    }

    private function createJobArray() {
        return [
            'id' => uniqid(),
            'title' => uniqid(),
            'company_name' => uniqid(),
            'apply_link' => uniqid(),
            'locations' => [uniqid()],
            'creation_date' => '2015-08-'.rand(1,30),
        ];
    }

    private function createJobArrayWithMultipleLocations($count) {
        $cc = 0;

        while ($cc < $count) {
            $locations[] = uniqid();
            $cc++;
        }

        return [
            'id' => uniqid(),
            'title' => uniqid(),
            'company_name' => uniqid(),
            'apply_link' => uniqid(),
            'locations' => $locations,
            'creation_date' => '2015-08-'.rand(1,30),
        ];
    }

    private function getProviderAttributes($attributes = [])
    {
        $defaults = [
            'path' => uniqid(),
            'format' => 'json',
            'source' => uniqid(),
            'params' => [uniqid()],
            'jobs_count' => rand(2,10),
        ];
        return array_replace($defaults, $attributes);
    }
}
