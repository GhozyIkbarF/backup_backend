<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Models\Design;
use App\Models\ModelOrder;
use App\Models\BuktiBayar;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Lang;
use DateTimeZone;


class OrderController extends Controller
{
    public function showAllOrderMasuk()
    {
        $orders = ModelsOrder::with(['designs:id,order_id,design'])
            ->where('status', 'masuk')
            ->select('id', 'name', 'phone', 'email', 'description')->orderBy('created_at', 'DESC')
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

    public function showAllOrderProses()
    {
        $orders = ModelsOrder::with(['designs:id,order_id,design'])
            ->where('status', 'proses')
            ->select('id', 'name', 'phone', 'email', 'deadline', 'quantity', 'progres')->orderBy('created_at', 'DESC')
            ->get();

        // loop through each order
        foreach ($orders as $order) {
            if($order->deadline !== null) {
                $order->deadline = Carbon::parse($order->deadline)->format('d/m/Y');
            }
            foreach ($order->designs as $design) {
                $design->design = asset('storage/' . $design->design);
            }
        }

        return response()->json($orders);
    }

    public function showAllOrderFinish()
    {
        $orders = ModelsOrder::with(['designs:id,order_id,design'])
            ->where('status', 'selesai')
            ->select('id', 'name', 'phone','description', 'endDate')->orderBy('endDate', 'DESC')
            ->get();

        // loop through each order
        foreach ($orders as $order) {
            if($order->endDate !== null) {

                $order->endDate = Carbon::parse($order->endDate)->format('d/m/Y');
            }
            foreach ($order->designs as $design) {
                $design->design = asset('storage/' . $design->design);
            }
        }

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = ModelsOrder::with(['designs', 'modelOrders', 'buktiBayars', 'company.bank_accounts'])->findOrFail($id);

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
        $finishCount = ModelsOrder::where('status', 'selesai')->count();
        $pendapatanTotal = ModelsOrder::where('status', 'selesai')->sum('payment');
        // $employeesCount = ModelsEmployes::get()->count();

        $ordersByMonth = [];

        // loop through each month of the year
        for ($month = 1; $month <= 12; $month++) {
            // get the year and month string in yyyy-mm format
            $yearMonth = date('Y-m', mktime(0, 0, 0, $month, 1));

            // filter the orders by the year and month
            $orders = ModelsOrder::whereYear('endDate', substr($yearMonth, 0, 4))
                ->whereMonth('endDate', substr($yearMonth, 5, 2))
                ->where('status', 'selesai')
                ->get();

            // calculate the total amount of the filtered orders
            $totalAmount = $orders->count();

            $monthName = DateTime::createFromFormat('!m', $month)->format('F');

            // add the total amount to the result array
            $ordersByMonth[] = [
                'label' => $monthName,
                'total_amount' => $totalAmount,
            ];
        }

        return [
            'masuk' => $pendingCount,
            'proses' => $prosesCount,
            'selesai' => $finishCount,
            'orderByMonth' => $ordersByMonth,
            'pendapatanTotal' => $pendapatanTotal,
        ];
    }

    public function getNumbersOrderPerYear($param)
    {
        $ordersByMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            // get the year and month string in yyyy-mm format
            $yearMonth = date('Y-m', mktime(0, 0, 0, $month, 1));

            // filter the orders by the year and month
            $orders = ModelsOrder::whereYear('endDate', $param)
                ->whereMonth('endDate', substr($yearMonth, 5, 2))
                ->where('status', 'selesai')
                ->get();

            // calculate the total amount of the filtered orders
            $totalAmount = $orders->count();

            // get the name of the month
            $monthName = DateTime::createFromFormat('!m', $month)->format('F');
            

            // add the total amount and month name to the result array
            $ordersByMonth[] = [
                'label' => $monthName,
                'total_amount' => $totalAmount,
            ];
        }

        return [
            'orderByMonth' => $ordersByMonth,
            'year' => substr($yearMonth, 0, 4),
        ];
    }

    public function getOrdersPerDay(Request $request)
    {
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        $ordersPerDay = ModelsOrder::selectRaw('DATE(endDate) AS label, COUNT(*) AS total_amount')
            ->whereBetween('endDate', [$request->startDate, $request->endDate])
            ->where('status', 'selesai')
            ->groupBy('label')->orderBy('label', 'asc')
            ->get();

            foreach ($ordersPerDay as $order) {
                $order->label = Carbon::createFromFormat('Y-m-d', $order->label)->format('d-m-Y');
            }

        return $ordersPerDay;
    }

    // report order permonth
    public function getOrderPerMonth(Request $request)
    {
        $date = $request->date;
        $orderPerMonth = ModelsOrder::whereYear('endDate', substr($date, 0, 4))
            ->whereMonth('endDate', substr($date, 5))
            ->where('status', 'selesai')
            ->select('name', 'phone', 'endDate', 'quantity', 'pricePerItem', 'payment', 'description')
            ->get();
            
            $company = Company::findOrFail(1);

            return response()->json([
                'orderPerMonth' => $orderPerMonth,
                'company' => $company
            ]);
    }

    public function getOrderReportPerDay($startDate, $endDate)
    {
        $ordersPerDay = ModelsOrder::whereBetween('endDate', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->select('name', 'phone', 'endDate', 'quantity', 'pricePerItem', 'payment', 'description')
            ->orderBy('endDate', 'asc')
            ->get();

        $pendapatan = $ordersPerDay->sum('payment');

            foreach ($ordersPerDay as $order) {
                $order->endDate = Carbon::createFromFormat('Y-m-d', $order->endDate)->format('d-m-Y');
            }

        return $ordersPerDay;
        // return [
        //     'masuk' => $pendingCount,
        //     'proses' => $prosesCount,
        //     'selesai' => $finishCount,
        //     'orderByMonth' => $ordersByMonth,
        // ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required|numeric|min:11',
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
            'email' => 'email',
            'phone' => 'required',
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
            'message' => 'Update progres order successfully',
        ], 200);
    }

    public function updateShippingCost(Request $request, $id)
    {
        $request->validate([
            'shippingCost' => 'required',
        ]);

        $order = ModelsOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($request->all());

        return response()->json([
            'message' => 'Update shipping cost successfully',
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
}
