<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Review;
use Illuminate\Support\Facades\Auth;
use Validator;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Pusher\Laravel\Facades\Pusher;

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
        // $validator = Validator::make($request->all(), [
        //     'latitude' => 'required',
        //     'longitude' => 'required',
        // ]);
        // 
        // if ($validator->fails()) {
        //     return response()->json(['error'=>$validator->errors()], 401);
        // }
        
        $user = Auth::user();
        $reviews = Review::select('business_id','stars')->where('user_id', $user->id)->get();
        
        $data['user_id'] =(string) $user->id;
        $data['reviews'] = $reviews;
        $user_reviews = json_encode($data);
        // $user_reviews_slashes = addslashes($user_reviews);
        
        Log::info($user_reviews);
        // Log::info($user_reviews_slashes);
        
        $client = new \GuzzleHttp\Client([
              'base_uri' => env("LIVY_PATH"),
        ]);
        
        // make call to livy
        try {
            $response = $client->request('POST', '/batches',
              [
                'json' => [ 
                  'file' => 's3://coen424-data/src/als.py',
                  'args' => ["$user_reviews"]
                ]
              ]
            );
            
            $response = $response->getBody()->getContents();
            Log::info($response);
            return response()->json(['success'=> $response], $this-> successStatus);
        } catch (RequestException $e) {
            Log::error($e->getMessage());
            return $e->getResponse();
            
        }
        return response()->json(['error'=> ''], $this-> successStatus);
    }
    
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',       
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        
        $restaurants = collect();
        
        //Convert request body to collection
        $restaurant_ids = collect(json_decode($request->getContent(),true));
                
        //get business details for all ids
        $restaurant_ids->each(function ($item, $key) use ($restaurants){
        
          //call yelp to get the content of the business by id
          $headers = array(
              'User-Agent' => 'browser/1.0',
              'Accept' => 'application/json',
              'Authorization' => "Bearer " . env("YELP_TOKEN")
          );

          $client = new \GuzzleHttp\Client([
              'base_uri' => 'https://api.yelp.com/',
          ]);

          try {
              $response = $client->request(
                  'GET',
                  "/v3/businesses/$key",
                  [
                      'headers'=> $headers
                  ]
              );
              $response = $response->getBody()->getContents();
              $restaurant = collect(json_decode($response, true));
              $filtered_restaurant_element = $restaurant->only(['id','name','image_url','rating','location','coordinates','price'])->all();
              
              //add response to restaurants
              $restaurants->push($filtered_restaurant_element);
          } catch (RequestException $e) {
              Log::error($e->getMessage());
              return $e->getResponse();
          }

      });
  
      $restaurants_array = $restaurants->toArray();
      $restaurant_response = array('success'=>$restaurants_array);
    
      Pusher::trigger('my-channel', 'my-event',$restaurant_response);

      return response()->json(['success'=>$restaurants], $this-> successStatus);
    }
}
