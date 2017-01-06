<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\MuseProvider;
use JobApis\Jobs\Client\Queries\MuseQuery;
use Mockery as m;

class MuseProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MuseProvider
     */
    public $client;

    public function setUp()
    {
        $this->query = m::mock('JobApis\Jobs\Client\Queries\MuseQuery');

        $this->client = new MuseProvider($this->query);
    }

    public function testItCanGetDefaultResponseFields()
    {
        $fields = [
            'levels', // array
            'locations', // array
            'tags', // array
            'categories', // array
            'publication_date',
            'short_name',
            'refs', // array
            'contents',
            'type',
            'model_type',
            'company', // array
            'id',
            'name',
        ];
        $this->assertEquals($fields, $this->client->getDefaultResponseFields());
    }

    public function testItCanGetListingsPath()
    {
        $this->assertEquals('results', $this->client->getListingsPath());
    }

    public function testItCanCreateJobObjectFromPayload()
    {
        $payload = $this->createJobArray();

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['name'], $results->getTitle());
        $this->assertEquals($payload['contents'], $results->getDescription());
        $this->assertEquals($payload['refs']['landing_page'], $results->getUrl());
    }

    /**
     * Integration test for the client's getJobs() method.
     */
    public function testItCanGetJobs()
    {
        $options = [
            'category' => uniqid(),
            'location' => uniqid(),
            'api_key' => uniqid(),
        ];

        $guzzle = m::mock('GuzzleHttp\Client');

        $query = new MuseQuery($options);

        $client = new MuseProvider($query);

        $client->setClient($guzzle);

        $response = m::mock('GuzzleHttp\Message\Response');

        $jobs = json_encode(['results' => [
            $this->createJobArray(),
            $this->createJobArray(),
            $this->createJobArray(),
        ]]);

        $guzzle->shouldReceive('get')
            ->with($query->getUrl(), [])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($jobs);

        $results = $client->getJobs();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(3, $results);
    }

    /**
     * Integration test with actual API call to the provider.
     */
    public function testItCanGetJobsFromApi()
    {
        if (!getenv('API_KEY')) {
            $this->markTestSkipped('API_KEY not set. Real API call will not be made.');
        }

        $query = new MuseQuery([
            'api_key' => getenv('API_KEY'),
        ]);

        $client = new MuseProvider($query);

        $results = $client->getJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);

        foreach($results as $job) {
            $this->assertNotNull($job->name);
        }
    }

    private function createJobArray() {
        return [
            'levels' =>
                array (
                    0 =>
                        array (
                            'short_name' => 'entry',
                            'name' => 'Entry Level',
                        ),
                ),
            'locations' =>
                array (
                    0 =>
                        array (
                            'name' => 'Sao Paolo, Brazil',
                        ),
                ),
            'tags' =>
                array (
                    0 =>
                        array (
                            'short_name' => 'fortune-1000-companies',
                            'name' => 'Fortune 1000',
                        ),
                ),
            'categories' =>
                [
                    ["name" => "Sales & Business Development"],
                ],
            'publication_date' => '2016-12-18T16:06:13.351729Z',
            'short_name' => 'litigation-counsel-lead-latam-f744ac',
            'refs' =>
                array (
                    'landing_page' => 'https://www.themuse.com/jobs/facebook/litigation-counsel-lead-latam-f744ac',
                ),
            'contents' => '<p>Description with HTML...</p>',
            'type' => 'external',
            'model_type' => 'jobs',
            'company' =>
                array (
                    'short_name' => 'facebook',
                    'name' => 'Facebook',
                    'id' => 659,
                ),
            'id' => 115888,
            'name' => 'Litigation Counsel Lead, LATAM',
        ];
    }
}
