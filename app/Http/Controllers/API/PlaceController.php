<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;


class PlaceController extends Controller
{
    public $successStatus = 200;

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request)
    {

//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email',
//            'password' => 'required',
//            'c_password' => 'required|same:password',
//        ]);

        $headers = array(
            'User-Agent' => 'browser/1.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Bearer " . env("YELP_TOKEN")
        );

        $query = [
            'term' => $request->term,
            'location'=>$request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'categories' => $request->categories,
            'limit' => $request->limit,
            'sort_by' => $request->sort_by,
            'price' => $request->price,
            'open_now' => $request->open_now,
            'open_at' => $request->open_at,
        ];


        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.yelp.com/',
        ]);

        try {
            $response = $client->request(
                'GET',
                '/v3/businesses/search',
                [
                    'headers'=> $headers,
                    'query' => $query
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
