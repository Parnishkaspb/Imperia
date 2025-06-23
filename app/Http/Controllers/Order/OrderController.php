<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Manufacture;
use App\Models\Order;
use App\Models\OrderManufacture;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        if (Auth::user()->role_id === 4){
            $orders = Order::with(['status'])->where('user_id', Auth::user()->id);
        } else {
            $orders = Order::with(['status']);
        }

        $orders = $orders->orderBy('id', 'DESC')
            ->paginate(30);

        return view('order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['status', 'orderProducts.product.category', 'orderManufacture']);
        $statuses = OrderStatus::all();

        $uniqueCategories = collect();

        if ($order && $order->orderProducts) {
            $uniqueCategories = $order->orderProducts
                ->filter(fn($op) => $op->product && $op->product->category)
                ->pluck('product.category')
                ->unique('id');
        }

        $productsData = $order->orderProducts?->map(function ($orderProduct) {
            $product = $orderProduct->product;

            if (!$product) return null;

            return [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'id' => $product->id,
                'quantity' => $orderProduct->quantity,
                'buying_price' => $orderProduct->buying_price,
                'selling_price' => $orderProduct->selling_price,
                'weight' => $product->weight,
                'length' => $product->length,
                'width' => $product->width,
                'height' => $product->height,
                'concrete_volume' => $product->concrete_volume,

            ];
        })->filter()->values() ?? [];

        $products = $productsData->groupBy('category_id');

        $orderManufacture = OrderManufacture::where('order_id', $order->id)->get();

        $manufactures = array_unique(array_column($orderManufacture->toArray(), 'manufacture_id'));
        $manufactures = Manufacture::with(['emails'])->whereIn('id', $manufactures)->get();
        $manufactures = $manufactures->keyBy('id')->map(function ($manufacture) {
            return [
                'name' => $manufacture->name,
                'emails' => $manufacture->emails->pluck('email')->toArray(),
            ];
        })->toArray();

        $tmp = [
            "th" => [],
            "body" => []
        ];

        foreach ($orderManufacture as $manufacture) {
            if (!isset($tmp["th"][$manufacture->manufacture_id])) {
                $tmp["th"][$manufacture->category_id][$manufacture->manufacture_id] = [ ...$manufactures[$manufacture->manufacture_id], "delete_id" => $manufacture->id];
            }

            $tmp["body"][$manufacture->category_id][$manufacture->product_id][$manufacture->manufacture_id] = [
                "comment" => $manufacture->comment,
                "price" => $manufacture->price,
            ];
        }

        $manufactures = $tmp;
        unset($tmp);


        return view('order.show', compact('order', 'statuses', 'uniqueCategories', 'products', 'manufactures'));
    }

    public function delete(Request $request, $orderId, $what, $value)
    {

        switch ($what) {
            case 16:
                $orderProduct = OrderProduct::where('order_id', $orderId)
                    ->where('product_id', (int) $value)
                    ->firstOrFail();

                $order = Order::find($orderId);

                $this->authorize('deleteProduct', [$order, $orderProduct]);

                $orderProduct->delete();

                $string = Auth::user()->name . "удалил продукт(".$value.") в заказе" . $order->id;
                Log::info($string);

                $order->touch();
                break;

            case 18:
                $orderManufacture = OrderManufacture::find($value);
                $productsId = OrderManufacture::select('product_id')->where('order_id', $orderId)->where('manufacture_id', $orderManufacture->manufacture_id)->get()->toArray();

                $categoryId = Product::where('id', $orderManufacture->product_id)->value('category_id');

                $needProductIds = Product::whereIn('id', $productsId)
                    ->where('category_id', $categoryId)
                    ->pluck('id')
                    ->toArray();

                OrderManufacture::whereIn('product_id', $needProductIds)->where('manufacture_id', $orderManufacture->manufacture_id)->where('order_id', $orderId)->delete();
                break;

        }

        return redirect()->back();
    }

    public function update(Request $request, Order $order){
        switch ($request->what){
            case 2:
                OrderProduct::where('order_id', $order->id)->where('product_id', (int) $request->product_id)->update(['quantity' => (int) $request->value]);
                $order->touch();
                break;

            case 3:
                OrderProduct::where('order_id', $order->id)->where('product_id', (int) $request->product_id)->update(['buying_price' => (int) $request->value]);
                $order->touch();
                break;

            case 4:
                OrderProduct::where('order_id', $order->id)->where('product_id', (int) $request->product_id)->update(['selling_price' => (int) $request->value]);
                $order->touch();
                break;

            case 9:
                $order->status_id = (int) $request->value;
                $order->save();
                break;

            case 17:
                $order->amo_lead = (int) $request->value;
                $order->save();
                break;

            case 19:
                OrderManufacture::where('order_id', (int) $order->id)->where('product_id', (int) $request->product_id)->where('manufacture_id', (int) $request->update_id)->update([
                   "price" => (int) $request->value,
                ]);
                $order->touch();
                break;

            case 20:
                OrderManufacture::where('order_id', (int) $order->id)->where('product_id', (int) $request->product_id)->where('manufacture_id', (int) $request->update_id)->update([
                    "comment" => (string) $request->value,
                ]);
                $order->touch();
                break;
        }
    }

    public function workWithProductToRedis(Request $request)
    {
        $userId = Auth::user()->id;
        $id = $request->product_id;
        $name = $request->product_name;
        $order = $request->order ?? "newOrder";
        $category = $request->product_category;
        $isChecked = filter_var($request->isChecked, FILTER_VALIDATE_BOOLEAN);
        $key = "{$userId}_order_{$order}";

        $cachedData = Cache::get($key, '[]');
        $items = json_decode($cachedData, true) ?? [];

        if ($isChecked) {
            $exists = collect($items)->contains('product_id', $id);
            if (!$exists) {
                $items[] = [
                    'product_id' => $id,
                    'product_name' => $name,
                    'product_category' => $category
                ];
            }
        } else {
            $items = array_filter($items, function ($item) use ($id) {
                return $item['product_id'] != $id;
            });
            $items = array_values($items);
        }

        if (empty($items)) {
            Cache::forget($key);
            return response()->json([]);
        }

        Cache::put($key, json_encode($items));

        return response()->json($items);
    }

    public function workWithProductToRedisGet(Request $request)
    {
        $userId = Auth::user()->id;
        $order = $request->order ?? "newOrder";
        $key = "{$userId}_order_{$order}";

        $cachedData = Cache::get($key, '[]');
        $items = json_decode($cachedData, true) ?? [];

        if (empty($items)) {
            return response()->json([]);
        }

        $answer = "";
        foreach ($items as $item) {
            $answer .= '<tr>
            <td>
            ' . $item['product_id'] . '
            </td>
            <td>
            ' . $item['product_name'] . '
            </td>
            <td>
                <input type="checkbox" name="scales" class="orderRedis" onclick="getSelectedCheckboxArray(this, '.$item['product_id'].', \''.$item['product_name'].'\', '.$item['product_category'].', '.($order === "newOrder" ? 'null' : '\''.$order.'\'').')" checked>
            </td>';
        }

        return $answer;
    }

    public function store(Request $request){
        $userId = Auth::user()->id;
        $order = $request->order ?? "newOrder";
        $key = "{$userId}_order_{$order}";

        $cachedData = Cache::get($key, '[]');
        $items = json_decode($cachedData, true) ?? [];

        if (empty($items)) {
            return response()->json(['message' => "Вы не выбрали никаких данных"]);
        }

        $productsId  = array_column($items, 'product_id');

        if ($order === "newOrder") {
            DB::beginTransaction();
            try {
                $order_id = Order::create([
                    'status_id' => 0,
                    'user_id'   => $userId,
                ])->id;

                $insert = $this->createArrayToInsert($productsId, $order_id);

                OrderProduct::insertOrIgnore($insert);
                Log::info($userId . " создал новый заказ с №" . $order_id);
                DB::commit();

                Cache::forget($key);
                return response()->json(['message' => 'Успешно создан новый заказ!', 'order_id' => $order_id]);
            } catch (\Exception $e ) {
                DB::rollBack();
                Log::error($userId . " пытался создать новый заказ. Ошибка: " . $e->getMessage());

                return response()->json(['message' => 'К сожалению, возникли трудности', 'error' => $e->getMessage()]);
            }
        } else {
            $productsCategoryId = array_unique(array_column($items, 'product_category'));

            $categoryByProductId = array_column($items, 'product_category', 'product_id');

            DB::beginTransaction();
            try {
                $order = (int) $order;

                $manufacturesId = OrderManufacture::select('manufacture_id')->where('order_id', $order)->where('category_id', $productsCategoryId)->get()->toArray();

                $insert = [];
                foreach ($manufacturesId as $manufacture_id){
                    foreach ($productsId as $productId) {
                        $insert[] = ['product_id' => $productId, 'manufacture_id' => (int) $manufacture_id['manufacture_id'], 'order_id' => $order, 'category_id' => $categoryByProductId[$productId]];
                    }
                }

                OrderManufacture::insertOrIgnore($insert);

                $insert = $this->createArrayToInsert($productsId, $order);

                OrderProduct::insertOrIgnore($insert);
                Log::info($userId . " добавил данные к заказу №" . $order);
                DB::commit();

                Cache::forget($key);
                return response()->json(['message' => 'Данные успешно добавлены к заказу!', 'order_id' => $order]);
            } catch (\Exception $e ) {
                DB::rollBack();
                Log::error($userId . " пытался добавить данные к заказу ". $order .". Ошибка: " . $e->getMessage());

                return response()->json(['message' => 'К сожалению, возникли трудности', 'error' => $e->getMessage()]);
            }
        }
    }

    private function createArrayToInsert(array $productsId, int $order_id): array {
        $insert = [];
        $insertTemplate = [
            'order_id'   => $order_id,
            'quantity'   => 1,
        ];

        foreach ($productsId as $productId) {
            $insert[] = $insertTemplate + ['product_id' => $productId];
        }

        return $insert;
    }

    public function addManufacture(Request $request, Order $order){
        $manufacture_id = (int) $request->manufacture_id;
        $category_id    = (int) $request->category_id;

        $productsId = OrderManufacture::where('order_id', $order->id)
            ->where('category_id', $category_id)
            ->distinct()
            ->pluck('product_id');

        if (count($productsId) === 0) {
            $productsId = OrderProduct::whereHas('product', function($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
                ->where('order_id', $order->id)
                ->distinct()
                ->pluck('product_id');
        }

        DB::beginTransaction();
        try {
            $insert = [];
            foreach ($productsId as $productId) {
                $insert[] = [
                    "product_id" => $productId,
                    "manufacture_id" => $manufacture_id,
                    "category_id" => $category_id,
                    "order_id" => $order->id,
                ];
            }

            if (empty($insert)) {
                DB::rollBack();
                throw new \Exception("вставка не может быть пустой!");
            }

            DB::table('order_manufactures')->insertOrIgnore($insert);
            $order->touch();
            DB::commit();
            return response()->json(["message" => "OK", "order_id" => $order->id]);

        } catch (\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
