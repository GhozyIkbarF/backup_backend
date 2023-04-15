<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Models\Design;
use App\Models\ModelOrder;
use App\Models\BuktiBayar;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Carbon\Carbon;


class OrderController extends Controller
{
    public function showAllOrderMasuk()
    {
        $orders = ModelsOrder::with(['designs'])
            ->where('status', 'masuk')
            ->select('id', 'name', 'phone', 'email', 'description')
            ->get();

        // loop through each order
        foreach ($orders as $order) {
            
            // loop through each design and set the URL
            foreach ($order->designs as $design) {
                $design->design = asset('storage/' . $design->design);
            }

            // loop through each model and set the URL
            foreach ($order->modelOrders as $model) {
                $model->model = asset('storage/' . $model->model);
            }
        }

        return response()->json($orders);
    }

    public function showAllOrderProses()
    {
        $orders = ModelsOrder::with(['designs:id,order_id,design'])
            ->where('status', 'proses')
            ->select('id', 'name', 'phone', 'email', 'deadline', 'progres')
            ->get();

        // loop through each order
        foreach ($orders as $order) {

            $order->deadline = Carbon::parse($order->deadline)->format('d/m/Y');

            // loop through each design and set the URL
            foreach ($order->designs as $design) {
                $design->design = asset('storage/' . $design->design);
            }

            // loop through each model and set the URL
            foreach ($order->modelOrders as $model) {
                $model->model = asset('storage/' . $model->model);
            }
        }

        return response()->json($orders);
    }

    public function showAllOrderFinish()
    {
        $orders = ModelsOrder::with(['designs:id,order_id,design'])
            ->where('status', 'selesai')
            ->select('id', 'name', 'phone', 'email', 'description')
            ->get();

        // loop through each order
        foreach ($orders as $order) {

            // loop through each design and set the URL
            foreach ($order->designs as $design) {
                $design->design = asset('storage/' . $design->design);
            }
        
        }

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = ModelsOrder::with(['designs', 'modelOrders', 'buktiBayars'])->findOrFail($id);


        // loop through each design and set the URL
        foreach ($order->designs as $design) {
            $design->design = asset('storage/' . $design->design);
        }

        // loop through each model and set the URL
        foreach ($order->modelOrders as $model) {
            $model->model = asset('storage/' . $model->model);
        }

        // loop through each proof of payment and set the URL
        foreach ($order->buktiBayars as $buktiBayar) {
            $buktiBayar->buktiBayar = asset('storage/' . $buktiBayar->buktiBayar);
        }


        return response()->json($order);
    }

    public function getOrderCounts()
    {
        $pendingCount = ModelsOrder::where('status', 'masuk')->count();
        $prosesCount = ModelsOrder::where('status', 'proses')->count();
        $finishCount = ModelsOrder::where('status', 'finish')->count();
        $currentMonth = date('Y-m');
        $deadlineCount = ModelsOrder::where('deadline', 'like', $currentMonth . '%');


        return [
            'masuk' => $pendingCount,
            'proses' => $prosesCount,
            'finish' => $finishCount,
            'deadline' => $deadlineCount,
        ];
    }

    public function getOrderPerMonth(Request $request)
    {
        $date = ModelsOrder::whereYear('deadline', substr($request->date, 0, 4))
        ->whereMonth('deadline', substr($request->date, 5))
        ->get();


        return response()->json($date);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'pembayaran' => 'numeric'
        ]);


        $order = ModelsOrder::create($request->all());

        if ($order) {
            return response()->json([
                'message' => 'Employee created successfully',
                'employee' => $request->name
            ], 201);
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50',
            'phone' => 'required|numeric|min:11',
            'address' => 'required',
        ]);

        $order = ModelsOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($request->all());

        return response()->json([
            'message' => 'Order updated successfully',
        ], 200);
    }
    
    public function updateProgres(Request $request, $id)
    {
        $request->validate([
            'progres' => 'required',
        ]);

        $order = ModelsOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($request->all());

        return response()->json([
            'message' => 'Uppdate progres order successfully',
        ], 200);
    }

    

    public function destroy($id)
    {
        $order = ModelsOrder::findOrfail($id);
        $designs = Design::where('order_id', $id)->get();
        $models = ModelOrder::where('order_id', $id)->get();
        $buktiBayars = BuktiBayar::where('order_id', $id)->get();

        if ($order) {
            foreach ($designs as $design) {
                Storage::delete('public/' . $design->design);
            }
            foreach ($models as $model) {
                Storage::delete('public/' . $model->model);
            }
            foreach ($buktiBayars as $buktiBayar) {
                Storage::delete('public/' . $buktiBayar->buktiBayar);
            }

            $order->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }


    public function sendMessage(Request $request)
    {

        $request->validate([
            'accountId' => 'required',
            'auth_token' => 'required',
            'whatsapp_number' => 'required',
            'customer_phone' => 'required',

            // 'photo' => 'image|mimes:jpg,png,jpeg,gif,svg',
        ]);
        $account_sid = $request->accoutId;
        $auth_token = $request->auth_token;
        $twilio_whatsapp_number = 'whatsapp:' . $request->whatsapp_number;
        $customer_phone = $request->customer_phone;

        $client = new Client($account_sid, $auth_token);

        $message = $client->messages->create(
            'whatsapp:' . $customer_phone, // recipient phone number
            [
                'from' => $twilio_whatsapp_number,
                'body' => 'Hello from Twilio!'
            ]
        );

        if (!$message) {
            return response()->json(['message' => 'Send message is fault'], 404);
        }

        return response()->json([
            'message' => 'Send Message successfully',
            'data' => $request
        ], 200);
    }
}
