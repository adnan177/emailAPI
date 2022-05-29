<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WebsiteDetail;

class APIController extends Controller
{
    private function api_response($data=NULL,$total_record=0,$status=200)
    {
        $res = ["status" => $status ,"total_records" => $total_record , "data" => $data];
        return response()->json($res);
    }



    public function create_post(Request $request)
    {
        if(empty($request->website_id) || empty($request->post_title) || empty($request->post_detail))
        {
            return $this->api_response(["message"=>"Parameters Validation Errors"],1,400);  
        }

        $post = new Post();
        $post->post_title = $request->post_title;
        $post->post_detail = $request->post_detail;
        $post->website_id = $request->website_id;
        $post->created_at = date('Y-m-d H:i:s');
        $post->save();
        $post->id();

        $website_name = WebsiteDetail::find($request->website_id)->value('website_name');
        $website_subscribers = WebsiteDetail::select('user_id')->where('website_id',$request->website_id)->get();
        $email_data = array('website_name'=>$website_name, "post_title",$request->post_title);

        
        dispatch(new App\Jobs\SendEmailSubscribeJob($website_subscribers,$website_name,$email_data));

        return $this->api_response(["message"=>"Post Created Successfully"],1,200);

    }

    public function make_subscription(Request $request)
    {
        if(empty($request->website_id) || empty($request->user_id))
        {
            return $this->api_response(["message"=>"Parameters Validation Errors"],1,400);  
        }

        $row_count =  Subscription::where(['website_id'=>$request->website_id,'user_id'=> $request->user_id])->count();
        if($row_count > 0)
        {
            return $this->api_response(["message"=>"This User Already Subscribed to this Website"],1,400);  
        }
        $sub = new Subscription();
        $sub->website_id = $request->website_id;
        $sub->user_id = $request->user_id;
        $sub->status = '1';
        $sub->created_at = date('Y-m-d H:i:s');
        $sub->save();
        return $this->api_response(["message"=>"Subscription Done Successfully"],1,200);

    }
}
