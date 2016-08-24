<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CreateTicketFromTask;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{ 
  /**
   * Add task to the queue.
   * 
   * @param  Request $request 
   * @return json
   */
  public function storeTask(Request $request)
  {        
    $response = [
      'error' => [
        'message' => 'The request does not contain the required values.'
      ]
    ];

    $projects = json_decode(Storage::get('projects.json'), true);

    if ($request->has('task')) {
      $task = $request->get('task');
    }

    if ( isset($task) 
         && isset($projects[ $task['project_id'] ])
         && $projects[ $task['project_id'] ]['sync'] == true
      ) {
      
      $task['subject'] = sprintf('%s - %s...',
        $projects[ $task['project_id'] ]['name'],
        substr($task['description'], 0, 15)
      );

      Queue::push(new CreateTicketFromTask($task));

      $response = [
        'success' => [
          'message' => 'The task has been added to the queue.'
        ]
      ];

    }
    
    return response()->json($response);
  }

}