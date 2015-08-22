<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Muse extends AbstractProvider
{
    /**
     * Category
     *
     * @var string
     */
    public $category;

    /**
     * Company
     *
     * @var string
     */
    public $company;

    /**
     * Level
     *
     * @var string
     */
    public $level;

    /**
     * Location
     *
     * @var string
     */
    public $location;

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
            "description",
            "creation_date",
            "company_mini_f1_image",
            "locations", // array
            "id",
            "state",
            "update_date",
            "level_displays", // array
            "company_name",
            "company_small_logo_image",
            "company_small_f1_image",
            "apply_link",
            "external_apply_link",
            "title",
            "company_f1_post_tile_image",
            "company_f1_image",
            "full_description",
            "model_type",
            "categories", // array
            "search_image",
            "type",
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['title'],
            'name' => $payload['title'],
            'url' => 'https://www.themuse.com'.$payload['apply_link'],
        ]);

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

        return http_build_query($query_string);
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
