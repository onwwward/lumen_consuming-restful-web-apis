<?php

namespace App\Jobs;

use Freshdesk;

class CreateTicketFromTask extends Job
{   
    /**
     * Task array
     *
     * @var array
     */
    protected $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            "description" => $this->task['description'],
            "subject" => $this->task['description'],
            "email" => $this->task['requester_email'],
            "priority" => 1,
            "status" => 2
        ];

        try {
            $ticket = Freshdesk::tickets()->create($data);
            //LOG SUCCESS
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                //ADD THIS TO LOG
            } else {
              throw $e;
            }
        }
    }
}