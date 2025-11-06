<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace App\Modules\Donation\Http\Controllers;

use App\Modules\Donation\Http\Requests\CreateDonationRequest;
use App\Modules\Donation\Http\Requests\ExportDonationsRequest;
use App\Modules\Donation\Http\Requests\UpdateDonationRequest;
use App\Modules\Donation\Http\Resources\DonationResource;
use App\Modules\Donation\Models\Donation;
use App\Modules\Donation\Services\DonationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DonationController {
    public function __construct(
        private readonly DonationService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return DonationResource::collection(Donation::all());
    }

    /**
     * @throws Throwable
     */
    public function create(CreateDonationRequest $request): JsonResponse {
        $donation = $this->service->create($request->validated());

        return (new DonationResource($donation))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Donation $donation): DonationResource {
        return new DonationResource($donation);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateDonationRequest $request, Donation $donation): DonationResource {
        $donation = $this->service->update($donation, $request->validated());

        return new DonationResource($donation);
    }

    public function destroy(Donation $donation): JsonResponse {
        try {
            $this->service->delete($donation);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete donation',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Export donations for accounting
     *
     * @param ExportDonationsRequest $request
     * @return JsonResponse|StreamedResponse
     */
    public function export(ExportDonationsRequest $request): JsonResponse|StreamedResponse {
        $result = $this->service->exportForAccounting($request->validated());
        $format = $request->validated()['format'];

        return match ($format) {
            'csv' => $this->exportAsCsv($result['data'], $result['summary']),
            'excel' => $this->exportAsExcel($result['data'], $result['summary']),
            default => response()->json($result),
        };
    }

    /**
     * Export as CSV
     *
     * @param array $data
     * @param array $summary
     * @return StreamedResponse
     */
    private function exportAsCsv(array $data, array $summary): StreamedResponse {
        $filename = 'donations_export_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return Response::stream(function () use ($data, $summary) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add summary at top
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Donations', $summary['total_donations']]);
            fputcsv($handle, ['Total Amount', $summary['total_amount']]);
            fputcsv($handle, ['Completed', $summary['completed_count']]);
            fputcsv($handle, ['Pending', $summary['pending_count']]);
            fputcsv($handle, ['Failed', $summary['failed_count']]);
            fputcsv($handle, []); // Empty line

            // Add headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));

                // Add data rows
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export as Excel (simple CSV with .xlsx extension)
     * For real Excel support, use maatwebsite/excel package
     *
     * @param array $data
     * @param array $summary
     * @return StreamedResponse
     */
    private function exportAsExcel(array $data, array $summary): StreamedResponse {
        $filename = 'donations_export_'.date('Y-m-d_His').'.xlsx';

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // For now, return CSV with xlsx extension
        // TODO: Integrate maatwebsite/excel for real Excel format
        return $this->exportAsCsv($data, $summary);
    }
}
