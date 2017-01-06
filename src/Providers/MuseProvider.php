<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;
use JobBrander\Jobs\Client\Collection;

class MuseProvider extends AbstractProvider
{
    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $defaults = [
            "creation_date",
            "locations", // array
            "id",
            "state",
            "update_date",
            "level_displays", // array
            "company_name",
            "apply_link",
            "external_apply_link",
            "title",
            "company_f1_image",
            "full_description",
            "categories", // array
            "search_image",
            "type",
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['title'],
            'name' => $payload['title'],
            'description' => $payload['full_description'],
            'url' => 'https://www.themuse.com'.$payload['apply_link'],
            'sourceId' => $payload['id'],
            'location' => $payload['locations'],
        ]);

        $job->setCompany($payload['company_name'])
            ->setCompanyLogo($payload['company_f1_image'])
            ->setDatePostedAsString($payload['creation_date'])
            ->setOccupationalCategory($payload['categories'])
            ->setExperienceRequirements($payload['level_displays']);

        return $job;
    }

    /**
     * Get descending results
     *
     * @return string
     */
    public function getDescending()
    {
        if (isset($this->descending)) {
            return $this->descending;
        }
        return 'true';
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Create and get collection of jobs from given listings
     *
     * @param  array $listings
     *
     * @return Collection
     */
    protected function getJobsCollectionFromListings(array $listings = array())
    {
        $collection = new Collection;
        array_map(function ($item) use ($collection) {
            $jobs = $this->createJobArray($item);
            foreach ($jobs as $item) {
                $job = $this->createJobObject($item);
                $job->setSource($this->getSource());
                $collection->add($job);
            }
        }, $listings);
        return $collection;
    }

    public function createJobArray($item)
    {
        $jobs = [];
        if (isset($item['locations']) && count($item['locations']) > 1) {
            foreach ($item['locations'] as $location) {
                $item['location'] = $location;
                $jobs[] = $item;
            }
        } else {
            $item['location'] = $item['locations'][0];
            $jobs[] = $item;
        }
        return $jobs;
    }

    /**
     * Get category or categories
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_array($this->category)) {
        }
        return $this->category;
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'results';
    }

    /**
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        $query_params = [
            'descending' => 'getDescending',
            'page' => 'getPage',
            'job_category[]' => 'getCategory',
            'job_level[]' => 'getLevel',
            'job_location[]' => 'getLocation',
            'company[]' => 'getCompany',
        ];

        $query_string = [];

        array_walk($query_params, function ($value, $key) use (&$query_string) {
            $computed_value = $this->$value();
            if (!is_null($computed_value)) {
                $query_string[$key] = $computed_value;
            }
        });

        $query = http_build_query($query_string, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
    }

    /**
     * Get url
     *
     * @return  string
     */
    public function getUrl()
    {
        $query_string = $this->getQueryString();

        return 'https://api-v1.themuse.com/jobs?'.$query_string;
    }

    /**
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }
}
