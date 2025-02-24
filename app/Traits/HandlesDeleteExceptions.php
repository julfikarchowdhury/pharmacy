<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesDeleteExceptions
{
    /**
     * Handle the delete operation with exception handling for cascade restrictions.
     *
     * @param  callable  $callback
     * @return JsonResponse
     */
    public function handleDelete(
        callable $callback,
        $recordName
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $callback();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $recordName . ' deleted successfully.',
            ]);
        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                Log::error(
                    'Cascade restriction error on ' . $recordName . ' delete: ' . $e->getMessage()
                );

                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this ' . $recordName . ' as it is associated with other records.'
                ], 400);
            }

            Log::error('Database error on ' . $recordName . ' delete: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete the ' . $recordName . ' due to a database error.'
            ], 500);
        } catch (Exception $e) {
            Log::error(
                'Unexpected error deleting ' . $recordName . ' record: ' . $e->getMessage()
            );

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the ' . $recordName
            ], 500);
        }
    }

}
