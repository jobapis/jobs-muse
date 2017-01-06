<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class MuseProvider extends AbstractProvider
{
    /**
     * Returns the standardized job object
     *
     * @param array $payload Raw job payload from the API
     *
     * @return \JobApis\Jobs\Client\Job
     */
    public function createJobObject($payload = [])
    {
        $job = new Job([
            'title' => $payload['name'],
            'name' => $payload['name'],
            'description' => $payload['contents'],
            'url' => $payload['refs']['landing_page'],
            'sourceId' => $payload['id'],
        ]);

        // categories array
        $this->setCategories($job, $payload['categories']);

        // company array
        $this->setCompany($job, $payload['company']);

        // levels array
        $this->setLevels($job, $payload['levels']);

        // locations array
        $this->setLocation($job, $payload['locations']);

        return $job;
    }

    /**
     * Job response object default keys that should be set
     *
     * @return  string
     */
    public function getDefaultResponseFields()
    {
        return [
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
     * Sets the categories on the job using the categories array
     *
     * @param Job $job
     * @param array $categories
     *
     * @return MuseProvider
     */
    protected function setCategories(Job $job, $categories = [])
    {
        $occupationalCats = [];
        foreach ($categories as $category) {
            $occupationalCats[] = $category['name'];
        }
        if ($occupationalCats) {
            $job->setOccupationalCategory(implode(', ', $occupationalCats));
        }
        return $this;
    }

    /**
     * Sets the company on the job using the company array
     *
     * @param Job $job
     * @param array $company
     *
     * @return MuseProvider
     */
    protected function setCompany(Job $job, $company = [])
    {
        if ($company && isset($company['name'])) {
            $job->setCompany($company['name']);
        }
        return $this;
    }

    /**
     * Sets the experience levels on the job using the levels array
     *
     * @param Job $job
     * @param array $levels
     *
     * @return MuseProvider
     */
    protected function setLevels(Job $job, $levels = [])
    {
        $requirements = [];
        foreach ($levels as $level) {
            $requirements[] = $level['name'];
        }
        if ($requirements) {
            $job->setExperienceRequirements(implode(', ', $requirements));
        }
        return $this;
    }

    /**
     * Sets the location on the job using the first location in the array
     *
     * @param Job $job
     * @param array $locations
     *
     * @return MuseProvider
     */
    protected function setLocation(Job $job, $locations = [])
    {
        if (isset($locations[0]) && isset($locations[0]['name'])) {
            $job->setLocation($locations[0]['name']);
        }
        return $this;
    }
}
