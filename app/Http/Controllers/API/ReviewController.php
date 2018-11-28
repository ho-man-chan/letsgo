<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\review;

class ReviewController extends Controller
{
    public $successStatus = 200;
    
      /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function index()
      {
          //
      }

      /**
       * Show the form for creating a new resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function create()
      {
          //
      }

      /**
       * Store a newly created resource in storage.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return \Illuminate\Http\Response
       */
      public function store(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'business_id' => 'required',
              'stars' => 'required',
              // 'text' => 'required',          
          ]);
          
          //check if already view
          // reviewed_already = Review::where('user_id', Auth::user()-id)
          //         ->where('business_id', $request->business_id)->get();
          
          if ($validator->fails()) {
              return response()->json(['error'=>$validator->errors()], 401);
          }
          
          $review = new Review;
          $review->business_id = $request->business_id;
          $review->user_id = Auth::user()->id;
          $review->stars = $request->stars;
          $review->text = $request->text;
          $review->save();
        
          return response()->json(['success'=>$review], $this-> successStatus);
      }

      /**
       * Display the specified resource.
       *
       * @param  \App\review  $review
       * @return \Illuminate\Http\Response
       */
      public function show(review $review)
      {
          //
      }

      /**
       * Show the form for editing the specified resource.
       *
       * @param  \App\review  $review
       * @return \Illuminate\Http\Response
       */
      public function edit(review $review)
      {
          //
      }

      /**
       * Update the specified resource in storage.
       *
       * @param  \Illuminate\Http\Request  $request
       * @param  \App\review  $review
       * @return \Illuminate\Http\Response
       */
      public function update(Request $request, review $review)
      {
          //
      }

      /**
       * Remove the specified resource from storage.
       *
       * @param  \App\review  $review
       * @return \Illuminate\Http\Response
       */
      public function destroy(review $review)
      {
          //
      }
    
}
