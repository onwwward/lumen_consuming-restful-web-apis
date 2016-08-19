<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CreateTicketFromTask;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Queue;

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

    if ($request->has('task')) {
      Queue::push(new CreateTicketFromTask($request->get('task')));

      $response = [
        'success' => [
          'message' => 'The task has been added to the queue.'
        ]
      ];

    }
    
    return response()->json($response);
  }

}