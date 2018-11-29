<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
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
      return response()->json(['success'=> ''], $this-> successStatus);
      
        //checks for lat and long
        // $validator = Validator::make($request->all(), [
        //     'latitude' => 'required',
        //     'longitude' => 'required',
        // ]);
        // 
        // $user = Auth::user();
        // $user_with_reviews = User::with(['reviews' => function($query) {
        //   $query->orderBy('created_at', 'desc');
        // }])->find($user->id);
        // 
        // $user_with_reviews->user_id = $user->id;
        // $user_with_reviews_trimmed = $user_with_reviews->only(['user_id','reviews'])->all();
        // 

        // 
        // $coordinates->latitude = $request->latitude;
        // $coordinates->longitude = $request->longitude;
        // 
        // $user_with_reviews->coordinates = $coordinantes;
                
        // $user_with_reviews->user_id = $user_id;
        // $coordianates = "
        //   'coordinates': {
        //      'latitude': $request->latitude,
        //      'longitude': $request->longitude
        //    }";
        // 
        //    json_$coordinates
        // $user_with_reviews->coordinates = 
        // return response()->json(['success'=>$user_with_reviews], $this-> successStatus);
        
        // 
        // $user_with_reviews->user_id = $user->id;
        // revu
        // $user_with_reviews_trimmed = $user_with_reviews->only(['user_id','reviews'])->all();
        // $user_with_reviews_trimmed->push("
        //   "coordinates": {
        //      "latitude": $request->latitude,
        //      "longitude": $request->longitude
        //    }"
        // );
        // 
        // // $user_with_reviews_trimmed->push("
        //   "coordinates": {
        //      "latitude": $request->latitude,
        //      "longitude": $request->longitude
        //    }"
        // );
        
        // return $user_with_reviews_trimmed->to_json();
        // 
        
        // $client = new \GuzzleHttp\Client([
        //       'base_uri' => env("LIVY_PATH"),
        // ]);
        // 
        // // make call to livy
        // try {
        //   $response = $client->request('POST', '/batches',
        //     [
        //       'json' => [ 
        //         'file' => 's3://coen424-data/src/als.py',
        //         'args' => [
        //           '{
        //             "user_id": "1",
        //             "reviews": [
        //               {
        //                 "business_id": "Wpt0sFHcPtV5MO9He7yMKQ",
        //                 "stars": 5.0
        //               },
        //               {
        //                 "business_id": "WUiDaFQRZ8wKYGLvmjFjAw",
        //                 "stars": 1.0
        //               },
        //               {
        //                 "business_id": "akRtfcCezswizRIaAqJ4fQ",
        //                 "stars": 5.0
        //               },
        //               {
        //                 "business_id": "iPa__LOhse-hobC2Xmp-Kw",
        //                 "stars": 5.0
        //               }
        //             ],
        //             "coordinates": {
        //               "latitude": 37.80587,
        //               "longitude": -122.42058
        //             }
        //           }'
        //       ]
        //     ]
        //   );
        // 
        //     $response = $response->getBody()->getContents();
        //     return $response;
        // } catch (RequestException $e) {
        //     Log::error($e->getMessage());
        //     return $e->getResponse();
        // }
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
    
      Pusher::trigger('my-channel2', 'my-event',$restaurant_response);

      return response()->json(['success'=>$restaurants], $this-> successStatus);
    }
}
