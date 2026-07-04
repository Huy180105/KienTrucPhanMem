<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use App\Adapters\HoaDonRequestAdapter;
use App\Strategies\TinhHoaDonMacDinhStrategy;
use App\Strategies\TinhHoaDonTreHanStrategy;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    protected $service;
    protected $adapter;

    public function __construct(HoaDonRequestAdapter $adapter)
    {
        $this->service = DichVuFactory::make('hoadon');
        $this->adapter = $adapter;
    }

    public function index()
    {
        return response()->json($this->service->all());
    }

    public function show($id)
    {
        return response()->json($this->service->find($id));
    }

    public function store(Request $request)
    {
        $data = $this->adapter->toHoaDonData($request->all());

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        $request->validate([
            'maHopDong' => 'required|integer|exists:hop_dongs,maHopDong',
            'thang' => [
                'required',
                'integer',
                'between:1,12',
                function ($attribute, $value, $fail) use ($request, $currentYear, $currentMonth) {
                    $year = (int)$request->input('nam');
                    if ($year > $currentYear || ($year === $currentYear && (int)$value > $currentMonth)) {
                        $fail('Kỳ hóa đơn (tháng/năm) không được vượt quá tháng/năm hiện tại.');
                    }

                    // Check that billing period is within the contract period
                    $hopDong = \App\Models\HopDong::find($request->input('maHopDong'));
                    if ($hopDong) {
                        $startDate = \Carbon\Carbon::parse($hopDong->ngayBatDau);
                        $endDate = \Carbon\Carbon::parse($hopDong->ngayKetThuc);
                        
                        $startPeriod = $startDate->year * 12 + $startDate->month;
                        $endPeriod = $endDate->year * 12 + $endDate->month;
                        $billingPeriod = $year * 12 + (int)$value;
                        
                        if ($billingPeriod < $startPeriod || $billingPeriod > $endPeriod) {
                            $fail('Kỳ hóa đơn phải nằm trong thời hạn hiệu lực của hợp đồng (từ ' . $startDate->format('d/m/Y') . ' đến ' . $endDate->format('d/m/Y') . ').');
                            return;
                        }

                        // Check duplicate invoice for the same room in the same month/year
                        $roomId = $hopDong->maPhong;
                        $exists = \App\Models\HoaDon::where('thang', (int)$value)
                            ->where('nam', $year)
                            ->whereHas('hopDong', function ($q) use ($roomId) {
                                $q->where('maPhong', $roomId);
                            })
                            ->exists();
                            
                        if ($exists) {
                            $fail('Phòng này đã được lập hóa đơn cho kỳ tháng ' . $value . '/' . $year . ' rồi.');
                        }
                    }
                }
            ],
            'nam' => 'required|integer|min:2020',
            'ngayLap' => 'required|date',
            'tongTien' => 'required|numeric|min:0',
        ]);

        $invoice = $this->service->create($data);
        return response()->json($invoice, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'thang' => 'sometimes|required|integer|between:1,12',
            'nam' => 'sometimes|required|integer|min:2020',
            'ngayLap' => 'sometimes|required|date',
            'tongTien' => 'sometimes|required|numeric|min:0',
            'trangThai' => 'sometimes|required|string',
        ]);

        $invoice = $this->service->update($id, $validated);
        return response()->json($invoice);
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function search(Request $request)
    {
        $params = [
            'month' => $request->query('month'),
            'year' => $request->query('year'),
            'room_id' => $request->query('room_id'),
        ];
        return response()->json($this->service->search($params));
    }

    public function unpaid()
    {
        return response()->json($this->service->unpaid());
    }

    public function pay($id)
    {
        $invoice = $this->service->pay($id);
        return response()->json($invoice);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'maHopDong' => 'required|integer|exists:hop_dongs,maHopDong',
            'dienCu' => 'required|integer',
            'dienMoi' => 'required|integer|gte:dienCu',
            'nuocCu' => 'required|integer',
            'nuocMoi' => 'required|integer|gte:nuocCu',
            'strategy' => 'required|string',
        ]);

        $hopDongService = DichVuFactory::make('hopdong');
        $hopDong = $hopDongService->find($request->maHopDong);

        $strategyType = $request->input('strategy', 'default');
        if ($strategyType === 'late' || $strategyType === 'lateFee') {
            $strategy = new TinhHoaDonTreHanStrategy();
        } else {
            $strategy = new TinhHoaDonMacDinhStrategy();
        }

        $tongTien = $strategy->tinh($hopDong, [
            'dienCu' => $request->dienCu,
            'dienMoi' => $request->dienMoi,
            'nuocCu' => $request->nuocCu,
            'nuocMoi' => $request->nuocMoi,
        ]);

        return response()->json([
            'success' => true,
            'tongTien' => $tongTien,
            'strategy_applied' => $strategyType
        ]);
    }
}
