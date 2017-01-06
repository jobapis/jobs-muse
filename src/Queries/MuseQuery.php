<?php namespace JobApis\Jobs\Client\Queries;

class MuseQuery extends AbstractQuery
{
    /**
     * Page number (defaults to "1")
     *
     * @var integer
     */
    protected $page;

    /**
     * API Key (rate limited to 500 reqs/hour without this)
     *
     * @var string
     */
    protected $api_key;

    /**
     * Descending order (defaults to false)
     *
     * @var boolean
     */
    protected $descending;

    /**
     * Job Category. Must be one of the following:
     *
     * - Account Management
     * = Business & Strategy
     * - Creative & Design
     * - Customer Service
     * - Data Science
     * - Editorial
     * - Education
     * - Engineering
     * - Finance
     * - Fundraising & Development
     * - Healthcare & Medicine
     * - HR & Recruiting
     * - Legal
     * - Marketing & PR
     * - Operations
     * - Part Time
     * - Project & Product Management
     * - receptionist
     * - Retail Sales
     * - Social Media & Community
     *
     * @var string
     */
    protected $category;

    /**
     * Company
     *
     * @var string
     */
    protected $company;

    /**
     * Job Level
     *
     * @var string
     */
    protected $level;

    /**
     * Job Location
     *
     * @var string
     */
    protected $location;

    /**
     * Get baseUrl
     *
     * @return  string Value of the base url to this api
     */
    public function getBaseUrl()
    {
        return 'https://api-v2.themuse.com/jobs';
    }

    /**
     * Get keyword
     *
     * @return  string Attribute being used as the search keyword
     */
    public function getKeyword()
    {
        return $this->category;
    }

    /**
     * Default parameters
     *
     * @var array
     */
    protected function defaultAttributes()
    {
        return [
            'page' => 1,
        ];
    }
}
