<?php

namespace App\Console\Commands;

use Bugherd\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class RefreshProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the list of projects';

    /**
     * Vugherd client
     * 
     * @var Bugherd\Client
     */
    protected $bugherd;

    public function __construct(Client $bugherd)
    {
        parent::__construct();

        $this->bugherd = $bugherd;  
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bugherdProjects = $this->getBugherdProjects();

        try {
            $projects = json_decode(Storage::get('projects.json'), true);

            foreach ($projects as $id => $value) {
                $bugherdProjects[$id]['sync'] = $value['sync'];
            }   
        } catch (FileNotFoundException $e) {
            // do nothing for now            
        }

        Storage::put('projects.json', json_encode($bugherdProjects, JSON_PRETTY_PRINT));
    }

    /**
     * Get Bugherd projects
     * 
     * @return array
     */
    public function getBugherdProjects()
    {
        $projects = $this->bugherd->api('project')->listing(true, false);

        if (! is_array($projects)) {
            return [];
        }

        foreach ($projects as $id => &$value) {
            $value = [
                'name' => $value,
                'sync' => false
            ];
        }  

        return $projects;
    }
}