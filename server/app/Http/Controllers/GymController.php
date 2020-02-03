<?php


namespace App\Http\Controllers;

use App\Gym;
use App\GymSubscriptionType;
use App\Type;
use App\GroupSubscriptionType;
use App\Repositories\GymRepository;
use App\Http\Requests\GymRequest;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log;

class GymController extends Controller
{


    /**
     * @var gym
     */
    private $gym;


    /**
     * gymController constructor.
     * @param GymRepository $GymRepository
     */
    public function __construct(GymRepository $gymRepository)
    {
        $this->gym = $gymRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getType()
    {

        $types = Type::all();

        return $types;
    }

    public function index()
    {
        return $this->gym->all();

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // filter unwanted inputs from request
        $gym = $request->all(); 
        $gym['facilities'] =  json_encode($gym['facilities']) ;

        
        Log::info('------------ gym ------------');
     //   Log::info($gym);
    //    Log::info('------------ End gym ------------');

        $validator = Validator::make($gym, [
            'group_id' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'rate' => 'required',
            'qrcode' => 'required',
            'class_id' => 'required',
            'facilities' => 'required',
            'covers' => 'required',
            'summary' => 'required',
            'planning' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048'

        ], GymRequest::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }


        Log::info('request->hasFile(file)');
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $qrcode = $request->get('qrcode');
            // $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/logo/');
            $image->move($destinationPath, 'gymLogo_' . $qrcode . '.' . $image->getClientOriginalExtension());
            $gym['logo'] = 'gymLogo_' . $qrcode . '.' . $image->getClientOriginalExtension();
        }
        
        Log::info('facilitieAttach');
        $facilitieAttach = $gym['facilities'];

        Log::info('gym_facilitie = gym->insert(gym)');
        Log::info($gym);
        $gym_facilitie = $this->gym->insert($gym);

        if ($facilitieAttach){
            Log::info('facilitieAttach');
            $facilitieAttach = json_decode($facilitieAttach);
            Log::info($facilitieAttach);
            $gym_facilitie->facilities()->attach($facilitieAttach);
/*
            foreach ($facilitieAttach as $facilitieAttach_ ) {
                $gym_facilitie->facilities()->attach($facilitieAttach_);
                Log::info('facilitieAttach_');
                Log::info($facilitieAttach_);
            }
*/

        //todo insert gym_subscription_type i should rename this table to gym_subsscription_type to specify the price of passes for each gym

        
        if ($passe_with_price = $request->get('passes')) {
            Log::info('passe_with_price');
            
        Log::info('------------ add gym passe_with_price part ------------');
      //  Log::info('passe_with_price');
      //  Log::info($passe_with_price);
      //  Log::info('request->get(passes)');
      //  Log::info($request->get('passes'));

            foreach ($passe_with_price as $passe_price_id => $priceAttach) {
                Log::info('------------foreach passe_with_price------------');
                if ($priceAttach['prix']) {
                    Log::info($priceAttach['passid'], ['typeid' => $priceAttach['typeid'], 'price' => $priceAttach['prix']]);

                    $gymsubscription = GymSubscriptionType::create(['gym_id' => $gym_facilitie->id, 'subscription_id' => $priceAttach['passid'], 'type_id' => $priceAttach['typeid'], 'price' => $priceAttach['prix']]);
                    $groupsubscription = GroupSubscriptionType::create(['group_id' =>$request->get('group_id'), 'subscription_id' => $priceAttach['passid'], 'type_id' => $priceAttach['typeid'], 'price' => $priceAttach['prix']]);

                }

                //                    $class_pass->subscription()->attach($priceAttach['passid'], ['price' => $priceAttach['prix']]);
            }
        }

        //TODO add the possibility to select To option of stric and partout to add gym_subscription_type

        // return the id of the resource
                Log::info('------------return gym_facilitie->id------------');
        return ['gym_id' => $gym_facilitie->id];
    }
    Log::info('------------return gym->id------------');
    return ['gym_id' => $gym->id];
} // store




    /**
     * Display the specified resource.
     *
     * @param \App\Gym $gym
     * @return \Illuminate\Http\Response
     */
    public function show($gym_id)
    {
        return $this->gym->find($gym_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Gym $gym
     * @return \Illuminate\Http\Response
     */
    public function edit(Gym $gym)
    {
        //
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gym  $gym
     * @return \Illuminate\HttpResponse
     */
    public function update(Request $request, $gym_id)
    {
        // check if the the requested resource exist in database
        $gym = $this->gym->find($gym_id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'group_id' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'rate' => 'required',
            'qrcode' => 'required',
            'class_id' => 'required',
            'facilities' => 'required',
            'covers' => 'required',
            'summary' => 'required',
            'planning' => 'required',

        ], GymRequest::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $this->gym->update($gym_id, $data);

        return ['gym_id' => $gym_id];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Gym $gym
     * @return \Illuminate\Http\Response
     */
    public function destroy($gym_id)
    {
        $this->gym->destroy($gym_id);
        return ['status' => 'success', 'deleted_resource_id' => $gym_id];
    }


}



