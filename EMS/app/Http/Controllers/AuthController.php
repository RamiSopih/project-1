<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Cart;
use App\Models\Catigory;
use App\Models\Event;
use App\Models\fav;
use App\Models\Place;
use App\Models\Select_place;
use App\Models\Select_type;
use App\Models\Type_of_event;
use App\Models\Type_place;
use App\Models\User_fav;
use App\Models\User_like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\Mail;
class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }
    public function register(Request $request)
    {
        try{
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'packet'=>'required|integer|min:100000'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'packet'=>$request->packet,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
        ]);
     }
     catch (\Exception $e) {
        // Handle general exceptions
        return response()->json([
            'error' => 'An error occurred during registration: ' . $e->getMessage()
        ], 500);
     }
    }

        /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }



///////////////view//////////////////



    public function view_places(){

        return Place::select('name','type','price','location','image','desc')->get();
    }

    public function view_catigory(){
        return Catigory::select('item_name','item_type','price','image')->get();

    }

    public function show_favorite()
    {
        $user = auth()->user();
        $show = $user->catigory()->get();
        return response()->json([$show],200);

    }

    public function show_type_place()
    {
        $select = Place::select('type')->distinct()->get();
        return response()->json(['message'=>$select]);
    }
    public function show_finall()
    {
        $user = auth()->user();
        $get = Cart::where('user_id',$user->id)->get();
        $total = Cart::where('user_id',$user->id)->sum('prices');
        $place =Select_place::where('user_id',$user->id)->first();
        $int = (int)$total;
        $bill = new Bill();
        $bill->user_id = $user->id;
        $bill->select_place_id = $place->id;
        $bill->final_price = $int+$place->price;
        $bill->save();
        if($user->packet<$bill->final_price)
        {
            return response()->json(['message'=>'you dont have money enough!']);
        }
        $user->packet -= $bill->final_price;
        $user->save();

        // $user->packet = $packet;
        // $user->save;
            return response()->json(['The catigory'=>$get,'Total'=>$bill->final_price,'Place_name'=>$place->name,'Place_price'=>$place->price]);

        // return response()->json([gettype($var)]);

    }
    




 /////////////////////////////////////// add   +   and other  ////////////////////////////


    public function addlikes(Request $request)
    {


     $user = auth()->user();
     $id = $request->input('id');
     $place = Place::find($request->id);

        if ($place) {
            $found = User_like::where('user_id',$user->id)->where('place_id',$id)->exists();
        if ($found) {
            return response()->json(['message' => 'You have already liked this place.'], 400);
        }
        $place->likes +=1;
        $place->save();

        $liked = new User_like();
        $liked->user_id = $user->id;
        $liked->place_id = $place->id;
        $liked->save();

            return response()->json(['message' => 'Place liked successfully.'], 200);
        }
    }

    public function add_place(Request $request)//admin
    {
        try{
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'=>'required|string|max:255',
            'location'=>'required|string',
            'type'=>'required|string|in:pool,caffe,resturant,hall',
            'desc'=>'required|string',
            'price'=>'required|integer',
            ]);
            if ($request->hasFile('image')) {
                $photoPath = $request->file('image')->store('places', 'public'); // Save the photo in the 'public/photos' directory
              }
              $found = Place::where('name',$request->name)->where('type',$request->type)->exists();
              if($found)
              {
                return response()->json(['message'=>'the places is already here!']);
              }
              $place=Place::create([
                'image'=>$photoPath,
                'name'=>$request->name,
                'location'=>$request->location,
                'type'=>$request->type,
                'desc'=>$request->desc,
                'price'=>$request->price
             ]);
             return response()->json([
                'status' => 'success',
                'message' => 'place added',
                'place'=>$place
             ],201);
            }catch (\Exception $e) {
                return response()->json([
                    'error' => 'An error occurred during registration: ' . $e->getMessage()
                ], 500);
             }
    }


    public function add_catigory(Request $request)//admin
    {

        try{
     $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'item_name'=>'required|string|max:255',
        'item_type'=>'required|string|in:music,food,dj,dico,team,photo',
        'price'=>'required|integer',
        ]);
     if ($request->hasFile('image')) {
        $photoPath = $request->file('image')->store('cati', 'public'); // Save the photo in the 'public/photos' directory
      }

     $cati=Catigory::create([
        'image'=>$photoPath,
        'item_name'=>$request->item_name,
        'item_type'=>$request->item_type,
        'price'=>$request->price
     ]);
     return response()->json([
        'status' => 'success',
        'message' => 'catigory added',
        'catigory'=>$cati
     ],201);
     }
     catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred during registration: ' . $e->getMessage()
        ], 500);
     }

    }



    public function addFavorite(Request $request)
    {
        $user = auth()->user();
        $var1 = $request->input('id');
        if(!$var1){
            return response()->json(['error' => 'not have value!'], 404);
        }
        $cat = Catigory::where('id' , 'like' ,  $var1 )->first();
        if (!$cat) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $found = User_fav::where('fav_name' , 'like' , $cat->item_name )->where('user_id',$user->id)->exists();
        if($found)
        {
            return response()->json(['error' => 'Category is alread here!'], 404);
        }

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $fav = new User_fav();
        $fav->fav_name =$cat->item_name;
        $fav->user_id= $user->id;
        $fav->catigory_id =$cat->id;
        $fav->save();
        return response()->json(['message' => 'Favorite added successfully'], 200);

    }

    public function add_cart(Request $request)//from catigory
    {
        try{
     $user = auth()->user();
     $var1 = $request->input('id');
     $cat = Catigory::where('id' , 'like' ,  $var1 )->first();
     if (!$cat) {
         return response()->json(['error' => 'Category not found'], 404);
     }

     $found = Cart::where('catigory_id' , 'like' , $var1 )->where('user_id',$user->id)->exists();
     if($found)
     {
         return response()->json(['error' => 'Category is alread here'], 404);
     }
     if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
        }
        $cart = new Cart();
        $cart->user_id= $user->id;
        $cart->catigory_id =$cat->id;
        $cart->prices = $cat->price;
        $cart->save();

        // $bill = new Bill();
        // $bill->catigory_id= $cat->id;
        // $bill->cart_id = $cart->id;
        // $bill->user_id = $user->id;
        // $bill->save();
        return response()->json(['message' => 'The Category added'], 200);
        }
     catch (\Exception $e) {
        // Handle general exceptions
        return response()->json([
            'error' => 'An error occurred during registration: ' . $e->getMessage()
        ], 500);}
    }

    public function select_place(Request $request)
    {
        $user = auth()->user();
        $place = $request->input('id');
        $selected = Place::where('id',$place)->first();
        $found = Select_place::where('user_id',$user->id)->exists();
        if($found)
        {
            return response()->json(['error' => 'You chose the place before!'], 404);
        }
        if($selected)
        {
            $p = new Select_place();
            $p->place_id = $selected->id;
            $p->user_id = $user->id;
            $p->price = $selected->price;
            $p->name = $selected->name;
            $p->save();
            return response()->json(['message' => 'You chose the place successfully'], 200);

        }


    }

//////////////////////////////////////////////////////////
    public function delete_from_cart(Request $request)
    {
        $a = $request->input('id');
        $cart =Cart::find($a);
        if(!$cart)
        {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $cart->delete();
        return response()->json(['message' => 'Item removed from cart'], 200);

    }

    public function search(Request $request)//only catigor
    {
        $query = $request->input('name');

        $search = Catigory::where('name_item', 'like', $query.'%')->get('name_item');

        // Return the search results to a view
        return response()->json([$search],200);
    }


    ////////////////////////////////////////////////////////////////////
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
//
        ]);
    }
}
