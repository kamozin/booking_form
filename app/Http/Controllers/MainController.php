<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;


use Ixudra\Curl\Facades\Curl;


class MainController extends Controller
{
    public function booking(Request $request){


        $curlService = new \Ixudra\Curl\CurlService();
        $url = 'https://ko.tour-shop.ru/siteLead';
        $response = $curlService->to($url)
            ->withHeader('KoSiteKey: test198')
//			$response = Curl::to('http://www.foo.com/bar')
            ->withData(array(
                'site_id' => '100',
                'type' => 'order',
                'data'=>$request->data
            ))
            ->post();

        $count=strpos($response, '=');
      if($count==false){
        $result=[];
        $result['success']=0;
        $result['message']=$response;
      }else{
          $result=[];
          $result['success']=1;
          $result['message']=substr($response, $count+1);
      }
        return response()->json($result);
    }
}
