<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use GuzzleHttp\Exception\RequestException;

class RecommendController extends Controller
{
    public $successStatus = 200;
    
    /**
     * recommend api
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //checks for lat and long
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
      
        $client = new \GuzzleHttp\Client([
              'base_uri' => env("LIVY_PATH"),
        ]);
        
        //make call to livy
        try {
          
          $response = $client->request('POST', '/batches',
            [
              'json' => [ 
                'file' => 's3://coen424-data/src/predict.py',
                'args' => [
                  // (string)(Auth::user()->id)
                  (string)148,
                  (string)$request->latitude,
                  (string)$request->longitude]
              ]
            ]
          );
          
            $response = $response->getBody()->getContents();
            return $response;
        } catch (RequestException $e) {
            Log::error($e->getMessage());
            return $e->getResponse();
        }
    }
    
}
